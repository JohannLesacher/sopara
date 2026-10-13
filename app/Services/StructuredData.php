<?php

namespace App\Services;

use WP_Post;

class StructuredData
{
    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    /**
     * Imprime le JSON-LD BlogPosting + Person (articles) ou Person (experts),
     * indépendant de Yoast / SASWP.
     */
    public function printBlogSchema(): void
    {
        if (is_singular('post')) {
            $graph = $this->blogPostingGraph(get_queried_object());
        } elseif (is_singular('expert')) {
            $graph = [$this->expertPerson(get_queried_object())];
        } else {
            return;
        }

        $this->printJsonLd([
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ]);
    }

    /**
     * Archives `type_contenu` / `secteur_activite` : Accueil > Blog > Taxonomie > Terme.
     */
    public function taxonomyBreadcrumbLinks(array $crumbs): array
    {
        if (! is_tax(['type_contenu', 'secteur_activite']) && ! is_category()) {
            return $crumbs;
        }

        $taxonomy = get_taxonomy(get_queried_object()->taxonomy);
        $termIndex = count($crumbs) - 1;
        $insert = [];

        foreach ($crumbs as $index => $crumb) {
            if (($crumb['taxonomy'] ?? null) === $taxonomy->name) {
                $termIndex = $index;
                break;
            }
        }

        $blogId = (int) get_option('page_for_posts');
        $blogUrl = $blogId ? get_permalink($blogId) : '';

        if ($blogUrl && ! in_array($blogUrl, array_column($crumbs, 'url'), true)) {
            $insert[] = ['url' => $blogUrl, 'text' => get_the_title($blogId)];
        }

        $insert[] = ['url' => '', 'text' => pll__($taxonomy->labels->name), 'hide_in_schema' => true];

        array_splice($crumbs, $termIndex, 0, $insert);

        return $crumbs;
    }

    /**
     * Persiste l'attribut du toggle « Ajouter aux données structurées »
     * côté PHP (miroir de l'enregistrement JS dans resources/js/admin/accordion.js).
     */
    public function registerFaqAttribute(array $args, string $name): array
    {
        if ($name !== 'core/accordion') {
            return $args;
        }

        $args['attributes']['addToFaqSchema'] = [
            'type' => 'boolean',
            'default' => false,
        ];

        return $args;
    }

    /**
     * Imprime le JSON-LD FAQPage dans le <head> des pages singulières.
     */
    public function printFaqSchema(): void
    {
        if (! is_singular()) {
            return;
        }

        $post = get_queried_object();

        if (! $post instanceof WP_Post || ! has_blocks($post->post_content)) {
            return;
        }

        $entities = $this->collectFaqEntities(parse_blocks($post->post_content));

        if (empty($entities)) {
            return;
        }

        $this->printJsonLd([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ]);
    }

    private function printJsonLd(array $schema): void
    {
        printf(
            '<script type="application/ld+json">%s</script>'."\n",
            wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    private function blogPostingGraph(WP_Post $post): array
    {
        $experts = $this->blogPost->experts($post->ID);
        $url = get_permalink($post);

        $posting = array_filter([
            '@type' => 'BlogPosting',
            '@id' => $url.'#article',
            'headline' => $this->plainText(get_the_title($post)),
            'description' => $this->plainText(get_the_excerpt($post)),
            'image' => get_the_post_thumbnail_url($post, 'very-large'),
            'datePublished' => get_post_time('c', true, $post),
            'dateModified' => get_post_modified_time('c', true, $post),
            'inLanguage' => get_bloginfo('language'),
            'mainEntityOfPage' => $url,
            'author' => array_map(fn (WP_Post $expert) => ['@id' => $this->expertPersonId($expert)], $experts),
        ]);

        return [$posting, ...array_map(fn (WP_Post $expert) => $this->expertPerson($expert), $experts)];
    }

    private function expertPerson(WP_Post $expert): array
    {
        $linkedin = (string) get_post_meta($expert->ID, 'linkedin', true);

        return array_filter([
            '@type' => 'Person',
            '@id' => $this->expertPersonId($expert),
            'name' => $this->plainText(get_the_title($expert)),
            'url' => get_permalink($expert),
            'jobTitle' => (string) get_post_meta($expert->ID, 'fonction', true),
            'knowsAbout' => (string) get_post_meta($expert->ID, 'expertise', true),
            'image' => wp_get_attachment_image_url((int) get_post_meta($expert->ID, 'photo', true), 'medium'),
            'sameAs' => $linkedin !== '' ? [$linkedin] : [],
        ]);
    }

    private function expertPersonId(WP_Post $expert): string
    {
        return get_permalink($expert).'#person';
    }

    private function plainText(string $html): string
    {
        return $this->normalize(html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /**
     * Parcourt récursivement l'arbre de blocs et collecte les entités FAQ
     * des accordéons taggés.
     */
    private function collectFaqEntities(array $blocks): array
    {
        $entities = [];

        foreach ($blocks as $block) {
            if ($block['blockName'] === 'core/accordion' && ! empty($block['attrs']['addToFaqSchema'])) {
                foreach ($block['innerBlocks'] as $item) {
                    $entity = $this->extractFaqItem($item);

                    if ($entity !== null) {
                        $entities[] = $entity;
                    }
                }

                continue;
            }

            if (! empty($block['innerBlocks'])) {
                $entities = array_merge($entities, $this->collectFaqEntities($block['innerBlocks']));
            }
        }

        return $entities;
    }

    /**
     * Transforme un accordion-item en entité Question/Answer.
     */
    private function extractFaqItem(array $item): ?array
    {
        if ($item['blockName'] !== 'core/accordion-item') {
            return null;
        }

        $question = '';
        $answer = '';

        foreach ($item['innerBlocks'] as $child) {
            if ($child['blockName'] === 'core/accordion-heading') {
                $question = $this->cleanText($child['innerHTML']);
            }

            if ($child['blockName'] === 'core/accordion-panel') {
                $answer = $this->cleanAnswer($child['innerBlocks'], $child['innerHTML']);
            }
        }

        if ($question === '' || $answer === '') {
            return null;
        }

        return [
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $answer,
            ],
        ];
    }

    private function cleanText(string $html): string
    {
        $text = $this->normalize(wp_strip_all_tags($html));

        return preg_replace('/\s*\+\s*$/u', '', $text);
    }

    private function cleanAnswer(array $innerBlocks, string $fallback): string
    {
        $html = '';

        foreach ($innerBlocks as $child) {
            $html .= render_block($child);
        }

        if ($html === '') {
            $html = $fallback;
        }

        return $this->normalize(wp_strip_all_tags($html));
    }

    /**
     * Remplace les séparateurs Unicode (U+2028 / U+2029) que wp_json_encode
     * ré-échappe toujours, puis condense les espaces.
     */
    private function normalize(string $text): string
    {
        $text = preg_replace('/[\x{2028}\x{2029}]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}

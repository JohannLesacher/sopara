<?php

namespace App\Services;

use WP_Post;

class StructuredData
{
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

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];

        printf(
            '<script type="application/ld+json">%s</script>'."\n",
            wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
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

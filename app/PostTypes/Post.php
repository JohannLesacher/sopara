<?php

namespace App\PostTypes;

use App\Services\BlogPost;
use WP_Post;
use WP_Query;

class Post
{
    private const WORDS_PER_MINUTE = 200;

    private const POSTS_PER_PAGE = 9;

    private string $post_type = 'post';

    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    public function addMetas(array $meta_boxes): array
    {
        $meta_boxes[] = [
            'title' => 'Article',
            'post_types' => $this->post_type,
            'context' => 'side',
            'fields' => [
                [
                    'id' => 'experts',
                    'name' => 'Experts',
                    'type' => 'post',
                    'post_type' => 'expert',
                    'field_type' => 'select_advanced',
                    'multiple' => true,
                ],
                [
                    'id' => 'reading_time_override',
                    'name' => 'Temps de lecture (min)',
                    'type' => 'number',
                    'min' => 1,
                    'desc' => 'Laisser vide pour un calcul automatique.',
                ],
                [
                    'id' => 'related_posts',
                    'name' => 'Articles liés',
                    'type' => 'post',
                    'post_type' => 'post',
                    'field_type' => 'select_advanced',
                    'multiple' => true,
                    'js_options' => [
                        'maximumSelectionLength' => 3,
                    ],
                    'desc' => '3 maximum. Laisser vide pour une sélection automatique (même catégorie).',
                ],
            ],
        ];

        $meta_boxes[] = [
            'title' => 'Introduction',
            'taxonomies' => 'category',
            'fields' => [
                [
                    'id' => 'intro',
                    'name' => 'Introduction',
                    'type' => 'wysiwyg',
                    'options' => [
                        'media_buttons' => false,
                        'teeny' => true,
                        'textarea_rows' => 6,
                    ],
                ],
            ],
        ];

        $meta_boxes[] = [
            'title' => 'Bannière',
            'taxonomies' => ['category', 'type_contenu', 'secteur_activite'],
            'fields' => [
                [
                    'id' => 'banner_image',
                    'name' => 'Image de bannière',
                    'type' => 'single_image',
                    'desc' => 'Laisser vide pour utiliser l\'image par défaut (Réglages du thème).',
                ],
            ],
        ];

        return $meta_boxes;
    }

    public function storeReadingTime(int $postId, WP_Post $post): void
    {
        if (wp_is_post_revision($postId) || wp_is_post_autosave($postId)) {
            return;
        }

        $words = $this->countWords($post->post_content);

        update_post_meta($postId, '_reading_time', max(1, (int) ceil($words / self::WORDS_PER_MINUTE)));
    }

    public function adjustMainQuery(WP_Query $query): void
    {
        if (is_admin() || ! $query->is_main_query()) {
            return;
        }

        if ($query->is_home()) {
            $query->set('ignore_sticky_posts', true);
            $query->set('posts_per_page', self::POSTS_PER_PAGE);

            if ($featuredId = $this->blogPost->featuredPostId()) {
                $query->set('post__not_in', [$featuredId]);
            }

            return;
        }

        if ($query->is_category() || $query->is_tax(['type_contenu', 'secteur_activite'])) {
            $query->set('posts_per_page', self::POSTS_PER_PAGE);
        }
    }

    private function countWords(string $content): int
    {
        $text = trim(wp_strip_all_tags(strip_shortcodes($content)));

        if ($text === '') {
            return 0;
        }

        return count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
    }
}

<?php

namespace App\PostTypes;

use App\Services\BlogPost;
use WP_Query;

class Post
{
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
}

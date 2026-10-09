<?php

namespace App\View\Composers;

use App\Services\BlogPost;
use Roots\Acorn\View\Composer;
use WP_Post;
use WP_Term;

class SinglePost extends Composer
{
    private const BLOG_TAXONOMIES = ['type_contenu', 'secteur_activite'];

    protected static $views = [
        'partials.content-single-post',
    ];

    protected int $postId;

    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    public function with(): array
    {
        $this->postId = get_queried_object_id();

        return [
            'category' => $this->category(),
            'summary' => has_excerpt($this->postId) ? get_the_excerpt($this->postId) : '',
            'experts' => $this->experts(),
            'publishedIso' => get_the_date('c', $this->postId),
            'published' => get_the_date('', $this->postId),
            'isUpdated' => $this->isUpdated(),
            'modifiedIso' => get_the_modified_date('c', $this->postId),
            'modified' => get_the_modified_date('', $this->postId),
            'readingTime' => $this->blogPost->readingTime($this->postId),
            'content' => apply_filters('the_content', get_post_field('post_content', $this->postId)),
            'tags' => $this->tags(),
            'relatedPosts' => $this->blogPost->relatedPosts($this->postId),
        ];
    }

    protected function category(): ?array
    {
        $term = $this->blogPost->primaryCategory($this->postId);

        return $term ? ['name' => $term->name, 'url' => get_term_link($term)] : null;
    }

    /**
     * @return array<int, array{name: string, url: string, imageId: int}>
     */
    protected function experts(): array
    {
        return array_map(fn (WP_Post $expert) => [
            'name' => get_the_title($expert),
            'url' => get_permalink($expert),
            'imageId' => (int) get_post_thumbnail_id($expert),
        ], $this->blogPost->experts($this->postId));
    }

    protected function isUpdated(): bool
    {
        $post = get_post($this->postId);

        return strtotime($post->post_modified_gmt) - strtotime($post->post_date_gmt) > DAY_IN_SECONDS;
    }

    /**
     * @return array<int, array{name: string, url: string}>
     */
    protected function tags(): array
    {
        $terms = wp_get_post_terms($this->postId, self::BLOG_TAXONOMIES);

        if (is_wp_error($terms)) {
            return [];
        }

        return array_map(fn (WP_Term $term) => [
            'name' => $term->name,
            'url' => get_term_link($term),
        ], $terms);
    }
}

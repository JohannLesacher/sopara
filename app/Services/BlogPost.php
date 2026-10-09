<?php

namespace App\Services;

use WP_Post;
use WP_Term;

class BlogPost
{
    private ?int $featuredPostId = null;

    private bool $featuredResolved = false;

    public function readingTime(int $postId): int
    {
        $override = (int) get_post_meta($postId, 'reading_time_override', true);

        return $override > 0 ? $override : (int) get_post_meta($postId, '_reading_time', true);
    }

    /**
     * @return WP_Post[]
     */
    public function experts(int $postId): array
    {
        return $this->publishedPosts($this->translatedIds(get_post_meta($postId, 'experts')), 'expert');
    }

    public function primaryCategory(int $postId): ?WP_Term
    {
        $termId = function_exists('yoast_get_primary_term_id') ? (int) yoast_get_primary_term_id('category', $postId) : 0;

        if ($termId && ($term = get_term($termId, 'category')) instanceof WP_Term) {
            return $term;
        }

        $categories = get_the_category($postId);

        return $categories[0] ?? null;
    }

    public function featuredPostId(): ?int
    {
        if ($this->featuredResolved) {
            return $this->featuredPostId;
        }

        $this->featuredResolved = true;

        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
            'fields' => 'ids',
        ];

        $sticky = get_option('sticky_posts');
        $ids = $sticky ? get_posts([...$args, 'post__in' => $sticky]) : [];

        if (! $ids) {
            $ids = get_posts($args);
        }

        return $this->featuredPostId = $ids ? (int) $ids[0] : null;
    }

    /**
     * @return WP_Post[]
     */
    public function mainQueryPosts(): array
    {
        global $wp_query;

        return $wp_query->posts;
    }

    /**
     * @return WP_Post[]
     */
    public function relatedPosts(int $postId, int $count = 3): array
    {
        $related = array_slice(
            $this->publishedPosts($this->translatedIds(get_post_meta($postId, 'related_posts')), 'post'),
            0,
            $count
        );

        $missing = $count - count($related);
        $category = $this->primaryCategory($postId);

        if ($missing <= 0 || ! $category) {
            return $related;
        }

        $exclude = [$postId, ...array_map(fn (WP_Post $post) => $post->ID, $related)];

        return [...$related, ...get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $missing,
            'cat' => $category->term_id,
            'post__not_in' => $exclude,
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ])];
    }

    public function bannerImageId(): int
    {
        return $this->queriedBannerImageId() ?: (int) (get_option('theme_settings', [])['banner_default'] ?? 0);
    }

    public function preloadBannerImage(): void
    {
        if (! is_home() && ! is_category() && ! is_tax() && ! is_singular(['post', 'expert'])) {
            return;
        }

        $imageId = $this->bannerImageId();
        $url = $imageId ? wp_get_attachment_image_url($imageId, 'very-large') : '';

        if ($url) {
            echo '<link rel="preload" fetchpriority="high" as="image" href="'.esc_url($url).'" imagesrcset="'.esc_attr((string) wp_get_attachment_image_srcset($imageId, 'very-large')).'" imagesizes="100vw">';
        }
    }

    private function queriedBannerImageId(): int
    {
        if (is_home()) {
            return (int) get_post_thumbnail_id((int) get_option('page_for_posts'));
        }

        if (is_singular('post')) {
            return (int) get_post_thumbnail_id(get_queried_object_id());
        }

        $term = get_queried_object();

        if ($term instanceof WP_Term) {
            return (int) get_term_meta($term->term_id, 'banner_image', true);
        }

        return 0;
    }

    /**
     * @return int[]
     */
    private function translatedIds(array $ids): array
    {
        if (! function_exists('pll_get_post')) {
            return array_filter(array_map('intval', $ids));
        }

        $translated = [];

        foreach ($ids as $id) {
            if ($translatedId = pll_get_post((int) $id)) {
                $translated[] = $translatedId;
            }
        }

        return array_unique($translated);
    }

    /**
     * @return WP_Post[]
     */
    private function publishedPosts(array $ids, string $postType): array
    {
        if (! $ids) {
            return [];
        }

        return get_posts([
            'post_type' => $postType,
            'post_status' => 'publish',
            'post__in' => $ids,
            'orderby' => 'post__in',
            'posts_per_page' => count($ids),
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ]);
    }
}

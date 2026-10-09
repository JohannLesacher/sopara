<?php

namespace App\View\Composers\Blocks;

use App\Services\BlogPost;
use Roots\Acorn\View\Composer;
use WP_Post;

class PostListing extends Composer
{
    private const PREVIEW_COUNT = 6;

    protected static $views = [
        'blocks.post-listing',
    ];

    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    public function with(): array
    {
        $isBlogIndex = is_home() && empty($this->view->getData()['is_preview']);

        return [
            'featured' => $this->featured(),
            'posts' => $isBlogIndex ? $this->blogPost->mainQueryPosts() : $this->previewPosts(),
            'paginate' => $isBlogIndex,
        ];
    }

    protected function featured(): ?WP_Post
    {
        if (is_paged()) {
            return null;
        }

        $featuredId = $this->blogPost->featuredPostId();

        return $featuredId ? get_post($featuredId) : null;
    }

    /**
     * @return WP_Post[]
     */
    protected function previewPosts(): array
    {
        return get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => self::PREVIEW_COUNT,
            'post__not_in' => array_filter([$this->blogPost->featuredPostId()]),
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ]);
    }
}

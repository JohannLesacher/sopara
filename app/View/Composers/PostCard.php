<?php

namespace App\View\Composers;

use App\Services\BlogPost;
use Roots\Acorn\View\Composer;
use WP_Post;

class PostCard extends Composer
{
    protected static $views = [
        'partials.post-card',
    ];

    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    public function with(): array
    {
        $post = $this->view->getData()['post'];
        $isFeatured = ! empty($this->view->getData()['isFeatured']);

        return [
            'cardModifier' => $isFeatured ? 'post-card--featured' : '',
            'cardImageSize' => $isFeatured ? 'large' : 'medium_large',
            'cardHeadingTag' => $this->view->getData()['heading'] ?? 'h2',
            'cardPermalink' => get_permalink($post),
            'cardTitle' => get_the_title($post),
            'cardImageId' => (int) get_post_thumbnail_id($post),
            'cardCategory' => $this->blogPost->primaryCategory($post->ID)?->name ?? '',
            'cardDateIso' => get_the_date('c', $post),
            'cardDate' => get_the_date('', $post),
            'cardReadingTime' => $this->blogPost->readingTime($post->ID),
            'cardExperts' => implode(', ', array_map(
                fn (WP_Post $expert) => get_the_title($expert),
                $this->blogPost->experts($post->ID)
            )),
        ];
    }
}

<?php

namespace App\View\Composers;

use App\Services\BlogPost;
use Roots\Acorn\View\Composer;
use WP_Term;

class Archive extends Composer
{
    protected static $views = [
        'taxonomy',
    ];

    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    public function with(): array
    {
        return [
            'intro' => $this->intro(),
            'posts' => $this->blogPost->mainQueryPosts(),
        ];
    }

    protected function intro(): string
    {
        $term = get_queried_object();

        if (! $term instanceof WP_Term) {
            return '';
        }

        $intro = (string) get_term_meta($term->term_id, 'intro', true);

        return $intro !== '' ? wpautop($intro) : '';
    }
}

<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Home extends Composer
{
    protected static $views = [
        'home',
    ];

    public function with(): array
    {
        $pageId = (int) get_option('page_for_posts');

        return [
            'blogPageContent' => $pageId ? apply_filters('the_content', get_post_field('post_content', $pageId)) : '',
        ];
    }
}

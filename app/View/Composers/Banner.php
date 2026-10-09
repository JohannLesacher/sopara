<?php

namespace App\View\Composers;

use App\Services\BlogPost;
use Roots\Acorn\View\Composer;

class Banner extends Composer
{
    protected static $views = [
        'partials.banner',
    ];

    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    public function with(): array
    {
        return [
            'bannerTitle' => $this->bannerTitle(),
            'bannerImageId' => $this->blogPost->bannerImageId(),
        ];
    }

    protected function bannerTitle(): string
    {
        if (is_home()) {
            return get_the_title((int) get_option('page_for_posts'));
        }

        if (is_category() || is_tax()) {
            return single_term_title('', false);
        }

        return get_the_title(get_queried_object_id());
    }
}

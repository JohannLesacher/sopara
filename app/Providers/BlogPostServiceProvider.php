<?php

namespace App\Providers;

use App\Services\BlogPost;
use Illuminate\Support\ServiceProvider;

class BlogPostServiceProvider extends ServiceProvider
{
    /**
     * Register the service.
     */
    public function register(): void
    {
        $this->app->singleton(BlogPost::class);
    }

    /**
     * Bootstrap the service (initialise les hooks WordPress).
     */
    public function boot(BlogPost $blogPost): void
    {
        add_action('wp_head', [$blogPost, 'preloadBannerImage'], 1);
    }
}

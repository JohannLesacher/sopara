<?php

namespace App\Providers;

use App\Services\StructuredData;
use Illuminate\Support\ServiceProvider;

class StructuredDataServiceProvider extends ServiceProvider
{
    /**
     * Register the service.
     */
    public function register(): void
    {
        $this->app->singleton(StructuredData::class);
    }

    /**
     * Bootstrap the service (initialise les hooks WordPress).
     */
    public function boot(StructuredData $structuredData): void
    {
        add_filter('register_block_type_args', [$structuredData, 'registerFaqAttribute'], 10, 2);
        add_action('wp_head', [$structuredData, 'printFaqSchema']);
        add_action('wp_head', [$structuredData, 'printBlogSchema']);

        if (defined('WPSEO_VERSION')) {
            add_filter('wpseo_breadcrumb_links', [$structuredData, 'taxonomyBreadcrumbLinks']);
        }
    }
}

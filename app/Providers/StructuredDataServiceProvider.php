<?php

namespace App\Providers;

use App\Services\StructuredData;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class StructuredDataServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the service.
     */
    public function register(): void
    {
        $this->app->singleton(StructuredData::class, function () {
            return new StructuredData;
        });
    }

    /**
     * Bootstrap the service (initialise les hooks WordPress).
     */
    public function boot(StructuredData $structuredData): void
    {
        add_filter('register_block_type_args', [$structuredData, 'registerFaqAttribute'], 10, 2);
        add_action('wp_head', [$structuredData, 'printFaqSchema']);
    }
}

<?php

namespace App\Providers;

use App\Services\Megamenu;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class MegamenuServiceProvider extends LaravelServiceProvider {
    /**
     * Register the service.
     */
    public function register(): void {
        $this->app->singleton( Megamenu::class, function () {
            return new Megamenu();
        } );
    }

    /**
     * Bootstrap the service (initialise les hooks WordPress).
     */
    public function boot( Megamenu $megamenu ): void {
        add_action( 'wp_nav_menu_item_custom_fields', [ $megamenu, 'renderCustomFields' ], 10, 4 );
        add_action( 'wp_update_nav_menu_item', [ $megamenu, 'saveCustomFields' ], 10, 3 );
        add_filter( 'wp_setup_nav_menu_item', [ $megamenu, 'addCustomFieldsToMenuItem' ] );
        add_action('admin_enqueue_scripts', [$megamenu, 'enqueueAdminScripts']);
    }
}

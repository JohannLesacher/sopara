<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (function_exists('pll_register_string')) {
            pll_register_string('footer-adresse', 'Adresse :', 'theme');
            pll_register_string('footer-contact', 'Contact :', 'theme');

            // Blocs
            pll_register_string('etapes-num', 'Étape %d', 'theme');
            pll_register_string('hotspot-point', 'Point %d', 'theme');
            pll_register_string('hotspot-close', 'Fermer', 'theme');
            pll_register_string('slider-prev', 'Diapositive précédente', 'theme');
            pll_register_string('slider-next', 'Diapositive suivante', 'theme');

            // Header
            pll_register_string('header-menu', 'MENU', 'theme');
            pll_register_string('header-menu-aria', 'Menu', 'theme');
        }
    }
}

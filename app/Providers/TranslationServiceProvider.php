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

            // Blog
            pll_register_string('blog-reading-time', '%d min de lecture', 'theme');
            pll_register_string('blog-published', 'Publié le', 'theme');
            pll_register_string('blog-updated', 'Mis à jour le', 'theme');
            pll_register_string('blog-related', 'Articles liés', 'theme');
            pll_register_string('blog-pagination', 'Pagination', 'theme');
            pll_register_string('blog-prev', 'Précédent', 'theme');
            pll_register_string('blog-next', 'Suivant', 'theme');
            pll_register_string('blog-breadcrumb', 'Fil d\'Ariane', 'theme');
            pll_register_string('blog-type-contenu', 'Types de contenu', 'theme');
            pll_register_string('blog-secteur-activite', 'Secteurs d\'activité', 'theme');
            pll_register_string('expert-expertise', 'Expertise', 'theme');
            pll_register_string('expert-experience', 'Expérience', 'theme');
            pll_register_string('expert-years', '%d ans', 'theme');

            // Header
            pll_register_string('header-menu', 'MENU', 'theme');
            pll_register_string('header-menu-aria', 'Menu', 'theme');
        }
    }
}

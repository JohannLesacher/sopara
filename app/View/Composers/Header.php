<?php

namespace App\View\Composers;

use Log1x\Navi\Navi;
use Roots\Acorn\View\Composer;

class Header extends Composer {
    protected static $views = [ 'sections.header' ];

    public function with(): array {
        return [
            'navigation'    => $this->getNavigation(),
            'navigationCTA' => $this->getNavigationCTA(),
            'languages'     => $this->getLanguages(),
            'logo'          => get_custom_logo(),
            'is_fixed'      => true,
            'has_lang'      => true,
            'has_cta'       => true,
            'align'         => 'left',
        ];
    }

    private function getNavigation(): array {
        return Navi::make()->build( 'primary_navigation' )->isEmpty()
            ? []
            : Navi::make()->build( 'primary_navigation' )->all();
    }

    private function getLanguages(): array {
        return function_exists( 'pll_the_languages' )
            ? ( pll_the_languages( [ 'raw' => 1 ] ) ?: [] )
            : [];
    }

    private function getNavigationCTA(): array {
        return Navi::make()->build( 'cta_navigation' )->isEmpty()
            ? []
            : Navi::make()->build( 'cta_navigation' )->all();
    }
}

/**
 * Add "has-fixed-header" class to body when fixed option is active.
 */
add_filter( 'body_class', function ( array $classes ) {
    $classes[] = 'has-fixed-header';

    return $classes;
} );

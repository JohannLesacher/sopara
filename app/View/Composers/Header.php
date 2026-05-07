<?php

namespace App\View\Composers;

use App\Services\Megamenu;
use Log1x\Navi\Navi;
use Roots\Acorn\View\Composer;

class Header extends Composer {
    protected static $views = [ 'sections.header' ];
    private Megamenu $megamenu;

    public function __construct() {
        $this->megamenu = app( Megamenu::class );
    }

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
        $navi = Navi::make()->build( 'primary_navigation' );
        if ( $navi->isEmpty() ) {
            return [];
        }

        return array_map( fn( $item ) => $this->mapNavigationItem( $item ), $navi->all() );
    }

    private function mapNavigationItem( $item, int $depth = 0 ) {
        if ( isset( $item->id ) ) {
            if ( $depth === 0 ) {
                $item->megamenu_image_id = $this->megamenu->getImageId( $item );
            }
            if ( $depth >= 1 ) {
                $item->megamenu_style = $this->megamenu->getRadioFieldValue( $item );
                $item->megamenu_bouton = $this->megamenu->getTextFieldValue( $item );
            }
        }

        $item->children = is_array( $item->children ?? [] ) && ! empty( $item->children )
            ? array_values( array_map(
                fn( $child ) => $this->mapNavigationItem( $child, $depth + 1 ),
                $item->children
            ) )
            : [];

        return $item;
    }

    private function getNavigationCTA(): array {
        return Navi::make()->build( 'cta_navigation' )->isEmpty()
            ? []
            : Navi::make()->build( 'cta_navigation' )->all();
    }

    private function getLanguages(): array {
        return function_exists( 'pll_the_languages' )
            ? ( pll_the_languages( [ 'raw' => 1 ] ) ?: [] )
            : [];
    }
}

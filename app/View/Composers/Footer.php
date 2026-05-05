<?php

namespace App\View\Composers;

use Log1x\Navi\Facades\Navi;
use Roots\Acorn\View\Composer;

class Footer extends Composer {
    protected static $views = [ 'sections.footer' ];

    protected array $settings;

    protected string $lang;

    public function with(): array {
        $this->settings = get_option( 'theme_settings', [] );
        $this->lang     = function_exists( 'pll_current_language' ) ? pll_current_language() : 'fr';

        return [
            'nav'       => $this->nav(),
            'contact'   => $this->contact(),
            'branding'  => $this->branding(),
            'social'    => $this->social(),
            'copyright' => $this->copyright(),
        ];
    }

    protected function nav(): array {
        return [
            'left'  => $this->buildNav( 'footer_navigation_left' ),
            'right' => $this->buildNav( 'footer_navigation_right' ),
            'legal' => $this->buildNav( 'legal_footer_navigation' ),
        ];
    }

    protected function contact(): array {
        return [
            'address' => $this->t( 'footer_address' ),
            'phone'   => $this->t( 'footer_phone' ),
            'email'   => $this->t( 'footer_email' ),
        ];
    }

    protected function branding(): array {
        return [
            'slogan'  => $this->t( 'footer_slogan', 'YOUR THERMAL PARTNER FOR INDUSTRY' ),
        ];
    }

    protected function social(): array {
        return [
            'x'        => $this->settings['footer_social_x'] ?? '',
            'linkedin' => $this->settings['footer_social_linkedin'] ?? '',
            'youtube'  => $this->settings['footer_social_youtube'] ?? '',
        ];
    }

    protected function copyright(): string {
        return '© ' . date( 'Y' ) . ' ' . $this->t( 'footer_copyright_text', 'Sopara. All rights reserved.' );
    }

    protected function t( string $key, string $default = '' ): string {
        return $this->settings["{$key}_{$this->lang}"]
               ?? $this->settings["{$key}_fr"]
                  ?? $default;
    }

    protected function buildNav( string $location ): array {
        $nav = Navi::build( $location );

        return $nav->isEmpty() ? [] : $nav->all();
    }
}

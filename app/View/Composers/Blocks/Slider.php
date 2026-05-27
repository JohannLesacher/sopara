<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class Slider extends Composer {
    protected static $views = [
        'blocks.slider',
    ];

    private function attributes(): array {
        return $this->view->getData()['attributes'] ?? [];
    }

    public function dataAttributes(): array {
        $attributes = $this->attributes();

        $data = [
            'data-per-page' => $attributes['perPage'] ?? 3,
            'data-loop'     => ( $attributes['loop'] ?? false ) ? 'true' : 'false',
            'data-autoplay' => ( $attributes['autoplay'] ?? false ) ? 'true' : 'false',
            'data-arrows'   => ( $attributes['arrows'] ?? false ) ? 'true' : 'false',
        ];

        if ( isset( $attributes['perPageTablet'] ) ) {
            $data['data-per-page-tablet'] = $attributes['perPageTablet'];
        }

        if ( isset( $attributes['perPageMobile'] ) ) {
            $data['data-per-page-mobile'] = $attributes['perPageMobile'];
        }

        return $data;
    }

    public function classeNames(): array {
        $attributes = $this->attributes();

        $classes = [ 'block-slider', 'splide' ];

        if ( ! empty( $attributes['className'] ) ) {
            $classes[] = $attributes['className'];
        }

        if ( isset( $attributes['align'] ) ) {
            $classes[] = 'align' . $attributes['align'];
        }

        return $classes;
    }

    public function showArrows(): bool {
        $attributes = $this->attributes();

        return (bool) ($attributes['arrows'] ?? false);
    }
}

<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class Slide extends Composer {
    protected static $views = [
        'blocks.slide',
    ];

    private function attributes(): array {
        return $this->view->getData()['attributes'] ?? [];
    }

    public function animated(): bool {
        return $this->attributes()['animateOnScroll'] ?? false;
    }

    public function classeNames(): array {
        $classes = [ 'splide__slide', 'block-slide' ];
        $attributes = $this->attributes();

        if ( ! empty( $attributes['className'] ) ) {
            $classes[] = $attributes['className'];
        }

        if ( $this->animated() ) {
            $classes[] = 'is-animated is-animated--slide';
        }

        return $classes;
    }

    public function animationType(): string {
        return $this->attributes()['animationType'] ?? 'fade-up';
    }
}

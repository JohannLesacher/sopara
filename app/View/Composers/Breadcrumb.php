<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Breadcrumb extends Composer
{
    protected static $views = [
        'partials.breadcrumb',
    ];

    public function with(): array
    {
        return [
            'breadcrumb' => $this->breadcrumb(),
        ];
    }

    protected function breadcrumb(): string
    {
        if (! function_exists('yoast_breadcrumb')) {
            return '';
        }

        return (string) yoast_breadcrumb(
            '<nav class="breadcrumb" aria-label="'.esc_attr(pll__('Fil d\'Ariane')).'">',
            '</nav>',
            false
        );
    }
}

<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class KeyFigure extends Composer
{
    protected static $views = [
        'blocks.key-figure',
    ];

    public function with(): array
    {
        $data = $this->view->getData()['data'] ?? [];
        $attributes = $this->view->getData()['attributes'] ?? [];
        $customColor = sanitize_hex_color($attributes['customValueColor'] ?? '');
        $slug = sanitize_html_class($attributes['valueColor'] ?? '');

        return [
            'valeur' => $data['valeur'] ?? '',
            'unite' => $data['unite'] ?? '',
            'libelle' => $data['libelle'] ?? '',
            'valueColorStyle' => $customColor ? "color: {$customColor};" : '',
            'valueColorClass' => ! $customColor && $slug !== '' ? "has-text-color has-{$slug}-color" : '',
        ];
    }
}

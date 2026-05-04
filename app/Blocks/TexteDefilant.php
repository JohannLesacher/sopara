<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class TexteDefilant extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Texte défilant',
            'id' => 'texte-defilant',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'text',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('texte-defilant', Vite::asset('resources/css/blocks/texte-defilant.scss'), [], null);

                if (! is_admin()) {
                    wp_enqueue_script_module('texte-defilant', Vite::asset('resources/js/blocks/texte-defilant.js'), [], null);
                }
            },
            'supports' => [
                'align' => ['full', 'wide'],
                'anchor' => true,
                'customClassName' => true,
            ],
            'fields' => [
                [
                    'id' => 'texte',
                    'name' => 'Texte',
                    'type' => 'text',
                    'placeholder' => 'Texte défilant…',
                ],
                [
                    'id' => 'vitesse',
                    'name' => 'Vitesse',
                    'type' => 'select',
                    'options' => [
                        'lent' => 'Lent',
                        'normal' => 'Normal',
                        'rapide' => 'Rapide',
                    ],
                    'std' => 'normal',
                ],
                [
                    'id' => 'direction',
                    'name' => 'Direction',
                    'type' => 'select',
                    'options' => [
                        'gauche' => 'Gauche',
                        'droite' => 'Droite',
                    ],
                    'std' => 'gauche',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

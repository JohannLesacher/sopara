<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class Hero extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Hero',
            'id' => 'hero',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'admin-generic',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('hero', Vite::asset('resources/css/blocks/hero.scss'), [], null);
            },
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
            ],
            'fields' => [
                [
                    'id' => 'image_fond',
                    'name' => 'Image de fond',
                    'type' => 'single_image',
                ],
                [
                    'id' => 'image_fixe',
                    'name' => 'Image fixe',
                    'desc' => 'Affiche l\'image de fond en background-attachment: fixed',
                    'type' => 'switch',
                    'std' => 0,
                ],
                [
                    'id' => 'titre',
                    'name' => 'Grand titre',
                    'type' => 'textarea',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

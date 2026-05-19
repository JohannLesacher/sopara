<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class HoverCard extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'HoverCard',
            'id' => 'hover-card',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'id-alt',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('hover-card', Vite::asset('resources/css/blocks/hover-card.scss'), [], null);
            },
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
            ],
            'fields' => [
                [
                    'id' => 'image',
                    'name' => 'Image',
                    'type' => 'single_image',
                ],
                [
                    'id' => 'titre',
                    'name' => 'Titre',
                    'type' => 'text',
                ],
                [
                    'id' => 'texte',
                    'name' => 'Texte (révélé au survol)',
                    'type' => 'textarea',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

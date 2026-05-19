<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class HoverSectorImage extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'HoverSectorImage',
            'id' => 'hover-sector-image',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'cover-image',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('hover-sector-image', Vite::asset('resources/css/blocks/hover-sector-image.scss'), [], null);
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
                    'id' => 'tag',
                    'name' => 'Tag (texte du bouton)',
                    'type' => 'text',
                ],
                [
                    'id' => 'url',
                    'name' => 'Lien (optionnel)',
                    'type' => 'url',
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

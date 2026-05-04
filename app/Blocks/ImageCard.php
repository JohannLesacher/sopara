<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class ImageCard extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'ImageCard',
            'id' => 'image-card',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'format-image',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('image-card', Vite::asset('resources/css/blocks/image-card.scss'), [], null);
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
                    'id' => 'cta_label',
                    'name' => 'Texte du CTA',
                    'type' => 'text',
                ],
                [
                    'id' => 'cta_url',
                    'name' => 'Lien du CTA',
                    'type' => 'url',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

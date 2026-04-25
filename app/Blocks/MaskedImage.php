<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class MaskedImage extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        add_filter('register_block_type_args', function ($args, $block_type) {
            if ($block_type !== 'meta-box/masked-image') {
                return $args;
            }
            $args['attributes']['width'] = ['type' => 'string'];

            return $args;
        }, 10, 2);

        $meta_boxes[] = [
            'title' => 'Image masquée',
            'id' => 'masked-image',
            'type' => 'block',
            'category' => 'sur-mesure',
            'icon' => 'admin-generic',
            'context' => 'normal',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('masked-image', Vite::asset('resources/css/blocks/masked-image.scss'), [], null);
            },
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
            ],
            'fields' => [
                [
                    'id' => 'svg',
                    'name' => 'Masque SVG',
                    'type' => 'file_advanced',
                    'max_file_uploads' => 1,
                    'mime_type' => 'image/svg+xml',
                ],
                [
                    'id' => 'image',
                    'name' => 'Image',
                    'type' => 'single_image',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

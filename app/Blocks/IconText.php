<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class IconText extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Icône + Texte',
            'id' => 'icon-text',
            'type' => 'block',
            'category' => 'sur-mesure',
            'icon' => 'admin-generic',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('icon-text', Vite::asset('resources/css/blocks/icon-text.scss'), [], null);
            },
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
            ],
            'fields' => [
                [
                    'id' => 'icone',
                    'name' => 'Icône',
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1,
                ],
            ],
        ];

        return $meta_boxes;
    }
}

<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class ImageHotspots extends BlockEngine
{
    public static function register_block(array $meta_boxes): array
    {
        $meta_boxes[] = [
            'title'           => 'Image avec points interactifs',
            'id'              => 'image-hotspots',
            'type'            => 'block',
            'category'        => 'sur-mesure',
            'icon'            => 'location-alt',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets'  => function () {
                wp_enqueue_style('image-hotspots', Vite::asset('resources/css/blocks/image-hotspots.scss'), [], null);
                if (!is_admin()) {
                    wp_enqueue_script_module('image-hotspots-js', Vite::asset('resources/js/blocks/image-hotspots.js'), [], null);
                }
            },
            'supports' => [
                'align'           => true,
                'anchor'          => true,
                'customClassName' => true,
            ],
            'fields' => [
                [
                    'id'   => 'image',
                    'name' => 'Image de fond',
                    'type' => 'single_image',
                ],
                [
                    'id'     => 'points',
                    'name'   => 'Points',
                    'type'   => 'group',
                    'clone'  => true,
                    'sort_clone' => true,
                    'collapsible' => true,
                    'group_title' => 'Point : {title}',
                    'add_button'  => '+ Ajouter un point',
                    'fields' => [
                        [
                            'id'   => 'title',
                            'name' => 'Titre',
                            'type' => 'text',
                        ],
                        [
                            'id'   => 'text',
                            'name' => 'Texte',
                            'type' => 'textarea',
                            'rows' => 3,
                        ],
                        [
                            'id'   => 'cta_text',
                            'name' => 'CTA (Texte)',
                            'type' => 'text',
                        ],
                        [
                            'id'   => 'cta_url',
                            'name' => 'CTA (Lien)',
                            'type' => 'text',
                        ],
                        [
                            'id'   => 'pos_x',
                            'name' => 'Position X (%)',
                            'type' => 'number',
                            'min'  => 0,
                            'max'  => 100,
                            'step' => 0.1,
                            'std'  => 50,
                        ],
                        [
                            'id'   => 'pos_y',
                            'name' => 'Position Y (%)',
                            'type' => 'number',
                            'min'  => 0,
                            'max'  => 100,
                            'step' => 0.1,
                            'std'  => 50,
                        ],
                    ],
                ],
            ],
        ];

        return $meta_boxes;
    }
}

<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class EtapesCirculaires extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title'           => 'Étapes Circulaires',
            'id'              => 'etapes-circulaires',
            'type'            => 'block',
            'category'        => 'sur-mesure',
            'context'         => 'normal',
            'icon'            => 'image-rotate',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets'  => function () {
                wp_enqueue_style('etapes-circulaires', Vite::asset('resources/css/blocks/etapes-circulaires.scss'), [], null);

                if (! is_admin()) {
                    wp_enqueue_script_module('etapes-circulaires', Vite::asset('resources/js/blocks/etapes-circulaires.js'), [], null);
                }
            },
            'supports' => [
                'align'           => true,
                'anchor'          => true,
                'customClassName' => true,
                'reusable'        => true,
            ],
            'fields' => [
                [
                    'id'   => 'image',
                    'name' => 'Image centrale',
                    'type' => 'single_image',
                ],
                [
                    'id'         => 'etapes',
                    'name'       => 'Étapes',
                    'type'       => 'group',
                    'clone'      => true,
                    'sort_clone' => true,
                    'collapsible' => true,
                    'group_title' => 'Étape {#}',
                    'min_clone'  => 2,
                    'max_clone'  => 7,
                    'fields'     => [
                        [
                            'id'   => 'titre',
                            'name' => 'Titre',
                            'type' => 'text',
                        ],
                        [
                            'id'      => 'texte',
                            'name'    => 'Texte (affiché au survol / clic)',
                            'type'    => 'wysiwyg',
                            'options' => [
                                'media_buttons' => false,
                                'teeny'         => true,
                                'quicktags'     => false,
                                'textarea_rows' => 4,
                                'tinymce'       => [
                                    'toolbar1' => 'bold,italic,link,unlink,|,undo,redo',
                                    'toolbar2' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $meta_boxes;
    }
}

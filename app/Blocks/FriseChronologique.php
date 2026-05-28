<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class FriseChronologique extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Frise Chronologique',
            'id' => 'frise-chronologique',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'editor-ol',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('frise-chronologique', Vite::asset('resources/css/blocks/frise-chronologique.scss'), [], null);

                if (! is_admin()) {
                    wp_enqueue_script_module('frise-chronologique', Vite::asset('resources/js/blocks/frise-chronologique.js'), [], null);
                }
            },
            'supports' => [
                'align' => ['full'],
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
            ],
            'fields' => [
                [
                    'id' => 'type',
                    'name' => 'Type de frise',
                    'type' => 'select',
                    'options' => [
                        'scroll' => 'Scroll Horizontal',
                        'slider' => 'Slider',
                    ],
                    'std' => 'scroll',
                ],
                [
                    'id' => 'etapes',
                    'name' => 'Étapes',
                    'type' => 'group',
                    'clone' => true,
                    'sort_clone' => true,
                    'collapsible' => true,
                    'group_title' => '{date} — {titre}',
                    'min_clone' => 2,
                    'fields' => [
                        [
                            'id' => 'date',
                            'name' => 'Date',
                            'type' => 'text',
                        ],
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
                            'name' => 'Texte',
                            'type' => 'wysiwyg',
                            'options' => [
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => false,
                                'textarea_rows' => 4,
                                'tinymce' => [
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

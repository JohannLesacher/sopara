<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class Hero extends BlockEngine {
    public static function register_block( $meta_boxes ) {
        $meta_boxes[] = [
            'title'           => 'Hero',
            'id'              => 'hero',
            'type'            => 'block',
            'category'        => 'sur-mesure',
            'context'         => 'normal',
            'icon'            => 'admin-generic',
            'render_callback' => [ parent::class, 'renderBlock' ],
            'enqueue_assets'  => function () {
                wp_enqueue_style( 'hero', Vite::asset( 'resources/css/blocks/hero.scss' ), [], null );

                if ( ! is_admin() ) {
                    wp_enqueue_script_module( 'hero', Vite::asset( 'resources/js/blocks/hero.js' ), [], null );
                }
            },
            'supports'        => [
                'align'           => true,
                'anchor'          => true,
                'customClassName' => true,
                'reusable'        => true,
            ],
            'fields'          => [
                [
                    'id'   => 'image_fond',
                    'name' => 'Image de fond',
                    'type' => 'single_image',
                ],
                [
                    'id'   => 'titre',
                    'name' => 'Grand titre',
                    'type' => 'textarea',
                ],
                [
                    'id'   => 'titre_secteurs',
                    'name' => "Titre — Secteurs d'activité",
                    'type' => 'text',
                ],
                [
                    'id'               => 'images_secteurs',
                    'name'             => "Images — Secteurs d'activité",
                    'type'             => 'image_advanced',
                    'max_file_uploads' => 6,
                ],
            ],
        ];

        return $meta_boxes;
    }
}

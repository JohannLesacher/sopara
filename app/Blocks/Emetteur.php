<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class Emetteur extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Emetteur',
            'id' => 'emetteur',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'id',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('emetteur', Vite::asset('resources/css/blocks/emetteur.scss'), [], null);
            },
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
                'color' => [
                    'text' => true,
                    'background' => true,
                    'link' => true,
                ],
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
                    'id' => 'sous_titre',
                    'name' => 'Sous-titre',
                    'type' => 'text',
                ],
                [
                    'id' => 'texte',
                    'name' => 'Texte',
                    'type' => 'textarea',
                ],
                [
                    'id' => 'lien_url',
                    'name' => 'Lien (URL)',
                    'type' => 'text',
                ],
                [
                    'id' => 'lien_text',
                    'name' => 'Lien (Texte)',
                    'type' => 'text',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

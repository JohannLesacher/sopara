<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class AgentLocal extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'AgentLocal',
            'id' => 'agent-local',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'businessperson',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('agent-local', Vite::asset('resources/css/blocks/agent-local.scss'), [], null);
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
                    'id' => 'logo',
                    'name' => 'Logo',
                    'type' => 'single_image',
                ],
                [
                    'id' => 'title',
                    'name' => 'Nom',
                    'type' => 'text',
                ],
                [
                    'id' => 'website_url',
                    'name' => 'URL du site web',
                    'type' => 'url',
                ],
                [
                    'id' => 'location',
                    'name' => 'Localisation',
                    'type' => 'group',
                    'fields' => [
                        [
                            'id' => 'city',
                            'name' => 'Ville',
                            'type' => 'text',
                        ],
                        [
                            'id' => 'country',
                            'name' => 'Pays',
                            'type' => 'text',
                        ],
                    ],
                ],
                [
                    'id' => 'tags',
                    'name' => 'Tags',
                    'type' => 'text',
                    'clone' => true,
                    'sort_clone' => true,
                    'add_button' => '+ Ajouter un tag',
                ],
                [
                    'id' => 'contact',
                    'name' => 'Contact',
                    'type' => 'group',
                    'fields' => [
                        [
                            'id' => 'name',
                            'name' => 'Nom',
                            'type' => 'text',
                        ],
                        [
                            'id' => 'email',
                            'name' => 'Email',
                            'type' => 'email',
                        ],
                        [
                            'id' => 'phone',
                            'name' => 'Téléphone',
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
        ];

        return $meta_boxes;
    }
}

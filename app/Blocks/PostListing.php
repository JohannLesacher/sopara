<?php

namespace App\Blocks;

use App\Services\BlockEngine;

class PostListing extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Liste des articles',
            'id' => 'post-listing',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'grid-view',
            'render_callback' => [parent::class, 'renderBlock'],
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
                'spacing' => [
                    'margin' => true,
                ],
            ],
            'fields' => [],
        ];

        return $meta_boxes;
    }
}

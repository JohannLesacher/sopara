<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class KeyFigure extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Chiffre clé',
            'id' => 'key-figure',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'chart-bar',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('key-figure', Vite::asset('resources/css/blocks/key-figure.scss'), [], null);
            },
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
                'color' => [
                    'text' => true,
                    'background' => true,
                ],
                'spacing' => [
                    'padding' => true,
                ],
            ],
            'fields' => [
                [
                    'id' => 'valeur',
                    'name' => 'Valeur',
                    'type' => 'text',
                ],
                [
                    'id' => 'unite',
                    'name' => 'Unité',
                    'type' => 'text',
                ],
                [
                    'id' => 'libelle',
                    'name' => 'Libellé',
                    'type' => 'text',
                ],
            ],
        ];

        return $meta_boxes;
    }

    public static function boot(): void
    {
        add_filter('register_block_type_args', [static::class, 'registerAttributes'], 10, 2);
    }

    public static function registerAttributes(array $args, string $name): array
    {
        if ($name !== 'meta-box/key-figure') {
            return $args;
        }

        $args['attributes']['valueColor'] = ['type' => 'string', 'default' => 'primary-0'];
        $args['attributes']['customValueColor'] = ['type' => 'string'];

        return $args;
    }
}

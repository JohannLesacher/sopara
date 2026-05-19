<?php

namespace App\Blocks;

use Illuminate\Support\Facades\Vite;

use function Roots\view;

class Tabs
{
    public static function boot(): void
    {
        register_block_type('sur-mesure/tabs', [
            'api_version' => 3,
            'render_callback' => [self::class, 'renderTabs'],
        ]);

        register_block_type('sur-mesure/tab', [
            'api_version' => 3,
            'attributes' => [
                'title' => ['type' => 'string', 'default' => 'Titre onglet'],
                'showFirstChildInHeaderMobile' => ['type' => 'boolean', 'default' => false],
            ],
            'supports' => [
                'color' => ['background' => true, 'text' => true],
                'spacing' => ['blockGap' => true],
                'layout' => ['allowEditing' => false, 'default' => ['type' => 'flex']],
            ],
            'render_callback' => [self::class, 'renderTab'],
        ]);

        add_action('enqueue_block_assets', [self::class, 'enqueueStyles']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueueScripts']);
    }

    public static function enqueueStyles(): void
    {
        wp_enqueue_style('heat/tabs', Vite::asset('resources/css/blocks/tabs.scss'), [], null);
    }

    public static function enqueueScripts(): void
    {
        wp_enqueue_script_module('heat/tabs', Vite::asset('resources/js/blocks/tabs.js'), [], null);
    }

    public static function renderTabs(array $attributes, string $content): string
    {
        return view('blocks.tabs', compact('attributes', 'content'))->render();
    }

    public static function renderTab(array $attributes, string $content): string
    {
        return view('blocks.tab', compact('attributes', 'content'))->render();
    }
}

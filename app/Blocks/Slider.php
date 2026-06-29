<?php

namespace App\Blocks;

use Illuminate\Support\Facades\Vite;
use function Roots\view;

class Slider {
    public static function boot(): void {
        register_block_type( 'sur-mesure/slider', [
            'api_version'     => 3,
            'attributes'      => [
                'titre'         => [ 'type' => 'text', 'default' => "" ],
                'perPage'       => [ 'type' => 'number', 'default' => 3 ],
                'perPageTablet' => [ 'type' => 'number' ],
                'perPageMobile' => [ 'type' => 'number' ],
                'loop'          => [ 'type' => 'boolean', 'default' => false ],
                'autoplay'      => [ 'type' => 'boolean', 'default' => false ],
            ],
            'render_callback' => [ self::class, 'renderSlider' ],
        ] );

        register_block_type( 'sur-mesure/slide', [
            'api_version'     => 3,
            'render_callback' => [ self::class, 'renderSlide' ],
        ] );

        add_action( 'enqueue_block_assets', [ self::class, 'enqueueStyles' ] );
        add_action( 'wp_enqueue_scripts', [ self::class, 'enqueueScripts' ] );
    }

    public static function enqueueStyles(): void {
        wp_enqueue_style( 'heat/slider', Vite::asset( 'resources/css/blocks/slider.scss' ), [], null );
    }

    public static function enqueueScripts(): void {
        wp_enqueue_script_module( 'heat/slider', Vite::asset( 'resources/js/blocks/slider.js' ), [], null );
    }

    public static function renderSlider( array $attributes, string $content ): string {
        return view( 'blocks.slider', compact( 'attributes', 'content' ) )->render();
    }

    public static function renderSlide( array $attributes, string $content ): string {
        return view( 'blocks.slide', compact( 'attributes', 'content' ) )->render();
    }
}

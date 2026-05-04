<?php

namespace App\Blocks;

use Illuminate\Support\Facades\Vite;

use function Roots\view;

class Slider
{
    public static function boot(): void
    {
        register_block_type('sur-mesure/slider', [
            'render_callback' => [self::class, 'renderSlider'],
        ]);

        register_block_type('sur-mesure/slide', [
            'render_callback' => [self::class, 'renderSlide'],
        ]);

        wp_enqueue_style('heat/slider', Vite::asset('resources/css/blocks/slider.scss'), [], null);

        if (! is_admin()) {
            wp_enqueue_script_module('heat/slider', Vite::asset('resources/js/blocks/slider.js'), [], null);
        }
    }

    public static function renderSlider(array $attributes, string $content): string
    {
        return view('blocks.slider', compact('attributes', 'content'))->render();
    }

    public static function renderSlide(array $attributes, string $content): string
    {
        return view('blocks.slide', compact('attributes', 'content'))->render();
    }
}

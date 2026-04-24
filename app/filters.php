<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Quickfix for script module loading bug on Firefox
 *
 * @see https://wordpress.org/support/topic/importmap-wordpress-interactivity/
 */
remove_action( 'after_setup_theme', [ wp_script_modules(), 'add_hooks' ] );
add_action( 'wp_head', function () {
    wp_script_modules()->print_import_map();
    wp_script_modules()->print_enqueued_script_modules();
    wp_script_modules()->print_script_module_preloads();
    echo "\r\n";
}, 8 );

/**
 * Allow SVG
 */
add_filter( 'upload_mimes', function ( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';

    return $mimes;
} );

/**
 * Animation option
 */
add_filter( 'register_block_type_args', function ( $args, $name ) {
    $allowed_blocks = [ 'core/heading', 'core/group', 'core/columns', 'core/image' ];
    if ( in_array( $name, $allowed_blocks ) ) {
        $args['attributes']['animateOnScroll'] = [
            'type'    => 'boolean',
            'default' => false,
        ];
    }

    return $args;
}, 10, 2 );

/*
 * Add icon to button
 */
add_filter('render_block', function ($html, $block) {
    if ($block['blockName'] !== 'core/button') {
        return $html;
    }

    if (!str_contains($block['attrs']['className'] ?? '', 'is-style-with-icon')) {
        return $html;
    }

    $color = $block['attrs']['iconColor'] ?? 'currentColor';
    $icon = '<svg xmlns="http://www.w3.org/2000/svg"  width="19" height="16" viewBox="0 0 19 16" fill="none">
<path d="M16.1603 1.98647V7.0807C15.8004 4.05215 13.6804 1.53848 10.8086 0.505198V7.08334C10.3352 3.0972 6.8141 0 2.53846 0H0V16H2.53846C6.81319 16 10.3352 12.9028 10.8077 8.9149V15.493C13.6795 14.4598 15.7995 11.9461 16.1593 8.91754V14.0127C17.8984 12.5463 18.9991 10.3979 18.9991 7.99956C18.9991 5.60119 17.8984 3.45278 16.1593 1.98647H16.1603Z" fill="' . esc_attr($color) . '"/>
</svg>
</svg>';

    return str_replace('</a>', $icon . '</a>', $html);
}, 10, 2);

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

/**
 * Custom block category
 */
add_filter( 'block_categories_all', function ( $categories ) {
    return [
        [
            'slug'  => 'sur-mesure',
            'title' => __( 'Sur-mesure', 'sage' ),
            'icon'  => null,
        ],
        ...$categories,
    ];
} );


add_filter('image_size_names_choose', function ($sizes) {
    return array_merge($sizes, [
        'very-large' => __('Très large', 'sage'),
    ]);
});

add_action('wp_head', function () {
    if (!is_singular()) return;

    $post = get_post();
    if (has_block('core/cover', $post->post_content)) {
        $blocks = parse_blocks($post->post_content);

        $filtered_blocks = array_values(array_filter($blocks, function($block) {
            return !empty(trim($block['blockName'] ?? ''));
        }));

        if (!empty($filtered_blocks) && $filtered_blocks[0]['blockName'] === 'core/cover') {
            $first_block = $filtered_blocks[0];

            if (isset($first_block['attrs']['url'])) {
                echo '<link rel="preload" fetchpriority="high" as="image" href="' . esc_url($first_block['attrs']['url']) . '">';
            }
        }
    }
}, 1);

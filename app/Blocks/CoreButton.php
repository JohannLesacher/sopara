<?php

namespace App\Blocks;

class CoreButton {
    public static function boot(): void {
        add_filter( 'register_block_type_args', [ static::class, 'registerAttributes' ], 10, 2 );
        add_filter( 'render_block', [ static::class, 'addIcon' ], 10, 2 );
    }

    public static function registerAttributes( array $args, string $name ): array {
        if ( $name !== 'core/button' ) {
            return $args;
        }

        $args['attributes']['iconColor']    = [ 'type' => 'string' ];
        $args['attributes']['iconPosition'] = [ 'type' => 'string', 'default' => 'right' ];

        return $args;
    }

    public static function addIcon( string $html, array $block ): string {
        if ( $block['blockName'] !== 'core/button' ) {
            return $html;
        }

        if ( ! str_contains( $block['attrs']['className'] ?? '',
                'is-style-with-icon' ) && ! str_contains( $block['attrs']['className'] ?? '',
                'is-style-border-with-icon' ) ) {
            return $html;
        }

        $color = esc_attr( $block['attrs']['iconColor'] ?? 'currentColor' );
        $icon  = '<svg xmlns="http://www.w3.org/2000/svg" width="19" height="16" viewBox="0 0 19 16" fill="none">'
                 . '<path d="M16.16 1.99v5.09C15.8 4.05 13.68 1.54 10.81.5v6.58C10.34 3.1 6.81 0 2.54 0H0v16h2.54c4.27 0 7.8-3.1 8.27-7.09v6.58c2.87-1.03 4.99-3.55 5.35-6.58v5.1C17.9 12.54 19 10.4 19 8s-1.1-4.55-2.84-6.01m0 5.08" fill="' . $color . '"/></svg>';

        return str_replace( '</a>', $icon . '</a>', $html );
    }
}

<?php

namespace App\Services;

class ThemeSettings
{
    public function registerSettingsPage(): void
    {
        add_filter('mb_settings_pages', function (array $pages): array {
            $pages[] = [
                'id' => 'theme-settings',
                'option_name' => 'theme_settings',
                'menu_title' => 'Réglages du thème',
                'icon_url' => 'dashicons-admin-appearance',
                'position' => 60,
            ];

            return $pages;
        });
    }

    public function registerFields(): void
    {
        add_filter('rwmb_meta_boxes', function (array $meta_boxes): array {
            $languages = function_exists('pll_languages_list')
                ? pll_languages_list(['fields' => 'slug'])
                : ['fr'];

            foreach ($languages as $lang) {
                $meta_boxes[] = [
                    'title' => 'Footer — '.strtoupper($lang),
                    'id' => 'theme-settings-footer-'.$lang,
                    'settings_pages' => 'theme-settings',
                    'fields' => $this->footerFields($lang),
                ];
            }

            $meta_boxes[] = [
                'title' => 'Réseaux sociaux',
                'id' => 'theme-settings-social',
                'settings_pages' => 'theme-settings',
                'fields' => [
                    [
                        'id' => 'footer_social_x',
                        'name' => 'X (Twitter) URL',
                        'type' => 'url',
                    ],
                    [
                        'id' => 'footer_social_linkedin',
                        'name' => 'LinkedIn URL',
                        'type' => 'url',
                    ],
                    [
                        'id' => 'footer_social_youtube',
                        'name' => 'YouTube URL',
                        'type' => 'url',
                    ],
                ],
            ];

            return $meta_boxes;
        });
    }

    private function footerFields(string $lang): array
    {
        return [
            [
                'id' => "footer_slogan_{$lang}",
                'name' => 'Slogan (grand texte)',
                'type' => 'text',
            ],
            [
                'id' => "footer_address_{$lang}",
                'name' => 'Adresse',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'id' => "footer_phone_{$lang}",
                'name' => 'Téléphone',
                'type' => 'text',
            ],
            [
                'id' => "footer_email_{$lang}",
                'name' => 'Email',
                'type' => 'text',
            ],
            [
                'id' => "footer_copyright_text_{$lang}",
                'name' => 'Texte copyright',
                'type' => 'text',
                'placeholder' => 'Sopara. All rights reserved.',
                'desc' => "L'année courante est ajoutée automatiquement : © ".date('Y').' {votre texte}',
            ],
        ];
    }
}

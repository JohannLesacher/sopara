<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Secteurs extends Composer
{
    protected static $views = [
        'partials.secteurs',
    ];

    protected array $settings;

    protected string $lang;

    public function with(): array
    {
        $this->settings = get_option('theme_settings', []);
        $this->lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';

        return [
            'shouldDisplay' => $this->shouldDisplay(),
            'titre' => $this->titre(),
            'images' => $this->images(),
            'pages' => $this->pages(),
        ];
    }

    protected function shouldDisplay(): bool
    {
        if (! is_page()) {
            return false;
        }

        return (bool) get_post_meta(get_the_ID(), 'display_secteurs', true);
    }

    protected function titre(): string
    {
        return $this->settings["titre_secteurs_{$this->lang}"]
            ?? $this->settings['titre_secteurs_fr']
            ?? '';
    }

    protected function images(): array
    {
        $ids = $this->settings['images_secteurs'] ?? [];

        return is_array($ids) ? array_values(array_filter($ids)) : [];
    }

    protected function pages(): array
    {
        $ids = $this->settings['pages_secteurs'] ?? [];

        return is_array($ids) ? array_values(array_filter($ids)) : [];
    }
}

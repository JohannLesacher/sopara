<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class FriseChronologique extends Composer
{
    protected static $views = [
        'blocks.frise-chronologique',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    public function type(): string
    {
        return $this->data()['type'] ?? 'scroll';
    }

    public function etapes(): array
    {
        $raw = $this->data()['etapes'] ?? [];
        if (! is_array($raw)) {
            return [];
        }

        $validEtapes = array_filter($raw, fn ($etape) => ! empty($etape['date']) || ! empty($etape['titre']));

        return array_map(function ($etape) {
            $etape['imageAlt'] = '';

            if (! empty($etape['image'])) {
                $etape['imageAlt'] = get_post_meta($etape['image'], '_wp_attachment_image_alt', true);
            }

            return $etape;
        }, array_values($validEtapes));
    }
}

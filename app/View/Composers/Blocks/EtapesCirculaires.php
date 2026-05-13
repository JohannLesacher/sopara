<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class EtapesCirculaires extends Composer
{
    protected static $views = [
        'blocks.etapes-circulaires',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    public function image(): string
    {
        $id = (int) ($this->data()['image'] ?? 0);
        if (! $id) {
            return '';
        }
        $src = wp_get_attachment_image_src($id, 'large');

        return $src ? $src[0] : '';
    }

    public function etapes(): array
    {
        $raw = $this->data()['etapes'] ?? [];
        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_filter($raw, fn ($etape) => ! empty($etape['titre']) || ! empty($etape['texte'])));
    }

    public function count(): int
    {
        return count($this->etapes());
    }
}

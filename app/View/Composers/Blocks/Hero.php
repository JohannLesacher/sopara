<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class Hero extends Composer
{
    protected static $views = [
        'blocks.hero',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    public function imageFond(): string
    {
        $id = (int) ($this->data()['image_fond'] ?? 0);
        if (! $id) {
            return '';
        }
        $src = wp_get_attachment_image_src($id, 'very-large');

        return $src ? $src[0] : '';
    }

    public function imageFixe(): bool
    {
        return ! empty($this->data()['image_fixe']);
    }

    public function imageFondSources(): array
    {
        $id = (int) ($this->data()['image_fond'] ?? 0);
        if (! $id) {
            return [];
        }
        $sources = [];
        foreach (['medium_large', 'large', 'very-large'] as $size) {
            $src = wp_get_attachment_image_src($id, $size);
            if ($src) {
                $sources[$size] = $src[0];
            }
        }

        return $sources;
    }

    public function titre(): string
    {
        return nl2br($this->data()['titre'] ?? '', false);
    }
}

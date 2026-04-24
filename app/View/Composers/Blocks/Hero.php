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
        $src = wp_get_attachment_image_src($id, 'full');

        return $src ? $src[0] : '';
    }

    public function titre(): string
    {
        return wpautop($this->data()['titre'] ?? '');
    }

    public function titreSecteurs(): string
    {
        return $this->data()['titre_secteurs'] ?? '';
    }

    public function imagesSecteurs(): array
    {
        $ids = $this->data()['images_secteurs'] ?? [];

        return is_array($ids) ? array_values(array_filter($ids)) : [];
    }
}

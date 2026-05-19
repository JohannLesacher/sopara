<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class HoverSectorImage extends Composer
{
    protected static $views = ['blocks.hover-sector-image'];

    public function imageSrc(): ?string
    {
        $imageId = $this->view->getData()['data']['image'] ?? null;
        if (! $imageId) {
            return null;
        }

        return wp_get_attachment_image_src($imageId, 'large')[0] ?? null;
    }

    public function tag(): string
    {
        return $this->view->getData()['data']['tag'] ?? '';
    }

    public function url(): string
    {
        return $this->view->getData()['data']['url'] ?? '';
    }

    public function texte(): string
    {
        return nl2br(esc_html($this->view->getData()['data']['texte'] ?? ''));
    }
}

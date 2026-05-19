<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class HoverCard extends Composer
{
    protected static $views = ['blocks.hover-card'];

    public function imageSrc(): ?string
    {
        $imageId = $this->view->getData()['data']['image'] ?? null;
        if (! $imageId) {
            return null;
        }

        return wp_get_attachment_image_src($imageId, 'large')[0] ?? null;
    }

    public function titre(): string
    {
        return $this->view->getData()['data']['titre'] ?? '';
    }

    public function texte(): string
    {
        return nl2br(esc_html($this->view->getData()['data']['texte'] ?? ''));
    }
}

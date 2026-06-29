<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class HoverSectorImage extends Composer
{
    protected static $views = ['blocks.hover-sector-image'];

    public function imageId(): ?string
    {
        $imageId = $this->view->getData()['data']['image'] ?? null;
        if (! $imageId) {
            return null;
        }

        return $imageId;
    }

    public function imageAlt(): ?string
    {
        $imageId = $this->view->getData()['data']['image'] ?? null;
        if (! $imageId) {
            return null;
        }

        return get_post_meta($imageId, '_wp_attachment_image_alt', true);
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

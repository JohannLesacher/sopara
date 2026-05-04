<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class ImageCard extends Composer
{
    protected static $views = ['blocks.image-card'];

    public function imageSrc(): ?string
    {
        $imageId = $this->view->getData()['data']['image'] ?? null;
        if (! $imageId) {
            return null;
        }

        return wp_get_attachment_image_src($imageId, 'large')[0] ?? null;
    }

    public function ctaLabel(): string
    {
        return $this->view->getData()['data']['cta_label'] ?? '';
    }

    public function ctaUrl(): string
    {
        return $this->view->getData()['data']['cta_url'] ?? '';
    }
}

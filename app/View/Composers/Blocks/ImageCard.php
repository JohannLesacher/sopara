<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class ImageCard extends Composer
{
    protected static $views = ['blocks.image-card'];

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

    public function ctaLabel(): string
    {
        return $this->view->getData()['data']['cta_label'] ?? '';
    }

    public function ctaUrl(): string
    {
        return $this->view->getData()['data']['cta_url'] ?? '';
    }
}

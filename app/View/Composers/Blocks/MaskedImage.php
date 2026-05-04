<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class MaskedImage extends Composer
{
    protected static $views = [
        'blocks.masked-image',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    private function svgId(): int
    {
        $ids = $this->data()['svg'] ?? [];

        return is_array($ids) && ! empty($ids) ? (int) $ids[0] : 0;
    }

    public function maskUrl(): string
    {
        $id = $this->svgId();

        return $id ? (wp_get_attachment_url($id) ?: '') : '';
    }

    public function imageId(): int
    {
        return (int) ($this->data()['image'] ?? 0);
    }

    public function imageFixe(): bool {
        return (bool) ( $this->data()['image_fixed'] ?? false );
    }

    public function aspectRatio(): string
    {
        $id = $this->svgId();
        if (! $id) {
            return '';
        }

        $file = get_attached_file($id);
        if (! $file || ! file_exists($file)) {
            return '';
        }

        $content = file_get_contents($file);
        if (! $content) {
            return '';
        }

        if (preg_match('/viewBox=["\']([^"\']+)["\']/', $content, $m)) {
            $parts = preg_split('/[\s,]+/', trim($m[1]));
            if (count($parts) === 4) {
                return $parts[2].' / '.$parts[3];
            }
        }

        return '';
    }

    public function width(): string
    {
        return (string) ($this->view->getData()['attributes']['width'] ?? '');
    }

    public function inlineStyles(): string
    {
        $styles = [];

        if ($ratio = $this->aspectRatio()) {
            $styles[] = "aspect-ratio: $ratio";
        }

        if ($url = $this->maskUrl()) {
            $styles[] = "--mask-url: url('$url')";
        }

        if ($width = $this->width()) {
            $styles[] = "width: $width";
        }

        return implode('; ', $styles);
    }
}

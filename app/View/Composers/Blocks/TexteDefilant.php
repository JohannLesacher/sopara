<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class TexteDefilant extends Composer
{
    private const ALLOWED_TAGS = ['div', 'p', 'h1', 'h2', 'h3'];

    protected static $views = [
        'blocks.texte-defilant',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    private function attributes(): array
    {
        return $this->view->getData()['attributes'] ?? [];
    }

    public function tag(): string
    {
        $tag = $this->data()['tag'] ?? 'div';

        return in_array($tag, self::ALLOWED_TAGS, true) ? $tag : 'div';
    }

    public function texte(): string
    {
        return $this->data()['texte'] ?? 'Texte défilant';
    }

    public function vitesse(): string
    {
        return $this->data()['vitesse'] ?? 'normal';
    }

    public function direction(): string
    {
        return $this->data()['direction'] ?? 'gauche';
    }

    public function ariaLabel(): string
    {
        return strip_tags($this->texte());
    }

    public function classeNames(): array
    {
        $attributes = $this->attributes();

        $classes = ['block-texte-defilant'];

        if (! empty($attributes['className'])) {
            $classes[] = $attributes['className'];
        }

        $classes[] = 'align' . ($attributes['align'] ?? 'full');

        return $classes;
    }
}

<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class IconText extends Composer
{
    protected static $views = [
        'blocks.icon-text',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    public function icone(): int
    {
        $ids = $this->data()['icone'] ?? [];

        return is_array($ids) && ! empty($ids) ? (int) $ids[0] : 0;
    }
}

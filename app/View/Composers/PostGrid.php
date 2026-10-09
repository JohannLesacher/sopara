<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class PostGrid extends Composer
{
    protected static $views = [
        'partials.post-grid',
    ];

    public function with(): array
    {
        return [
            'gridPagination' => $this->gridPagination(),
        ];
    }

    protected function gridPagination(): string
    {
        if (empty($this->view->getData()['paginate'])) {
            return '';
        }

        return paginate_links([
            'type' => 'list',
            'mid_size' => 1,
            'prev_text' => pll__('Précédent'),
            'next_text' => pll__('Suivant'),
        ]) ?? '';
    }
}

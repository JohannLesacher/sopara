<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class EtapesCirculaires extends Composer
{
    protected static $views = [
        'blocks.etapes-circulaires',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    public function image(): string
    {
        $id = (int) ($this->data()['image'] ?? 0);
        if (! $id) {
            return '';
        }
        $src = wp_get_attachment_image_src($id, 'large');

        return $src ? $src[0] : '';
    }

    public function etapes(): array
    {
        $raw = $this->data()['etapes'] ?? [];
        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_filter($raw, fn ($etape) => ! empty($etape['titre']) || ! empty($etape['texte'])));
    }

    public function count(): int
    {
        return count($this->etapes());
    }

    public function gridStyle(): string
    {
        $rows = $this->totalRows();

        return sprintf(
            'grid-template-columns: 1fr 1fr 1fr; grid-template-rows: repeat(%d, auto);',
            $rows
        );
    }

    public function imageStyle(): string
    {
        $sideRows = $this->sideRows();

        return sprintf('grid-area: 1 / 2 / %d / 3;', $sideRows + 1);
    }

    public function items(): array
    {
        $etapes = $this->etapes();
        $count = count($etapes);
        $sideRows = $this->sideRows();
        $totalRows = $this->totalRows();
        $odd = $count % 2 === 1;
        $items = [];

        foreach ($etapes as $i => $etape) {
            $step = $i + 1;

            if ($step <= $sideRows) {
                $row = $step;
                $col = 3;
                $side = 'right';
            } elseif ($odd && $step === $sideRows + 1) {
                $row = $totalRows;
                $col = 2;
                $side = 'bottom';
            } else {
                $leftIdx = $step - $sideRows - ($odd ? 1 : 0);
                $row = $sideRows - ($leftIdx - 1);
                $col = 1;
                $side = 'left';
            }

            $angleDeg = (360 / $count) * ($i + 0.5);
            $rad = deg2rad($angleDeg);

            $items[] = [
                'index'    => $i,
                'titre'    => $etape['titre'] ?? '',
                'texte'    => $etape['texte'] ?? '',
                'num'      => str_pad((string) $step, 2, '0', STR_PAD_LEFT),
                'side'     => $side,
                'boxStyle' => sprintf('grid-area: %d / %d / %d / %d;', $row, $col, $row + 1, $col + 1),
                'nx'       => round(sin($rad), 4),
                'ny'       => round(-cos($rad), 4),
            ];
        }

        return $items;
    }

    private function sideRows(): int
    {
        $count = $this->count();
        $odd = $count % 2 === 1;

        return (int) ceil(($count - ($odd ? 1 : 0)) / 2);
    }

    private function totalRows(): int
    {
        $count = $this->count();

        return $this->sideRows() + ($count % 2 === 1 ? 1 : 0);
    }
}

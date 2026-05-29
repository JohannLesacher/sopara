<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class Emetteur extends Composer
{
    protected static $views = ['blocks.emetteur'];

    public function imageId(): ?int
    {
        $id = $this->view->getData()['data']['image'] ?? null;

        return $id ? (int) $id : null;
    }

    public function titre(): string
    {
        return $this->view->getData()['data']['titre'] ?? '';
    }

    public function sousTitre(): string
    {
        return $this->view->getData()['data']['sous_titre'] ?? '';
    }

    public function texte(): string
    {
        return $this->view->getData()['data']['texte'] ?? '';
    }

    public function lien(): array
    {
        $lienUrl = $this->view->getData()['data']['lien_url'] ?? [];
        $lienText = $this->view->getData()['data']['lien_text'] ?? [];

        return [
            'url' => $lienUrl,
            'title' => $lienText,
        ];
    }
}

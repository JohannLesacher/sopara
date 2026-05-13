<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class ImageHotspots extends Composer {
    protected static $views = [ 'blocks.image-hotspots' ];

    function getData() {
        return $this->view->getData()['data'] ?? [];
    }

    function image() {
        $data = $this->getData();

        return $data['image'] ?? null;
    }

    function points() {
        $data = $this->getData();

        return $data['points'] ?? null;
    }
}

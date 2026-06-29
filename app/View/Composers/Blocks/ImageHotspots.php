<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class ImageHotspots extends Composer {
    protected static $views = [ 'blocks.image-hotspots' ];

    function getData() {
        return $this->view->getData()['data'] ?? [];
    }

    function imageId() {
        $data = $this->getData();

        return $data['image'] ?? null;
    }

    function imageAlt() {
        $data    = $this->getData();
        $imageId = $data['image'] ?? null;

        if ( ! $imageId ) {
            return null;
        }

        return get_post_meta( $imageId, '_wp_attachment_image_alt', true );
    }

    function points() {
        $data = $this->getData();

        return $data['points'] ?? null;
    }
}

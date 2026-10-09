<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Expert extends Composer
{
    protected static $views = [
        'single-expert',
    ];

    protected int $expertId;

    public function with(): array
    {
        $this->expertId = get_queried_object_id();

        return [
            'photoId' => (int) $this->meta('photo'),
            'fonction' => $this->meta('fonction'),
            'expertise' => nl2br(esc_html($this->meta('expertise'))),
            'experience' => (int) $this->meta('annees_experience'),
            'linkedin' => $this->meta('linkedin'),
            'content' => apply_filters('the_content', get_post_field('post_content', $this->expertId)),
        ];
    }

    protected function meta(string $key): string
    {
        return (string) get_post_meta($this->expertId, $key, true);
    }
}

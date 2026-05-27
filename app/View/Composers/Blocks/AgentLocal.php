<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class AgentLocal extends Composer
{
    protected static $views = ['blocks.agent-local'];

    public function imageId(): ?int
    {
        $imageId = $this->view->getData()['data']['image'] ?? null;
        if (! $imageId) {
            return null;
        }

        return $imageId;
    }

    public function logoId(): ?int
    {
        $logoId = $this->view->getData()['data']['logo'] ?? null;
        if (! $logoId) {
            return null;
        }

        return (int) $logoId;
    }

    public function title(): string
    {
        return $this->view->getData()['data']['title'] ?? '';
    }

    public function websiteUrl(): string
    {
        return $this->view->getData()['data']['website_url'] ?? '';
    }

    public function websiteName(): string
    {
        $url = $this->view->getData()['data']['website_url'] ?? '';
        if (! $url) {
            return '';
        }

        $parsedUrl = parse_url($url);
        return $parsedUrl['host'] ?? '';
    }

    public function locationCity(): string
    {
        return $this->view->getData()['data']['location']['city'] ?? '';
    }

    public function locationCountry(): string
    {
        return $this->view->getData()['data']['location']['country'] ?? '';
    }

    public function tags(): array
    {
        $tags = $this->view->getData()['data']['tags'] ?? [];

        if (! is_array($tags)) {
            $tags = [$tags];
        }

        return array_values(array_filter(array_map('trim', $tags)));
    }

    public function contactName(): string
    {
        return $this->view->getData()['data']['contact']['name'] ?? '';
    }

    public function contactEmail(): string
    {
        return $this->view->getData()['data']['contact']['email'] ?? '';
    }

    public function contactPhone(): string
    {
        return $this->view->getData()['data']['contact']['phone'] ?? '';
    }
}

<?php

namespace App\Services\Schema;

use App\Services\BlogPost;
use WP_Post;
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

class ExpertPerson extends Abstract_Schema_Piece
{
    public $identifier = 'expert_person';

    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    public static function schemaId(WP_Post $expert): string
    {
        return get_permalink($expert).'#person';
    }

    public function is_needed(): bool
    {
        return $this->experts() !== [];
    }

    public function generate(): array
    {
        return array_map(fn (WP_Post $expert) => $this->person($expert), $this->experts());
    }

    /**
     * @return WP_Post[]
     */
    private function experts(): array
    {
        $indexable = $this->context->indexable;

        if ($indexable->object_type !== 'post') {
            return [];
        }

        return match ($indexable->object_sub_type) {
            'expert' => array_filter([get_post($indexable->object_id)]),
            'post' => $this->blogPost->experts($indexable->object_id),
            default => [],
        };
    }

    private function person(WP_Post $expert): array
    {
        $data = [
            '@type' => 'Person',
            '@id' => self::schemaId($expert),
            'name' => get_the_title($expert),
            'url' => get_permalink($expert),
        ];

        $fonction = (string) get_post_meta($expert->ID, 'fonction', true);
        $expertise = (string) get_post_meta($expert->ID, 'expertise', true);
        $linkedin = (string) get_post_meta($expert->ID, 'linkedin', true);
        $imageUrl = get_the_post_thumbnail_url($expert, 'medium');

        if ($fonction !== '') {
            $data['jobTitle'] = $fonction;
        }

        if ($expertise !== '') {
            $data['knowsAbout'] = $expertise;
        }

        if ($imageUrl) {
            $data['image'] = $imageUrl;
        }

        if ($linkedin !== '') {
            $data['sameAs'] = [$linkedin];
        }

        return $data;
    }
}

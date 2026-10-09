<?php

namespace App\PostTypes;

class Expert
{
    private string $post_type = 'expert';

    public function addMetas(array $meta_boxes): array
    {
        $meta_boxes[] = [
            'title' => 'Profil',
            'post_types' => $this->post_type,
            'fields' => [
                [
                    'id' => 'photo',
                    'name' => 'Photo de profil',
                    'type' => 'single_image',
                ],
                [
                    'id' => 'fonction',
                    'name' => 'Fonction',
                    'type' => 'text',
                ],
                [
                    'id' => 'expertise',
                    'name' => 'Expertise',
                    'type' => 'textarea',
                    'rows' => 3,
                ],
                [
                    'id' => 'annees_experience',
                    'name' => 'Années d\'expérience',
                    'type' => 'number',
                    'min' => 0,
                ],
                [
                    'id' => 'linkedin',
                    'name' => 'LinkedIn',
                    'type' => 'url',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

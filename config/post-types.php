<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Post Types
    |--------------------------------------------------------------------------
    |
    | Post types to be registered with Extended CPTs
    | <https://github.com/johnbillion/extended-cpts>
    |
    */

    'post_types' => [
        'expert' => [
            'menu_icon' => 'dashicons-businessperson',
            'supports' => ['title', 'editor', 'thumbnail'],
            'public' => true,
            'show_in_rest' => true,
            'has_archive' => false,
            'menu_position' => 5,
            'names' => [
                'singular' => 'Expert',
                'plural' => 'Experts',
                'slug' => 'expert',
            ],
            'labels' => [
                'name' => 'Experts',
                'singular_name' => 'Expert',
                'menu_name' => 'Experts',
                'name_admin_bar' => 'Expert',
                'add_new' => 'Ajouter',
                'add_new_item' => 'Ajouter un expert',
                'new_item' => 'Nouvel expert',
                'edit_item' => 'Modifier l\'expert',
                'view_item' => 'Voir l\'expert',
                'view_items' => 'Voir les experts',
                'all_items' => 'Tous les experts',
                'search_items' => 'Rechercher des experts',
                'not_found' => 'Aucun expert trouvé',
                'not_found_in_trash' => 'Aucun expert dans la corbeille',
                'archives' => 'Archives des experts',
                'attributes' => 'Attributs de l\'expert',
                'insert_into_item' => 'Insérer dans l\'expert',
                'uploaded_to_this_item' => 'Téléversé sur cet expert',
                'featured_image' => 'Photo',
                'set_featured_image' => 'Définir la photo',
                'remove_featured_image' => 'Retirer la photo',
                'use_featured_image' => 'Utiliser comme photo',
                'filter_items_list' => 'Filtrer la liste des experts',
                'items_list_navigation' => 'Navigation de la liste des experts',
                'items_list' => 'Liste des experts',
                'item_published' => 'Expert publié.',
                'item_published_privately' => 'Expert publié en privé.',
                'item_reverted_to_draft' => 'Expert repassé en brouillon.',
                'item_scheduled' => 'Expert planifié.',
                'item_updated' => 'Expert mis à jour.',
                'item_link' => 'Lien de l\'expert',
                'item_link_description' => 'Un lien vers un expert.',
            ],
        ],
        //        'evenement' => [
        //            'menu_icon'      => 'dashicons-calendar-alt',
        //            'supports'       => [ 'title', 'editor', 'thumbnail' ],
        //            'public'         => true,
        //            'show_in_rest'   => true,
        //            'show_in_menu'   => true,
        //            'show_ui'        => true,
        //            'has_archive'    => true,
        //            'menu_position'  => 4,
        //            'names'          => [
        //                'singular' => 'Événement',
        //                'plural'   => 'Événements',
        //                'slug'     => 'evenements',
        //            ],
        //            'labels'         => [
        //                'singular'                   => 'Événement',
        //                'plural'                     => 'Événements',
        //                'menu_name'                  => 'Événements',
        //                'name'                       => 'Événements',
        //                'singular_name'              => 'Événement',
        //                'name_admin_bar'             => 'Événement',
        //                'search_items'               => 'Rechercher des événements',
        //                'popular_items'              => 'Événements populaires',
        //                'all_items'                  => 'Tous les événements',
        //                'archives'                   => 'Archives des événements',
        //                'parent_item'                => 'Événement parent',
        //                'parent_item_colon'          => 'Événement parent :',
        //                'edit_item'                  => 'Modifier l\'événement',
        //                'view_item'                  => 'Voir l\'événement',
        //                'update_item'                => 'Mettre à jour',
        //                'add_new_item'               => 'Ajouter un événement',
        //                'new_item_name'              => 'Nom du nouvel événement',
        //                'separate_items_with_commas' => 'Séparer par des virgules',
        //                'add_or_remove_items'        => 'Ajouter ou supprimer',
        //                'choose_from_most_used'      => 'Choisir parmi les plus utilisés',
        //                'not_found'                  => 'Aucun événement trouvé',
        //                'no_terms'                   => 'Aucun événement',
        //                'filter_by_item'             => 'Filtrer par événement',
        //                'most_used'                  => 'Plus utilisés',
        //                'back_to_items'              => '&larr; Retour aux événements',
        //                'item_link'                  => 'Lien de l\'événement',
        //                'item_link_description'      => 'Le lien vers l\'événement.',
        //                'no_item'                    => 'Aucun événement',
        //                'filter_by'                  => 'Filtrer par',
        //            ],
        //            'admin_cols'     => [
        //                'date_event' => [
        //                    'title'    => 'Date de l\'événement',
        //                    'meta_key' => 'date_event',
        //                    'function' => function () {
        //                        global $post;
        //                        $date = get_post_meta( $post->ID, 'date_event', true );
        //                        if ( $date ) {
        //                            $date_obj = DateTime::createFromFormat( 'Ymd', $date );
        //                            echo $date_obj ? $date_obj->format( 'd/m/Y' ) : esc_html( $date );
        //                        }
        //                    },
        //                ],
        //            ],
        //            'site_sortables' => [
        //                'date_event' => [
        //                    'meta_key' => 'date_event',
        //                ],
        //            ],
        //        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Taxonomies
    |--------------------------------------------------------------------------
    |
    | Taxonomies to be registered with Extended CPTs library
    | <https://github.com/johnbillion/extended-cpts>
    |
    */

    'taxonomies' => [
        'type_contenu' => [
            'post_types' => ['post'],
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'names' => [
                'singular' => 'Type de contenu',
                'plural' => 'Types de contenu',
                'slug' => 'type-contenu',
            ],
            'labels' => [
                'name' => 'Types de contenu',
                'singular_name' => 'Type de contenu',
                'menu_name' => 'Types de contenu',
                'search_items' => 'Rechercher des types de contenu',
                'popular_items' => 'Types de contenu populaires',
                'all_items' => 'Tous les types de contenu',
                'parent_item' => 'Type de contenu parent',
                'parent_item_colon' => 'Type de contenu parent :',
                'name_field_description' => 'Le nom tel qu\'il apparaît sur le site.',
                'slug_field_description' => 'Version du nom adaptée aux URL, en minuscules, avec uniquement des lettres, chiffres et tirets.',
                'parent_field_description' => 'Assigner un terme parent pour créer une hiérarchie.',
                'desc_field_description' => 'La description n\'est pas affichée par défaut.',
                'edit_item' => 'Modifier le type de contenu',
                'view_item' => 'Voir le type de contenu',
                'update_item' => 'Mettre à jour le type de contenu',
                'add_new_item' => 'Ajouter un type de contenu',
                'new_item_name' => 'Nom du nouveau type de contenu',
                'separate_items_with_commas' => 'Séparer les types de contenu par des virgules',
                'add_or_remove_items' => 'Ajouter ou retirer des types de contenu',
                'choose_from_most_used' => 'Choisir parmi les types de contenu les plus utilisés',
                'not_found' => 'Aucun type de contenu trouvé',
                'no_terms' => 'Aucun type de contenu',
                'filter_by_item' => 'Filtrer par type de contenu',
                'items_list_navigation' => 'Navigation de la liste des types de contenu',
                'items_list' => 'Liste des types de contenu',
                'most_used' => 'Les plus utilisés',
                'back_to_items' => '&larr; Retour aux types de contenu',
                'item_link' => 'Lien du type de contenu',
                'item_link_description' => 'Un lien vers un type de contenu.',
            ],
        ],
        'secteur_activite' => [
            'post_types' => ['post'],
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'names' => [
                'singular' => 'Secteur d\'activité',
                'plural' => 'Secteurs d\'activité',
                'slug' => 'secteur-activite',
            ],
            'labels' => [
                'name' => 'Secteurs d\'activité',
                'singular_name' => 'Secteur d\'activité',
                'menu_name' => 'Secteurs d\'activité',
                'search_items' => 'Rechercher des secteurs d\'activité',
                'popular_items' => 'Secteurs d\'activité populaires',
                'all_items' => 'Tous les secteurs d\'activité',
                'parent_item' => 'Secteur d\'activité parent',
                'parent_item_colon' => 'Secteur d\'activité parent :',
                'name_field_description' => 'Le nom tel qu\'il apparaît sur le site.',
                'slug_field_description' => 'Version du nom adaptée aux URL, en minuscules, avec uniquement des lettres, chiffres et tirets.',
                'parent_field_description' => 'Assigner un terme parent pour créer une hiérarchie.',
                'desc_field_description' => 'La description n\'est pas affichée par défaut.',
                'edit_item' => 'Modifier le secteur d\'activité',
                'view_item' => 'Voir le secteur d\'activité',
                'update_item' => 'Mettre à jour le secteur d\'activité',
                'add_new_item' => 'Ajouter un secteur d\'activité',
                'new_item_name' => 'Nom du nouveau secteur d\'activité',
                'separate_items_with_commas' => 'Séparer les secteurs d\'activité par des virgules',
                'add_or_remove_items' => 'Ajouter ou retirer des secteurs d\'activité',
                'choose_from_most_used' => 'Choisir parmi les secteurs d\'activité les plus utilisés',
                'not_found' => 'Aucun secteur d\'activité trouvé',
                'no_terms' => 'Aucun secteur d\'activité',
                'filter_by_item' => 'Filtrer par secteur d\'activité',
                'items_list_navigation' => 'Navigation de la liste des secteurs d\'activité',
                'items_list' => 'Liste des secteurs d\'activité',
                'most_used' => 'Les plus utilisés',
                'back_to_items' => '&larr; Retour aux secteurs d\'activité',
                'item_link' => 'Lien du secteur d\'activité',
                'item_link_description' => 'Un lien vers un secteur d\'activité.',
            ],
        ],
    ],
];

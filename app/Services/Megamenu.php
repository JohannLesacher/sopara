<?php

namespace App\Services;

use Illuminate\Support\Facades\Vite;

class Megamenu {
    /**
     * Affiche les champs personnalisés dans l'admin.
     */
    public function renderCustomFields( string $id, object $item, int $depth, object $args ): void {

        if ( $depth === 0 ) {
            $this->renderImageField( $item );
        }

        if ( $depth >= 1 ) {
            $this->renderRadioField(
                'megamenu-style',
                $item,
                'Style de l\'élément :',
                '_megamenu_style',
                [
                    'Classique' => 'classique',
                    'Flèche'    => 'fleche',
                    'Bouton'    => 'bouton',
                    'Invisible' => 'invisible',
                ],
                'classique'
            );

            $this->renderTextField(
                'megamenu-button-text',
                $item,
                'Texte du bouton :',
                '_megamenu_button_text'
            );
        }
    }

    /**
     * Affiche un champ texte personnalisé dans l'admin.
     *
     * @param string $id ID du champ (pour le `name` et l'`id` HTML).
     * @param object $item Objet de l'élément de menu.
     * @param string $label Libellé du champ.
     * @param string $meta_key Clé du meta champ en base de données.
     * @param string $default Valeur par défaut (optionnelle).
     */
    private function renderTextField(
        string $id,
        object $item,
        string $label,
        string $meta_key,
        string $default = ''
    ): void {
        $value      = get_post_meta( $item->ID, $meta_key, true ) ?: $default;
        $field_id   = "edit-menu-item-{$id}-{$item->ID}";
        $field_name = "menu-item-{$id}[{$item->ID}]";
        ?>
        <p class="field-<?= esc_attr( $id ); ?> description-wide" style="display: none;">
            <label for="<?= $field_id; ?>">
                <?= esc_html( $label ); ?>
                <input
                    type="text"
                    id="<?= $field_id; ?>"
                    class="widefat code edit-menu-item-<?= esc_attr( $id ); ?>"
                    name="<?= $field_name; ?>"
                    value="<?= esc_attr( $value ); ?>"
                />
            </label>
        </p>
        <?php
    }

    /**
     * Affiche un champ radio personnalisé dans l'admin.
     *
     * @param string $id ID du champ (pour le `name` et l'`id` HTML).
     * @param object $item Objet de l'élément de menu.
     * @param string $label Libellé du champ.
     * @param string $meta_key Clé du meta champ en base de données (ex: '_megamenu_style').
     * @param array $options Tableau associatif des options (ex: ['Oui' => 'yes', 'Non' => 'no']).
     * @param string $default Valeur par défaut (optionnelle).
     */
    private function renderRadioField(
        string $id,
        object $item,
        string $label,
        string $meta_key,
        array $options,
        string $default = ''
    ): void {
        $current_value = get_post_meta( $item->ID, $meta_key, true ) ?: $default;
        $field_id      = "edit-menu-item-{$id}-{$item->ID}";
        $field_name    = "menu-item-{$id}[{$item->ID}]";
        ?>
        <p class="field-<?= esc_attr( $id ); ?> description-wide">
            <label for="<?= $field_id; ?>"><?= esc_html( $label ); ?><br/>
                <?php foreach ( $options as $option_label => $option_value ) : ?>
                    <label style="display: inline-block; margin-right: 10px; margin-top: 5px;">
                        <input
                            type="radio"
                            id="<?= $field_id . '-' . esc_attr( $option_value ); ?>"
                            name="<?= $field_name; ?>"
                            value="<?= esc_attr( $option_value ); ?>"
                            class="widefat code edit-menu-item-<?= esc_attr( $id ); ?>"
                            <?= checked( $current_value, $option_value, false ); ?>
                        />
                        <?= esc_html( $option_label ); ?>
                    </label>
                <?php endforeach; ?>
            </label>
        </p>
        <?php
    }

    /**
     * Affiche le champ image (uniquement pour depth = 0).
     */
    private function renderImageField( object $item ): void {
        $image_id  = (int) get_post_meta( $item->ID, '_megamenu_image_id', true );
        $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
        ?>
        <p class="field-megamenu-image description-wide">
            <label for="edit-menu-item-megamenu-image-<?= $item->ID; ?>">
                Photo du menu (niveau 1 uniquement) :
                <div class="megamenu-image-container" style="margin-top: 5px;">
                    <?php if ( $image_url ) : ?>
                        <img
                            src="<?= esc_url( $image_url ); ?>"
                            style="max-width: 100px; max-height: 100px; display: block; margin-bottom: 5px;"
                            class="megamenu-image-preview"
                            data-menu-item-id="<?= $item->ID; ?>"
                        />
                    <?php endif; ?>
                    <button
                        type="button"
                        class="button upload-megamenu-image"
                        data-menu-item-id="<?= $item->ID; ?>"
                        style="margin-right: 5px;"
                    >
                        <?php _e( 'Sélectionner une image' ); ?>
                    </button>
                    <button
                        type="button"
                        class="button remove-megamenu-image"
                        data-menu-item-id="<?= $item->ID; ?>"
                        <?php echo $image_id ? '' : 'style="display: none;"'; ?>
                    >
                        <?php _e( 'Supprimer' ); ?>
                    </button>
                    <input
                        type="hidden"
                        id="edit-menu-item-megamenu-image-<?= $item->ID; ?>"
                        class="megamenu-image-id"
                        name="menu-item-megamenu-image[<?= $item->ID; ?>]"
                        value="<?= esc_attr( $image_id ); ?>"
                        data-menu-item-id="<?= $item->ID; ?>"
                    />
                </div>
            </label>
        </p>
        <?php
    }

    /**
     * Sauvegarde les champs personnalisés.
     */
    public function saveCustomFields( int $menu_id, int $menu_item_db_id, array $args ): void {
        // Sauvegarde de l'ID de l'image
        if ( isset( $_REQUEST['menu-item-megamenu-image'][ $menu_item_db_id ] ) ) {
            update_post_meta( $menu_item_db_id, '_megamenu_image_id',
                (int) $_REQUEST['menu-item-megamenu-image'][ $menu_item_db_id ] );
        }

        // Sauvegarde du champ radio
        if ( isset( $_REQUEST['menu-item-megamenu-style'][ $menu_item_db_id ] ) ) {
            update_post_meta( $menu_item_db_id, '_megamenu_style',
                sanitize_text_field( $_REQUEST['menu-item-megamenu-style'][ $menu_item_db_id ] ) );
        }

        // Sauvegarde du champ texte "Texte bouton"
        if ( isset( $_REQUEST['menu-item-megamenu-button-text'][ $menu_item_db_id ] ) ) {
            update_post_meta( $menu_item_db_id, '_megamenu_button_text',
                sanitize_text_field( $_REQUEST['menu-item-megamenu-button-text'][ $menu_item_db_id ] ) );
        }
    }

    /**
     * Ajoute les champs à l'objet $item.
     */
    public function addCustomFieldsToMenuItem( object $menu_item ): object {
        $menu_item->megamenu_image_id    = (int) get_post_meta( $menu_item->ID, '_megamenu_image_id', true );
        $menu_item->megamenu_style       = get_post_meta( $menu_item->ID, '_megamenu_style', true ) ?: 'classique';
        $menu_item->megamenu_button_text = get_post_meta( $menu_item->ID, '_megamenu_button_text', true ) ?: '';

        return $menu_item;
    }

    /**
     * Récupère l'URL de l'image pour un élément de menu.
     *
     * @param object $menu_item
     * @param string $meta_key
     *
     * @return string|null
     */
    public function getImageId( object $menu_item, string $meta_key = '_megamenu_image_id' ): ?string {
        return get_post_meta( $menu_item->id, $meta_key, true );
    }

    /**
     * Récupère la valeur du champ radio pour un élément de menu.
     *
     * @param object $menu_item Objet de l'élément de menu.
     * @param string $meta_key
     *
     * @return string
     */
    public function getRadioFieldValue( object $menu_item, string $meta_key = '_megamenu_style' ): string {
        return $menu_item->megamenu_style ?? get_post_meta( $menu_item->id, $meta_key, true ) ?: 'classique';
    }

    /**
     * Récupère le texte d'un champ text
     *
     * @param object $menu_item
     * @param string $meta_key
     *
     * @return string|null
     */
    public function getTextFieldValue( object $menu_item, string $meta_key = '_megamenu_button_text' ): ?string {
        return get_post_meta( $menu_item->id, $meta_key, true );
    }

    /**
     * Enqueue les scripts JS/CSS pour l'uploader d'image.
     */
    public function enqueueAdminScripts(): void {
        wp_enqueue_media();
        wp_enqueue_script( 'megamenu', Vite::asset( 'resources/js/admin/megamenu.js' ), [], null );
    }
}

document.addEventListener('DOMContentLoaded', () => {
  // =========================================================================
  // GESTION DE L'UPLOADER D'IMAGE
  // =========================================================================
  document.querySelectorAll('.upload-megamenu-image').forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      const menuItemId = button.getAttribute('data-menu-item-id');
      if (!menuItemId) return;

      const uploader = wp.media({
        title: 'Sélectionner une image pour le megamenu',
        button: {text: 'Utiliser cette image'},
        multiple: false,
      });

      uploader.on('select', () => {
        const attachment = uploader.state().get('selection').first().toJSON();

        const input = document.getElementById(`edit-menu-item-megamenu-image-${menuItemId}`);
        const preview = document.querySelector(`.megamenu-image-preview[data-menu-item-id="${menuItemId}"]`);
        const removeButton = document.querySelector(`.remove-megamenu-image[data-menu-item-id="${menuItemId}"]`);

        if (input) input.value = attachment.id;
        if (preview) preview.src = attachment.sizes.thumbnail.url;
        if (removeButton) removeButton.style.display = 'inline-block';
      });

      uploader.open();
    });
  });

  // =========================================================================
  // SUPPRESSION DE L'IMAGE
  // =========================================================================
  document.querySelectorAll('.remove-megamenu-image').forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      const menuItemId = button.getAttribute('data-menu-item-id');
      if (!menuItemId) return;

      const input = document.getElementById(`edit-menu-item-megamenu-image-${menuItemId}`);
      const preview = document.querySelector(`.megamenu-image-preview[data-menu-item-id="${menuItemId}"]`);

      if (input) input.value = '';
      if (preview) preview.src = '';
      button.style.display = 'none';
    });
  });

  // =========================================================================
  // GESTION DU CHAMP "TEXTE BOUTON" (conditionnel)
  // =========================================================================
  document.querySelectorAll('input[name^="menu-item-megamenu-style"]').forEach(radio => {
    const nameMatch = radio.name.match(/menu-item-megamenu-style\[(\d+)\]/);
    if (!nameMatch) return;

    const menuItemId = nameMatch[1];
    const textFieldContainer = document.querySelector(`#edit-menu-item-megamenu-button-text-${menuItemId}`)?.closest('.field-megamenu-button-text');

    const updateVisibility = () => {
      if (radio.checked && radio.value === 'bouton') {
        textFieldContainer?.style.removeProperty('display');
      } else {
        textFieldContainer?.style.setProperty('display', 'none');
      }
    };

    radio.addEventListener('change', updateVisibility);
    updateVisibility(); // Initialise au chargement
  });
});

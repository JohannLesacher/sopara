import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/group', {
    name: '3-columns-grid', label: 'Grille à 3 colonnes',
  });
  wp.blocks.registerBlockStyle('core/group', {
    name: '4-columns-grid', label: 'Grille à 4 colonnes',
  });
  wp.blocks.registerBlockStyle('core/group', {
    name: '5-columns-grid', label: 'Grille à 5 colonnes',
  });
  wp.blocks.registerBlockStyle('core/group', {
    name: 'galery-grid', label: 'Grille galerie',
  });
  wp.blocks.registerBlockStyle('core/group', {
    name: 'horizontal-mobile', label: 'Horizontal mobile',
  });
  wp.blocks.registerBlockStyle('core/group', {
    name: 'align-start-mobile', label: 'Aligné gauche mobile"',
  });
});

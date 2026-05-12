import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/buttons', {
    name: 'centered-mobile', label: 'Centré mobile',
  });
  wp.blocks.registerBlockStyle('core/buttons', {
    name: 'left-mobile', label: 'Aligné à gauche mobile',
  });
});

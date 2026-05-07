import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/buttons', {
    name: 'centered-mobile', label: 'Centré mobile',
  });
});

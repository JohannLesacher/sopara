import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/heading', {
    name: 'centered-mobile', label: 'Centré mobile',
  });
});

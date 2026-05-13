import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/paragraph', {
    name: 'outline',
    label: 'Contour',
  });
});

import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/list', {
    name: 'compact', label: 'Compacte',
  });
});

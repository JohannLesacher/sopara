import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/table', {
    name: 'emetteurs', label: 'Emetteurs',
  });
});

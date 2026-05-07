import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/columns', {
    name: 'inverted-mobile', label: 'Inversé mobile',
  });
});

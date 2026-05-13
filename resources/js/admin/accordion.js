import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/accordion', {
    name: 'with-icon', label: 'Avec icône',
  });
});

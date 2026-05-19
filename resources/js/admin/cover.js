import domReady from '@wordpress/dom-ready';

domReady(() => {
  wp.blocks.registerBlockStyle('core/cover', {
    name: 'hero-banner', label: 'Hero Banner',
  });
});

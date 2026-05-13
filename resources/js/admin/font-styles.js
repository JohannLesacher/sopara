import domReady from '@wordpress/dom-ready';

const fontStyles = [
  'S0.875-7-1.5-AA',
  'S1-7-1.2-AA',
  'S1.5-7-1.3-AA',
  'S2-7-1.2-AA',
  'S2-7-1.3-AA',
  'F0.875-7-1.5-AA',
  'F1-4-1.5',
  'F1.125-4-1.5',
  'F1.125-5-1.5',
  'F1.125-6-1.5',
  'F1.125-7-1.5',
  'F1.250-7-1.5',
  'F1.500-7-1.3',
  'F2.250-7-1.2',
];

const toClassName = (style) => style.replace(/\./g, '_').toLowerCase();

const blocks = ['core/paragraph', 'core/heading', 'core/accordion-heading', 'core/list-item'];

domReady(() => {
  blocks.forEach((block) => {
    fontStyles.forEach((style) => {
      wp.blocks.registerBlockStyle(block, {
        name: toClassName(style),
        label: style,
      });
    });
  })
});

import domReady from '@wordpress/dom-ready';

const fontStyles = [
  ['S0.875-7-1.5-AA', 'Sync XS 1.5'],
  ['S1-7-1.2-AA', 'Sync S 1.2'],
  ['S1.5-7-1.3-AA', 'Sync M 1.3'],
  ['S2-7-1.2-AA', 'Sync L 1.2'],
  ['S2-7-1.3-AA', 'Sync L 1.3'],
  ['F0.875-7-1.5-AA', 'Fig XS Gras'],
  ['F1-4-1.5', 'Fig S Norm'],
  ['F1.125-4-1.5', 'Fig M Norm'],
  ['F1.125-5-1.5', 'Fig M Med'],
  ['F1.125-6-1.5', 'Fig M Semi'],
  ['F1.125-7-1.5', 'Fig M Gras'],
  ['F1.250-7-1.5', 'Fig L Gras'],
  ['F1.500-7-1.3', 'Fig XL Gras'],
  ['F2.250-7-1.2', 'Fig XXL Gras'],
];

const toClassName = (style) => style.replace(/\./g, '_').toLowerCase();

const blocks = ['core/paragraph', 'core/heading', 'core/accordion-heading', 'core/list-item'];

domReady(() => {
  blocks.forEach((block) => {
    fontStyles.forEach(([name, label]) => {
      wp.blocks.registerBlockStyle(block, {
        name: toClassName(name),
        label,
      });
    });
  });
});

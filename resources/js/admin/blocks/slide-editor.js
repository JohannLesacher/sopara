import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createElement as el } from '@wordpress/element';

registerBlockType('sur-mesure/slide', {
  title: 'Slide',
  category: 'sur-mesure',
  icon: 'media-interactive',
  parent: ['sur-mesure/slider'],
  attributes: {},

  edit: () => {
    const blockProps = useBlockProps({ className: 'editor-slide' });
    return el('div', blockProps, el(InnerBlocks, {}));
  },

  save: () => el(InnerBlocks.Content, null),
});

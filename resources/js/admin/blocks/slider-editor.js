import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createElement as el } from '@wordpress/element';

registerBlockType('sur-mesure/slider', {
  title: 'Slider',
  category: 'sur-mesure',
  icon: 'slides',
  attributes: {},
  supports: {
    align: ['wide', 'full'],
  },
  edit: () => {
    const blockProps = useBlockProps({ className: 'editor-slider' });
    return el(
      'div',
      blockProps,
      el(InnerBlocks, { allowedBlocks: ['sur-mesure/slide'] }),
    );
  },

  save: () => el(InnerBlocks.Content, null),
});

import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createElement as el } from '@wordpress/element';

registerBlockType('sur-mesure/tabs', {
  apiVersion: 3,
  title: 'Tabs',
  icon: 'images-alt2',
  category: 'common',

  edit: () => {
    const blockProps = useBlockProps();
    return el(
      'div',
      blockProps,
      el(InnerBlocks, {
        allowedBlocks: ['sur-mesure/tab'],
        template: [['sur-mesure/tab']],
        templateLock: false,
      }),
    );
  },

  save: () => el(InnerBlocks.Content, null),
});

import {registerBlockType} from '@wordpress/blocks';
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';
import {createElement as el} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

registerBlockType('sur-mesure/tabs', {
  apiVersion: 3,
  title: 'Tabs',
  icon: 'images-alt2',
  category: 'common',
  supports: {
    align: ['wide', 'full'],
  },

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


domReady(() => {
  wp.blocks.registerBlockStyle('sur-mesure/tabs', {
    name: 'pills', label: 'Boutons',
  });
});

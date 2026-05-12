import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createElement as el } from '@wordpress/element';

registerBlockType('sur-mesure/slide', {
  apiVersion: 3,
  title: 'Slide',
  category: 'sur-mesure',
  icon: 'media-interactive',
  parent: ['sur-mesure/slider'],
  attributes: {},

  edit: ({attributes}) => {
    const className = ['editor-slide', attributes.animateOnScroll && 'is-animated']
      .filter(Boolean)
      .join(' ');
    const blockProps = useBlockProps({ className });
    return el('div', blockProps, el(InnerBlocks, {}));
  },

  save: () => el(InnerBlocks.Content, null),
});

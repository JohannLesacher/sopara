import { registerBlockType } from '@wordpress/blocks';
import {
  InnerBlocks,
  useBlockProps,
  InspectorControls,
} from '@wordpress/block-editor';
import { createElement as el, Fragment } from '@wordpress/element';
import { PanelBody, RangeControl, ToggleControl } from '@wordpress/components';

registerBlockType('sur-mesure/slider', {
  title: 'Slider',
  category: 'sur-mesure',
  icon: 'slides',
  attributes: {
    perPage: { type: 'number', default: 3 },
    loop: { type: 'boolean', default: false },
    autoplay: { type: 'boolean', default: false },
  },
  supports: {
    align: ['wide', 'full'],
  },

  edit: ({ attributes, setAttributes }) => {
    const blockProps = useBlockProps({ className: 'editor-slider' });
    return el(
      Fragment,
      {},
      el(
        InspectorControls,
        {},
        el(
          PanelBody,
          { title: 'Disposition', initialOpen: true },
          el(RangeControl, {
            label: 'Slides par page',
            value: attributes.perPage,
            onChange: (val) => setAttributes({ perPage: val }),
            min: 1,
            max: 6,
          }),
          el(ToggleControl, {
            label: 'Boucle infinie',
            checked: attributes.loop,
            onChange: (val) => setAttributes({ loop: val }),
          }),
          el(ToggleControl, {
            label: 'Lecture automatique',
            checked: attributes.autoplay,
            onChange: (val) => setAttributes({ autoplay: val }),
          }),
        ),
      ),
      el(
        'div',
        blockProps,
        el(InnerBlocks, { allowedBlocks: ['sur-mesure/slide'] }),
      ),
    );
  },

  save: () => el(InnerBlocks.Content, null),
});

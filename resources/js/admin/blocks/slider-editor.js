import { registerBlockType } from '@wordpress/blocks';
import {
  InnerBlocks,
  useBlockProps,
  InspectorControls,
} from '@wordpress/block-editor';
import { createElement as el, Fragment } from '@wordpress/element';
import { PanelBody, RangeControl, ToggleControl } from '@wordpress/components';

registerBlockType('sur-mesure/slider', {
  apiVersion: 3,
  title: 'Slider',
  category: 'sur-mesure',
  icon: 'slides',
  attributes: {
    perPage: { type: 'number', default: 3 },
    perPageTablet: { type: 'number' },
    perPageMobile: { type: 'number' },
    loop: { type: 'boolean', default: false },
    arrows: { type: 'boolean', default: false },
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
            label: 'Slides par page (desktop)',
            value: attributes.perPage,
            onChange: (val) => setAttributes({ perPage: val }),
            min: 1,
            max: 6,
          }),
          el(RangeControl, {
            label: 'Slides par page (tablette ≤1024px)',
            value:
              attributes.perPageTablet ?? Math.max(1, attributes.perPage - 1),
            onChange: (val) => setAttributes({ perPageTablet: val }),
            min: 1,
            max: 6,
          }),
          el(RangeControl, {
            label: 'Slides par page (mobile ≤640px)',
            value:
              attributes.perPageMobile ?? Math.max(1, attributes.perPage - 2),
            onChange: (val) => setAttributes({ perPageMobile: val }),
            min: 1,
            max: 6,
          }),
          el(ToggleControl, {
            label: 'Boucle infinie',
            checked: attributes.loop,
            onChange: (val) => setAttributes({ loop: val }),
          }),
          el(ToggleControl, {
            label: 'Flèches',
            checked: attributes.arrows,
            onChange: (val) => setAttributes({ arrows: val }),
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

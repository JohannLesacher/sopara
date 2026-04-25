import {addFilter} from '@wordpress/hooks';
import {createHigherOrderComponent} from '@wordpress/compose';
import {InspectorControls} from '@wordpress/block-editor';
import {createElement as el, Fragment} from '@wordpress/element';
import {__experimentalUnitControl as UnitControl, PanelBody,} from '@wordpress/components';

addFilter(
  'blocks.registerBlockType',
  'theme/masked-image-width',
  (settings, name) => {
    if (name !== 'meta-box/masked-image') return settings;

    settings.attributes = {
      ...settings.attributes,
      width: {type: 'string'},
    };

    return settings;
  },
);

const withMaskedImageWidth = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    if (props.name !== 'meta-box/masked-image') {
      return el(BlockEdit, props);
    }

    return el(
      Fragment,
      {},
      el(BlockEdit, props),
      el(
        InspectorControls,
        {},
        el(
          PanelBody,
          {title: 'Dimensions', initialOpen: true},
          el(UnitControl, {
            label: 'Largeur',
            value: props.attributes.width || '',
            onChange: (val) => props.setAttributes({width: val || undefined}),
          }),
        ),
      ),
    );
  },
  'withMaskedImageWidth',
);

addFilter(
  'editor.BlockEdit',
  'theme/masked-image-width-control',
  withMaskedImageWidth,
);

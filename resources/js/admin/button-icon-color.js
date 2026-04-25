import domReady from '@wordpress/dom-ready';
import {addFilter} from '@wordpress/hooks';
import {createHigherOrderComponent} from '@wordpress/compose';
import {
  __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
  __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
  InspectorControls,
} from '@wordpress/block-editor';
import {createElement as el, Fragment} from '@wordpress/element';
import {registerBlockStyle} from '@wordpress/blocks';

addFilter(
  'blocks.registerBlockType',
  'theme/button-icon-color',
  (settings, name) => {
    if (name !== 'core/button') return settings;

    settings.attributes = {
      ...settings.attributes,
      iconColor: {type: 'string'},
    };

    return settings;
  },
);

const withIconColorControl = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    if (props.name !== 'core/button') {
      return el(BlockEdit, props);
    }

    const hasIconStyle = (props.attributes.className || '').includes(
      'is-style-with-icon',
    );

    return el(
      Fragment,
      {},
      el(BlockEdit, props),
      hasIconStyle &&
      el(
        InspectorControls,
        {group: 'color'},
        el(ColorGradientSettingsDropdown, {
          settings: [
            {
              colorValue: props.attributes.iconColor,
              onColorChange: (color) =>
                props.setAttributes({iconColor: color}),
              label: 'Icône',
              isShownByDefault: true,
              hasValue: () => !!props.attributes.iconColor,
              onDeselect: () => props.setAttributes({iconColor: undefined}),
              resetAllFilter: () => ({iconColor: undefined}),
              clearable: true,
            },
          ],
          panelId: props.clientId,
          ...useMultipleOriginColorsAndGradients(),
        }),
      ),
    );
  },
  'withIconColorControl',
);

addFilter(
  'editor.BlockEdit',
  'theme/button-icon-color-control',
  withIconColorControl,
);

domReady(() => {
  registerBlockStyle('core/button', {
    name: 'with-icon',
    label: 'Avec icône',
  });
});

import {addFilter} from '@wordpress/hooks';
import {createHigherOrderComponent} from '@wordpress/compose';
import {
  __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
  __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
  getColorObjectByColorValue,
  InspectorControls,
} from '@wordpress/block-editor';
import {createElement as el, Fragment} from '@wordpress/element';

const BLOCK_NAME = 'meta-box/key-figure';
const CLEARED_COLOR = {valueColor: '', customValueColor: undefined};

addFilter(
  'blocks.registerBlockType',
  'theme/key-figure-value-color',
  (settings, name) => {
    if (name !== BLOCK_NAME) return settings;

    settings.attributes = {
      ...settings.attributes,
      valueColor: {type: 'string', default: 'primary-0'},
      customValueColor: {type: 'string'},
    };

    return settings;
  },
);

const withValueColorControl = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    if (props.name !== BLOCK_NAME) {
      return el(BlockEdit, props);
    }

    const colorSettings = useMultipleOriginColorsAndGradients();
    const palette = colorSettings.colors.flatMap((origin) => origin.colors);
    const {valueColor, customValueColor} = props.attributes;
    const currentColor = customValueColor || palette.find((color) => color.slug === valueColor)?.color;

    const onColorChange = (color) => {
      const paletteColor = getColorObjectByColorValue(palette, color);

      props.setAttributes({
        valueColor: paletteColor?.slug ?? '',
        customValueColor: paletteColor || !color ? undefined : color,
      });
    };

    return el(
      Fragment,
      {},
      el(BlockEdit, props),
      el(
        InspectorControls,
        {group: 'color'},
        el(ColorGradientSettingsDropdown, {
          settings: [
            {
              colorValue: currentColor,
              onColorChange,
              label: 'Chiffre',
              isShownByDefault: true,
              hasValue: () => !!currentColor,
              onDeselect: () => props.setAttributes(CLEARED_COLOR),
              resetAllFilter: () => CLEARED_COLOR,
              clearable: true,
            },
          ],
          panelId: props.clientId,
          ...colorSettings,
        }),
      ),
    );
  },
  'withValueColorControl',
);

addFilter(
  'editor.BlockEdit',
  'theme/key-figure-value-color-control',
  withValueColorControl,
);

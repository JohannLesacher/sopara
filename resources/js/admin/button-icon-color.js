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
import {
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
  __experimentalToolsPanel as ToolsPanel,
  __experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';

addFilter(
  'blocks.registerBlockType',
  'theme/button-icon-color',
  (settings, name) => {
    if (name !== 'core/button') return settings;

    settings.attributes = {
      ...settings.attributes,
      iconColor: {type: 'string'},
      iconPosition: {type: 'string', default: 'right'},
    };

    return settings;
  },
);

const withIconColorControl = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    if (props.name !== 'core/button') {
      return el(BlockEdit, props);
    }

    const hasIconStyle = (
      (props.attributes.className || '').includes('is-style-with-icon',) ||
      (props.attributes.className || '').includes('is-style-border-with-icon')
    );

    const handlePositionChange = (val) => {
      const current = props.attributes.className || '';
      const cleaned = current.replace(/\bhas-icon-left\b/g, '').trim();
      props.setAttributes({
        iconPosition: val,
        className: val === 'left' ? `${cleaned} has-icon-left`.trim() : cleaned,
      });
    };

    return el(
      Fragment,
      {},
      el(BlockEdit, props),
      hasIconStyle &&
      el(
        InspectorControls,
        {group: 'styles'},
        el(
          ToolsPanel,
          {
            label: "Position de l'icône",
            resetAll: () => handlePositionChange('right'),
          },
          el(
            ToolsPanelItem,
            {
              label: 'Position',
              isShownByDefault: true,
              hasValue: () =>
                (props.attributes.iconPosition || 'right') !== 'right',
              onDeselect: () => handlePositionChange('right'),
            },
            el(
              ToggleGroupControl,
              {
                label: 'Position',
                value: props.attributes.iconPosition || 'right',
                onChange: handlePositionChange,
                isBlock: true,
                __nextHasNoMarginBottom: true,
              },
              el(ToggleGroupControlOption, {
                value: 'left',
                label: 'Gauche',
              }),
              el(ToggleGroupControlOption, {
                value: 'right',
                label: 'Droite',
              }),
            ),
          ),
        ),
      ),
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
    label: 'Plein avec icône',
  });

  registerBlockStyle('core/button', {
    name: 'border-with-icon',
    label: 'Contour avec icône',
  });
});

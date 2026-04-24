import domReady from '@wordpress/dom-ready';
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls, __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown, __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients } from '@wordpress/block-editor';
import { __experimentalToolsPanelItem as ToolsPanelItem } from '@wordpress/components';
import { Fragment, createElement as el } from '@wordpress/element';
import { registerBlockStyle } from '@wordpress/blocks';
import '@scripts/admin/animation';


addFilter('blocks.registerBlockType', 'theme/button-icon-color', (settings, name) => {
  if (name !== 'core/button') return settings;

  settings.attributes = {
    ...settings.attributes,
    iconColor: { type: 'string' },
  };

  return settings;
});

const withIconColorControl = createHigherOrderComponent((BlockEdit) => (props) => {
  if (props.name !== 'core/button') {
    return el(BlockEdit, props);
  }

  const hasIconStyle = (props.attributes.className || '').includes('is-style-with-icon');

  return el(Fragment, {},
    el(BlockEdit, props),
    hasIconStyle && el(InspectorControls, { group: 'color' },
      el(ColorGradientSettingsDropdown, {
        settings: [{
          colorValue: props.attributes.iconColor,
          onColorChange: (color) => props.setAttributes({ iconColor: color }),
          label: 'Icône',
          isShownByDefault: true,
          hasValue: () => !!props.attributes.iconColor,
          onDeselect: () => props.setAttributes({ iconColor: undefined }),
          resetAllFilter: () => ({ iconColor: undefined }),
        }],
        panelId: props.clientId,
        ...useMultipleOriginColorsAndGradients(),
      })
    )
  );
}, 'withIconColorControl');

addFilter('editor.BlockEdit', 'theme/button-icon-color-control', withIconColorControl);

domReady(() => {
  registerBlockStyle('core/button', {
    name: 'with-icon',
    label: 'Avec icône',
  });
});

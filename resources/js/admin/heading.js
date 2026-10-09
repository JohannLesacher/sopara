import domReady from '@wordpress/dom-ready';
import { addFilter } from '@wordpress/hooks';
import {registerResponsiveOption} from "./responsive-panel.js";

addFilter(
  'blocks.registerBlockType',
  'sopara/heading-post-levels',
  (settings, name) => {
    if (name !== 'core/heading' || window.typenow !== 'post' || !settings.attributes?.levelOptions) return settings;

    settings.attributes = {
      ...settings.attributes,
      levelOptions: { ...settings.attributes.levelOptions, default: [2, 3, 4] },
    };

    return settings;
  }
);

domReady(() => {
  wp.blocks.registerBlockStyle('core/heading', {
    name: 'centered-mobile', label: 'Centré mobile',
  });

  registerResponsiveOption('core/heading', {
    name: 'mobile-center',
    label: 'Centré sur mobile',
  });
});

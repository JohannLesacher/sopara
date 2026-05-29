import domReady from '@wordpress/dom-ready';
import {registerResponsiveOption} from "./responsive-panel.js";

domReady(() => {
  wp.blocks.registerBlockStyle('core/columns', {
    name: 'inverted-mobile', label: 'Inversé mobile',
  });

  wp.blocks.registerBlockStyle('core/columns', {
    name: 'linked', label: 'Liés',
  });

  registerResponsiveOption('core/columns', {
    name: 'mobile-slider',
    label: 'Slider mobile',
  });

  registerResponsiveOption('core/columns', {
    name: 'hidden',
    label: 'Caché sur mobile',
  });
});

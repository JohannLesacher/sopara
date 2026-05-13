import domReady from '@wordpress/dom-ready';
import {registerResponsiveOption} from "./responsive-panel.js";

domReady(() => {
  wp.blocks.registerBlockStyle('core/heading', {
    name: 'centered-mobile', label: 'Centré mobile',
  });

  registerResponsiveOption('core/heading', {
    name: 'mobile-center',
    label: 'Centré sur mobile',
  });
});

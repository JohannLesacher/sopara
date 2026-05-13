import domReady from '@wordpress/dom-ready';
import {registerResponsiveOption} from "./responsive-panel.js";

domReady(() => {
  wp.blocks.registerBlockStyle('core/buttons', {
    name: 'centered-mobile', label: 'Centré mobile',
  });

  registerResponsiveOption('core/buttons', {
    name: 'centered-mobile',
    label: 'Centré mobile',
  });

  wp.blocks.registerBlockStyle('core/buttons', {
    name: 'left-mobile', label: 'Aligné à gauche mobile',
  });
});

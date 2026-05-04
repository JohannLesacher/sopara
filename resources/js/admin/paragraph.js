import domReady from '@wordpress/dom-ready';
import {registerBlockStyle} from '@wordpress/blocks';

domReady(() => {
  console.log('paragraph');
  registerBlockStyle('core/paragraph', {
    name: 'outline',
    label: 'Contour',
  });
});

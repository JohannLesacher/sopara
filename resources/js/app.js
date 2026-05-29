import { initHeader } from './global/header.js';
import { initMegamenu } from './global/megamenu';
import { initHorizontalScroll } from './global/horizontal-scroll.js';
import { initGroupHorizontalScroll } from './global/group-horizontal-scroll.js';
import { initSliderColumns } from './global/slider-columns.js';
import './global/animation.js';
import './global/langswitcher.js';
import './global/secteurs.js';
import './global/sync-heights.js';

import.meta.glob(['../images/**', '../fonts/**']);

document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  initMegamenu();
  initGroupHorizontalScroll();
  initHorizontalScroll();
  initSliderColumns();
});

window.addEventListener('resize', initHorizontalScroll);

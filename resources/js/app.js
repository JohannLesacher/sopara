import {initHeader} from './global/header.js';
import {initMegamenu} from './global/megamenu';
import {initHorizontalScroll} from './global/horizontal-scroll.js';
import {initSliderColumns} from './global/slider-columns.js';
import './global/animation.js';
import './global/langswitcher.js';
import './global/secteurs.js';
import './global/sync-heights.js';
import {initHeroBannerMaxWidth} from "./blocks/cover.js";

import.meta.glob(['../images/**', '../fonts/**']);

document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  initMegamenu();
  // initGroupHorizontalScroll();
  initHorizontalScroll();
  initSliderColumns();
  initHeroBannerMaxWidth();
});

window.addEventListener('resize', initHorizontalScroll);

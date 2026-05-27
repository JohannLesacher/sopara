import { initHeader } from './global/header.js';
import { initMegamenu } from './global/megamenu';
import './global/animation.js';
import './global/langswitcher.js';
import './global/secteurs.js';
import './global/sync-heights.js';

import.meta.glob(['../images/**', '../fonts/**']);

document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  initMegamenu();
});

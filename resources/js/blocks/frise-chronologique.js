import Splide from '@splidejs/splide';
import { HorizontalScroll } from '../global/horizontal-scroll.js';

const DESKTOP = window.matchMedia('(min-width: 1000px)');

function debounce(fn, wait) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), wait);
  };
}

function el(tag, className) {
  const node = document.createElement(tag);
  if (className) node.className = className;
  return node;
}

class Frise {
  constructor(root) {
    this.root = root;
    this.type = root.dataset.friseType; // 'scroll' | 'slider'
    this.etapes = [...root.querySelector('[data-frise-etapes]').children];
    this.arrowsTemplate = root.querySelector('[data-frise-arrows]');

    this.mode = null;
    this.splide = null;
    this.scroll = null;
    this.observer = null;
    this.container = null;

    this.apply = this.apply.bind(this);
    DESKTOP.addEventListener('change', this.apply);

    // Mode scroll : HorizontalScroll gère son propre resize.
    // Mode slider : Splide ne recalcule pas le padding lu en JS (rem → px),
    // d'où le refresh manuel sur resize/zoom.
    this.onResize = debounce(() => {
      if (this.mode === 'slider' && this.splide) {
        this.splide.options = {
          padding: { left: this.sliderPadding(), right: this.sliderPadding() },
        };
        this.splide.refresh();
      }
    }, 150);
    window.addEventListener('resize', this.onResize);

    this.apply();
  }

  sliderPadding() {
    return (
      parseFloat(
        getComputedStyle(this.root).getPropertyValue('--frise-padding-start'),
      ) || 0
    );
  }

  // Mode rendu réel : 'slider' toujours sous 1000px ; 'scroll' réservé au desktop.
  desiredMode() {
    if (this.type === 'slider') return 'slider';
    return DESKTOP.matches ? 'scroll' : 'slider';
  }

  apply() {
    const next = this.desiredMode();
    if (next === this.mode) return;

    this.teardown();

    // Classe posée avant le build : la largeur des étapes en mode scroll
    // dépend de `.block-frise-chronologique--scroll`, et HorizontalScroll
    // mesure dès son instanciation.
    this.root.classList.toggle(
      'block-frise-chronologique--scroll',
      next === 'scroll',
    );
    this.root.classList.toggle(
      'block-frise-chronologique--slider',
      next === 'slider',
    );

    next === 'scroll' ? this.buildScroll() : this.buildSlider();
    this.mode = next;
  }

  teardown() {
    if (this.splide) {
      this.splide.destroy(true);
      this.splide = null;
    }
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
    }
    if (this.scroll) {
      this.scroll.destroy();
      this.scroll = null;
    }
    if (this.container) {
      this.container.remove();
      this.container = null;
    }
  }

  buildSlider() {
    const splideEl = el('div', 'block-frise-chronologique__splide splide');
    const track = el('div', 'splide__track');
    const list = el('ul', 'splide__list');

    this.etapes.forEach((etape) => {
      const slide = el('li', 'splide__slide');
      slide.appendChild(etape);
      list.appendChild(slide);
    });

    track.appendChild(list);
    splideEl.appendChild(track);
    if (this.arrowsTemplate) {
      splideEl.appendChild(this.arrowsTemplate.content.cloneNode(true));
    }
    this.root.appendChild(splideEl);
    this.container = splideEl;

    const padding = this.sliderPadding();

    this.splide = new Splide(splideEl, {
      type: 'slide',
      perPage: 3,
      perMove: 1,
      gap: '0px',
      arrows: true,
      pagination: false,
      padding: { left: padding, right: padding },
      speed: 600,
      easing: 'cubic-bezier(0.34, 1.25, 0.64, 1)',
      breakpoints: {
        1299: { perPage: 2 },
        999: { perPage: 1, padding: { left: padding, right: padding } },
      },
    });

    this.splide.mount();
  }

  buildScroll() {
    const hs = el('div', 'horizontal-scroll');
    const wrapper = el('div', 'horizontal-scroll__wrapper');
    const view = el('div', 'horizontal-scroll__view sync-element-heights');
    const scroller = el('div', 'horizontal-scroll__scroller');

    wrapper.setAttribute('js-horizontal-scroll_wrapper', '');
    view.setAttribute('js-horizontal-scroll_view', '');
    scroller.setAttribute('js-horizontal-scroll_scroller', '');

    this.etapes.forEach((etape) => view.appendChild(etape));
    wrapper.appendChild(view);
    hs.appendChild(wrapper);
    hs.appendChild(scroller);
    this.root.appendChild(hs);
    this.container = hs;

    this.scroll = new HorizontalScroll(hs);
    this.observer = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        entry.isIntersecting ? this.scroll.attach() : this.scroll.detach();
      }
    });
    this.observer.observe(hs);
  }
}

function init() {
  document.querySelectorAll('[data-frise]').forEach((root) => new Frise(root));
}

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', init)
  : init();

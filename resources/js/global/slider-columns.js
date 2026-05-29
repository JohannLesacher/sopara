import Splide from '@splidejs/splide';

function debounce(fn, wait) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), wait);
  };
}

class SliderColumns {
  static SELECTOR = '.wp-block-columns.is-responsive-mobile-slider';
  static MEDIA_QUERY = '(max-width: 768px)';

  constructor(element) {
    this.el = element;
    this.splide = null;
  }

  get isMounted() {
    return this.splide !== null;
  }

  mount() {
    if (this.isMounted) return;

    const root = this.transformMarkup();

    requestAnimationFrame(() => {
      if (!document.body.contains(root)) return;

      const slides = root.querySelectorAll('.splide__slide > *');
      slides.forEach((slide, index) => {
        slide.dataset.index = index + 1;
      });

      this.splide = new Splide(root, {
        gap: '1rem',
        pagination: false,
        padding: '1.5rem',
        easing: 'cubic-bezier(0.34, 1.25, 0.64, 1)',
      });

      this.splide.mount();
    });
  }

  unmount() {
    if (!this.isMounted) return;

    this.splide.destroy();
    this.splide = null;
    this.restoreMarkup();
  }

  sync() {
    const isMobile = window.matchMedia(SliderColumns.MEDIA_QUERY).matches;
    if (isMobile && !this.isMounted) this.mount();
    else if (!isMobile && this.isMounted) this.unmount();
  }

  destroy() {
    this.unmount();
  }

  getSlideSelector() {
    return this.el.classList.contains('wp-block-columns')
      ? '.wp-block-column'
      : '.wp-block-group';
  }

  transformMarkup() {
    const slideSelector = this.getSlideSelector();
    const items = this.el.querySelectorAll(`:scope > ${slideSelector}`);

    const root = document.createElement('div');
    root.classList.add('splide');

    const track = document.createElement('div');
    track.classList.add('splide__track');

    const list = document.createElement('ul');
    list.classList.add('splide__list');

    items.forEach((item) => {
      const slide = document.createElement('li');
      slide.classList.add('splide__slide');
      slide.appendChild(item);
      list.appendChild(slide);
    });

    track.appendChild(list);
    root.appendChild(track);
    root.insertAdjacentHTML('beforeend', this.getArrowsMarkup());

    this.el.innerHTML = '';
    this.el.appendChild(root);

    return root;
  }

  restoreMarkup() {
    const slideSelector = this.getSlideSelector();
    const root = this.el.querySelector('.splide');
    if (!root) return;

    const items = root.querySelectorAll(`.splide__slide > ${slideSelector}`);
    items.forEach((item) => this.el.appendChild(item));
    root.remove();
  }

  getArrowsMarkup() {
    return `
      <div class="splide__arrows">
        <button
          class="splide__arrow splide__arrow--prev"
          type="button"
          aria-label="Previous slide"
          aria-controls="splide01-track"
        >
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 16H27" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M18 7L27 16L18 25" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <button
          class="splide__arrow splide__arrow--next"
          type="button"
          aria-label="Next slide"
          aria-controls="splide01-track"
        >
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 16H27" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M18 7L27 16L18 25" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
    `;
  }
}

export function initSliderColumns() {
  const elements = document.querySelectorAll(SliderColumns.SELECTOR);
  if (!elements.length) return;

  const instances = new Map();

  for (const el of elements) {
    const instance = new SliderColumns(el);
    instances.set(el, instance);
    instance.sync();
  }

  const onResize = debounce(() => {
    for (const instance of instances.values()) instance.sync();
  }, 200);

  window.addEventListener('resize', onResize);

  return () => {
    window.removeEventListener('resize', onResize);
    for (const instance of instances.values()) instance.destroy();
    instances.clear();
  };
}

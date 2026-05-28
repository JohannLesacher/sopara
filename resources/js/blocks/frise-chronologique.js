import Splide from '@splidejs/splide';

class FriseSlider {
  static SELECTOR =
    '.block-frise-chronologique--slider .block-frise-chronologique__splide';

  static getSlidePadding(el) {
    return parseFloat(
      getComputedStyle(
        el.closest('.block-frise-chronologique'),
      ).getPropertyValue('--frise-padding-start'),
    );
  }

  static init() {
    const containers = document.querySelectorAll(FriseSlider.SELECTOR);

    containers.forEach((container) => {
      const slidePadding = FriseSlider.getSlidePadding(container) ?? 0;

      const splide = new Splide(container, {
        type: 'slide',
        perPage: 3,
        perMove: 1,
        gap: '0px',
        arrows: true,
        pagination: false,
        padding: { left: slidePadding, right: slidePadding },
        speed: 600,
        easing: 'cubic-bezier(0.34, 1.25, 0.64, 1)',
        breakpoints: {
          1299: {
            perPage: 2,
          },
          999: {
            perPage: 1,
            padding: { left: slidePadding, right: slidePadding },
          },
        },
      });

      splide.mount();
    });
  }
}

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', () => FriseSlider.init())
  : FriseSlider.init();

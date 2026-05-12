import Splide from '@splidejs/splide';
import { AutoScroll } from '@splidejs/splide-extension-auto-scroll';
import { Intersection } from '@splidejs/splide-extension-intersection';

class Slider {
  static getSlidePadding(el) {
    return parseFloat(
      getComputedStyle(el).getPropertyValue('--splide-padding-start'),
    );
  }

  static init() {
    const containers = document.querySelectorAll('.block-slider');

    containers.forEach((container) => {
      const slidePadding = Slider.getSlidePadding(container) ?? 0;

      const dataSplide = JSON.parse(container.dataset.splide || '{}');
      const perPage = dataSplide.perPage ?? 3;
      const perPageTablet =
        dataSplide.perPageTablet ?? Math.max(1, perPage - 1);
      const perPageMobile =
        dataSplide.perPageMobile ?? Math.max(1, perPage - 2);
      const autoplay = dataSplide.autoplay ?? false;

      const splide = new Splide(container, {
        perMove: 1,
        gap: '1rem',
        // wheel: true,
        // releaseWheel: true,
        arrows: false,
        pagination: false,
        padding: { left: slidePadding, right: slidePadding },
        speed: 600,
        easing: 'cubic-bezier(0.34, 1.25, 0.64, 1)',
        breakpoints: {
          1024: {
            perPage: perPageTablet,
            padding: { left: slidePadding, right: slidePadding * 2 },
          },
          640: {
            perPage: perPageMobile,
            padding: { left: slidePadding, right: slidePadding * 3 },
          },
        },
        ...(autoplay && {
          intersection: {
            inView: { autoplay: true },
          },
          autoScroll: {
            speed: 0.5,
          },
        }),
      });

      splide.on('overflow', (isOverflow) => {
        splide.options = {
          drag: isOverflow,
          clones: isOverflow ? undefined : 0,
        };

        const list = splide.root.querySelector('.splide__list');
        list.style.justifyContent = isOverflow ? '' : 'center';
      });

      splide.mount(autoplay ? { AutoScroll, Intersection } : {});
    });
  }
}

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', () => Slider.init())
  : Slider.init();

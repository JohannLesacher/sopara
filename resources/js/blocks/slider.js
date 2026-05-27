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

      const perPage = Number(container.dataset.perPage ?? 3);
      const perPageTablet = Number(
        container.dataset.perPageTablet ?? Math.max(1, perPage - 1),
      );
      const perPageMobile = Number(
        container.dataset.perPageMobile ?? Math.max(1, perPage - 2),
      );
      const autoplay = container.dataset.autoplay === 'true';
      const arrows = container.dataset.arrows === 'true';
      const loop = container.dataset.loop === 'true';

      const splide = new Splide(container, {
        type: loop ? 'loop' : 'slide',
        perPage,
        perMove: 1,
        gap: '1rem',
        arrows,
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
          type: 'loop',
          drag: 'free',
          focus: 'center',
          intersection: {
            inView: {
              autoScroll: {
                speed: 0.5,
              },
            },
          },
        }),
      });

      splide.on('mounted', () => {
        const headings = splide.root.querySelectorAll(
          '.splide__slide--clone :is(h1, h2, h3, h4, h5, h6)',
        );

        headings.forEach((heading) => {
          const div = document.createElement('div');
          div.innerHTML = heading.innerHTML;
          [...heading.attributes].forEach((attr) =>
            div.setAttribute(attr.name, attr.value),
          );
          heading.replaceWith(div);
        });
      });

      splide.on('overflow', (isOverflow) => {
        if (autoplay) return;

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

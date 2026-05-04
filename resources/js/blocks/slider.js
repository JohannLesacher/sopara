import Splide from '@splidejs/splide';

class Slider {
  static init() {
    const containers = document.querySelectorAll('.block-slider');

    containers.forEach(container => {
      const splide = new Splide(container, {
        type: 'slide',
        perPage: 3,
        perMove: 1,
        gap: '1rem',
        wheel: true,
        arrows: false,
        pagination: false,
        breakpoints: {
          1024: { perPage: 2 },
          640: { perPage: 1 }
        }
      });

      splide.on('overflow', (isOverflow) => {
        splide.options = {
          drag: isOverflow,
          clones: isOverflow ? undefined : 0,
        };

        const list = splide.root.querySelector('.splide__list');
        list.style.justifyContent = isOverflow ? '' : 'center';
      });

      splide.mount();
    });
  }
}

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', () => Slider.init())
  : Slider.init();

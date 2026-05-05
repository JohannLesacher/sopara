import {animate, svg} from 'animejs';

document.addEventListener('DOMContentLoaded', () => {
  const observerOptions = {
    rootMargin: '0px 0px -50px 0px',
    threshold: 0.1
  };

  let visibleIndex = 0;
  const stagger = 150;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = visibleIndex * stagger;
        visibleIndex++;

        setTimeout(() => {
          entry.target.classList.add('is-visible');
        }, delay);

        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.is-animated').forEach(el => observer.observe(el));
});

/* Button Icon Animation */
const buttonIconShapeAnimation = () => {
  const buttons = document.querySelectorAll('.wp-block-button.is-style-with-icon, .wp-block-button.is-style-border-with-icon');
  if (buttons.length === 0) return;

  buttons.forEach((button, index) => {
    const path = button.querySelector('.wp-block-button__link svg path');
    if (!path) return;

    // Create hidden SVG holding the original shape for morphTo to reference
    const originalId = `morph-original-${index}`;
    const hiddenSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    hiddenSvg.style.display = 'none';
    const hiddenPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    hiddenPath.setAttribute('id', originalId);
    hiddenPath.setAttribute('d', path.getAttribute('d'));
    hiddenSvg.appendChild(hiddenPath);
    document.body.appendChild(hiddenSvg);

    button.addEventListener('mouseenter', () => {
      animate(path, {
        d: svg.morphTo('#morphButtonIconShape path', 3),
        duration: 300,
        ease: 'inOutCirc',
      });
    });

    button.addEventListener('mouseleave', () => {
      animate(path, {
        d: svg.morphTo(`#${originalId}`, 3),
        duration: 300,
        ease: 'inOutCirc',
      });
    });
  });
};

document.addEventListener('DOMContentLoaded', buttonIconShapeAnimation);

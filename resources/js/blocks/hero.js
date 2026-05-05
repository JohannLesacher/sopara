import {animate, stagger} from 'animejs';

class HeroSecteurs {
  constructor(secteurs) {
    this.secteurs = secteurs;
    this.hero = secteurs.previousElementSibling?.classList.contains('block-hero')
      ? secteurs.previousElementSibling
      : null;
    this.images = [...secteurs.querySelectorAll('.block-hero__secteurs-img')];
    this.imagesContainer = secteurs.querySelector('.block-hero__secteurs-images');
    this.titre = secteurs.querySelector('.block-hero__secteurs-titre');
    this.isCollapsed = false;
    this.isAnimating = false;
    this.resizeHandler = this.onResize.bind(this);
  }

  init() {
    if (!this.hero || this.images.length === 0) return;

    document.querySelector('main').appendChild(this.secteurs);

    this.measure();
    this.animateEntry();
    this.observeHero();
    window.addEventListener('resize', this.resizeHandler, {passive: true});
  }

  measure() {
    if (this.isCollapsed) return;
    this.naturalImagesWidth = this.imagesContainer.offsetWidth;
    this.naturalImagesMargin =
      parseFloat(getComputedStyle(this.imagesContainer).marginRight) || 0;
  }

  onResize() {
    clearTimeout(this.resizeTimer);
    this.resizeTimer = setTimeout(() => this.measure(), 150);
  }

  observeHero() {
    this.observer = new IntersectionObserver(
      ([entry]) => {
        this.lastIntersecting = entry.isIntersecting;
        this.sync();
      },
      {
        threshold: 0,
        rootMargin: '-95% 0px 0px 0px',
      },
    );
    this.observer.observe(this.hero);
  }

  sync() {
    if (this.isAnimating) return;
    if (!this.lastIntersecting && !this.isCollapsed) this.collapse();
    else if (this.lastIntersecting && this.isCollapsed) this.expand();
  }

  animateEntry() {
    animate(this.images, {
      opacity: [0, 1],
      translateY: [20, 0],
      delay: stagger(100),
      duration: 500,
      ease: 'out(3)',
    });

    if (this.titre) {
      animate(this.titre, {
        opacity: [0, 1],
        delay: this.images.length * 100 + 150,
        duration: 400,
        ease: 'out(3)',
      });
    }
  }

  collapse() {
    this.isAnimating = true;
    this.measure();
    this.secteurs.classList.add('block-hero__secteurs--collapsed');

    const containerRight = this.imagesContainer.getBoundingClientRect().right;
    const offsets = this.images.map(
      (img) => containerRight - img.getBoundingClientRect().right,
    );

    animate(this.images, {
      translateX: (_, i) => offsets[i],
      duration: 300,
      ease: 'out(2)',
      delay: stagger(40, {from: 'last'}),
    });

    animate(this.imagesContainer, {
      opacity: [1, 0],
      width: [this.naturalImagesWidth, 0],
      marginRight: [this.naturalImagesMargin, 0],
      duration: 800,
      ease: 'inOutElastic(0.1, 3)',
      onComplete: () => {
        this.isCollapsed = true;
        this.isAnimating = false;
        this.sync();
      },
    });
  }

  expand() {
    this.isAnimating = true;

    Object.assign(this.imagesContainer.style, {
      opacity: '0',
      width: '0',
      marginRight: '0',
    });

    animate(this.imagesContainer, {
      opacity: [0, 1],
      width: [0, this.naturalImagesWidth],
      marginRight: [0, this.naturalImagesMargin],
      duration: 200,
      ease: 'inOutElastic(0.1, 3)',
    });

    animate(this.images, {
      translateX: 0,
      delay: stagger(60),
      duration: 400,
      ease: 'out(3)',
      onComplete: () => {
        this.imagesContainer.style.cssText = '';
        this.secteurs.classList.remove('block-hero__secteurs--collapsed');
        this.isCollapsed = false;
        this.isAnimating = false;
        this.measure();
        this.sync();
      },
    });
  }

  static init() {
    document.querySelectorAll('.block-hero__secteurs').forEach((el) => {
      new HeroSecteurs(el).init();
    });
  }
}

document.addEventListener('DOMContentLoaded', () => HeroSecteurs.init());

import { animate, stagger } from 'animejs';
import { onScroll } from './scroll.js';

class Secteurs {
  constructor(el) {
    this.el = el;
    this.images = [...el.querySelectorAll('.block-secteurs__img')];
    this.imagesContainer = el.querySelector('.block-secteurs__images');
    this.titre = el.querySelector('.block-secteurs__titre');
    this.firstBlock = null;
    this.offset = 0;
    this.appliedOffset = null;
    this.naturalImagesWidth = 0;
    this.naturalImagesMargin = 0;
    this.resizeTimer = null;
    this.scrollUnsub = null;
    this.isCollapsed = false;
    this.busy = false;
    this.pending = null;
    this.hovered = false;
    this.atTop = true;
    this.resizeHandler = this.onResize.bind(this);
    this.enterHandler = this.onEnter.bind(this);
    this.leaveHandler = this.onLeave.bind(this);
  }

  init() {
    if (this.images.length === 0) return;

    const main = document.querySelector('main');
    const first = main?.firstElementChild;
    this.firstBlock = first && first !== this.el ? first : null;

    this.atTop = window.scrollY <= 0;
    this.measureOffset();
    this.measure();

    this.appliedOffset = this.atTop ? this.offset : 0;
    animate(this.el, { translateY: this.appliedOffset, duration: 0 });

    if (this.atTop) {
      this.animateEntry();
    } else {
      this.isCollapsed = true;
      this.el.classList.add('block-secteurs--collapsed');
      Object.assign(this.imagesContainer.style, {
        opacity: '0',
        width: '0',
        marginRight: '0',
      });
    }

    this.scrollUnsub = onScroll((y) => {
      const newAtTop = y <= 0;
      if (newAtTop === this.atTop) return;
      this.atTop = newAtTop;
      this.transition('scroll');
    });

    window.addEventListener('resize', this.resizeHandler, { passive: true });
    this.el.addEventListener('mouseenter', this.enterHandler);
    this.el.addEventListener('mouseleave', this.leaveHandler);
  }

  destroy() {
    this.scrollUnsub?.();
    window.removeEventListener('resize', this.resizeHandler);
    this.el.removeEventListener('mouseenter', this.enterHandler);
    this.el.removeEventListener('mouseleave', this.leaveHandler);
    clearTimeout(this.resizeTimer);
  }

  onEnter() {
    this.hovered = true;
    this.transition('hover');
  }

  onLeave() {
    this.hovered = false;
    this.transition('hover');
  }

  onResize() {
    clearTimeout(this.resizeTimer);
    this.resizeTimer = setTimeout(() => {
      this.measure();
      this.measureOffset();
      const target = this.atTop ? this.offset : 0;
      this.runTranslate(target);
    }, 150);
  }

  // Si le premier bloc tient sur moins d'un écran, on remonte le bloc
  // secteurs pour qu'il apparaisse visuellement dans son aire (sticky bottom
  // sinon le placerait au bas du viewport, sous le premier bloc).
  measureOffset() {
    if (!this.firstBlock) {
      this.offset = 0;
      return;
    }
    const h = this.firstBlock.offsetHeight;
    const vh = window.innerHeight;
    this.offset = h < vh ? h - vh : 0;
  }

  measure() {
    if (this.isCollapsed) {
      const saved = this.imagesContainer.style.cssText;
      this.imagesContainer.style.cssText = '';
      this.naturalImagesWidth = this.imagesContainer.offsetWidth;
      this.naturalImagesMargin =
        parseFloat(getComputedStyle(this.imagesContainer).marginRight) || 0;
      this.imagesContainer.style.cssText = saved;
      return;
    }
    this.naturalImagesWidth = this.imagesContainer.offsetWidth;
    this.naturalImagesMargin =
      parseFloat(getComputedStyle(this.imagesContainer).marginRight) || 0;
  }

  transition(reason) {
    if (this.busy) {
      this.pending = reason;
      return;
    }
    this.runTransition(reason);
  }

  runTransition(reason) {
    this.busy = true;

    const targetCollapsed = !this.atTop && !this.hovered;
    const targetOffset = this.atTop ? this.offset : 0;

    const finish = () => {
      this.busy = false;
      if (this.pending) {
        const next = this.pending;
        this.pending = null;
        this.runTransition(next);
      }
    };

    if (reason === 'scroll') {
      if (this.atTop) {
        this.runTranslate(targetOffset, () => {
          this.runCollapseState(targetCollapsed, finish);
        });
      } else {
        this.runCollapseState(targetCollapsed, () => {
          this.runTranslate(targetOffset, finish);
        });
      }
    } else {
      this.runCollapseState(targetCollapsed, finish);
    }
  }

  runTranslate(target, onDone) {
    if (target === this.appliedOffset) {
      onDone?.();
      return;
    }
    this.appliedOffset = target;
    animate(this.el, {
      translateY: target,
      duration: 200,
      ease: 'inOut(2)',
      onComplete: () => onDone?.(),
    });
  }

  runCollapseState(target, onDone) {
    if (target === this.isCollapsed) {
      onDone?.();
      return;
    }
    if (target) this.collapse(onDone);
    else this.expand(onDone);
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

  collapse(onDone) {
    this.measure();
    this.el.classList.add('block-secteurs--collapsed');

    const containerRight = this.imagesContainer.getBoundingClientRect().right;
    const offsets = this.images.map(
      (img) => containerRight - img.getBoundingClientRect().right,
    );

    animate(this.images, {
      translateX: (_, i) => offsets[i],
      duration: 300,
      ease: 'out(2)',
      delay: stagger(40, { from: 'last' }),
    });

    animate(this.imagesContainer, {
      opacity: [1, 0],
      width: [this.naturalImagesWidth, 0],
      marginRight: [this.naturalImagesMargin, 0],
      duration: 800,
      ease: 'inOutElastic(0.1, 3)',
      onComplete: () => {
        this.isCollapsed = true;
        onDone?.();
      },
    });
  }

  expand(onDone) {
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
        this.el.classList.remove('block-secteurs--collapsed');
        this.isCollapsed = false;
        this.measure();
        onDone?.();
      },
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.block-secteurs').forEach((el) => {
    new Secteurs(el).init();
  });
});

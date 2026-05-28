import { animate, stagger } from 'animejs';

const MOBILE_BREAKPOINT = 991;
const RING_GAP_DEG = 6;
const HOVER_DURATION = 350;
const PREFERS_REDUCED_MOTION = window.matchMedia(
  '(prefers-reduced-motion: reduce)',
).matches;

const segmentLength = (count) => ((360 / count - RING_GAP_DEG) / 360) * 100;

const angleFor = (index, count) => {
  if (count === 6) {
    return ((index + 1) * 60) % 360;
  }
  return (360 / count) * (index + 0.5);
};

const offsetForIndex = (index, count) => {
  const angle = angleFor(index, count);
  const pos = (angle / 360) * 100;
  return -(pos - segmentLength(count) / 2);
};

class EtapesCirculaires {
  constructor(root) {
    this.root = root;
    this.count = parseInt(root.dataset.count, 10) || 0;
    if (this.count < 2) return;

    this.nums = Array.from(
      root.querySelectorAll('.block-etapes-circulaires__num'),
    );
    this.boxes = Array.from(
      root.querySelectorAll('.block-etapes-circulaires__box'),
    );
    this.image = root.querySelector('.block-etapes-circulaires__image');
    this.ring = root.querySelector('.block-etapes-circulaires__ring');
    this.arc = root.querySelector('.block-etapes-circulaires__ring-arc');
    this.base = root.querySelector('.block-etapes-circulaires__ring-base');
    this.mobilePanel = root.querySelector(
      '.block-etapes-circulaires__mobile-panel',
    );
    this.mobileTitre = root.querySelector(
      '.block-etapes-circulaires__mobile-titre',
    );
    this.mobileTexte = root.querySelector(
      '.block-etapes-circulaires__mobile-texte',
    );

    this.activeIndex = 0;
    this.revealed = false;
    this.boxData = [];

    this.initArc();
    requestAnimationFrame(() => this.measureBoxes());
    this.bind();
    this.observe();

    if (this.isMobile()) {
      this.setMobileContent();
    }
  }

  isMobile() {
    return window.matchMedia(`(max-width: ${MOBILE_BREAKPOINT}px)`).matches;
  }

  initArc() {
    if (this.base) {
      this.base.setAttribute('stroke-dasharray', '100 0');
    }
    if (this.arc) {
      const segLen = segmentLength(this.count);
      this.arc.setAttribute('stroke-dasharray', `${segLen} ${100 - segLen}`);
      this.currentOffset = offsetForIndex(this.activeIndex, this.count);
      this.arc.setAttribute('stroke-dashoffset', this.currentOffset);
    }
  }

  shortestOffset(target) {
    let next = target;
    while (next - this.currentOffset > 50) next -= 100;
    while (next - this.currentOffset < -50) next += 100;
    return next;
  }

  measureBoxes() {
    if (this.isMobile()) {
      this.boxData = [];
      return;
    }

    this.boxData = this.boxes.map((box) => {
      const titre = box.querySelector('.block-etapes-circulaires__titre');
      const texte = box.querySelector('.block-etapes-circulaires__texte');
      if (!titre || !texte) return null;

      titre.style.height = 'auto';
      titre.style.opacity = '0';
      texte.style.height = 'auto';
      texte.style.opacity = '0';

      const titreH = titre.getBoundingClientRect().height;
      const texteH = texte.getBoundingClientRect().height;

      box.style.minHeight = `${Math.max(titreH, texteH)}px`;

      titre.style.height = `${titreH}px`;
      titre.style.opacity = '';
      texte.style.height = '0px';
      texte.style.opacity = '0';

      return { titre, texte, titreH, texteH };
    });
  }

  bind() {
    this.boxes.forEach((box) => {
      const idx = parseInt(box.dataset.index, 10);

      box.addEventListener('mouseenter', () => {
        if (this.isMobile()) return;
        this.setActive(idx);
        this.hoverEnter(idx);
      });

      box.addEventListener('mouseleave', () => {
        if (this.isMobile()) return;
        this.hoverLeave(idx);
      });

      box.addEventListener('focus', () => {
        if (this.isMobile()) return;
        this.setActive(idx);
        this.hoverEnter(idx);
      });

      box.addEventListener('blur', () => {
        if (this.isMobile()) return;
        this.hoverLeave(idx);
      });
    });

    this.nums.forEach((num) => {
      const idx = parseInt(num.dataset.index, 10);
      num.addEventListener('click', () => this.setActive(idx));
      num.addEventListener('focus', () => this.setActive(idx));
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        this.measureBoxes();
        if (this.isMobile()) {
          this.setMobileContent();
        }
      }, 150);
    });
  }

  hoverEnter(idx) {
    const d = this.boxData[idx];
    if (!d) return;

    if (PREFERS_REDUCED_MOTION) {
      d.texte.style.height = `${d.texteH}px`;
      d.texte.style.opacity = '1';
      return;
    }

    animate(d.texte, {
      height: d.texteH,
      opacity: 1,
      duration: HOVER_DURATION,
      ease: 'inOut(2)',
    });
  }

  hoverLeave(idx) {
    const d = this.boxData[idx];
    if (!d) return;

    if (PREFERS_REDUCED_MOTION) {
      d.texte.style.height = '0px';
      d.texte.style.opacity = '0';
      return;
    }

    animate(d.texte, {
      height: 0,
      opacity: 0,
      duration: HOVER_DURATION,
      ease: 'inOut(2)',
    });
  }

  setActive(index) {
    if (index === this.activeIndex) return;
    this.activeIndex = index;

    this.nums.forEach((num) => {
      const isActive = parseInt(num.dataset.index, 10) === index;
      if (isActive) {
        num.setAttribute('data-active', '');
      } else {
        num.removeAttribute('data-active');
      }
    });

    this.boxes.forEach((box) => {
      const isActive = parseInt(box.dataset.index, 10) === index;
      if (isActive) {
        box.setAttribute('data-active', '');
      } else {
        box.removeAttribute('data-active');
      }
    });

    if (this.arc) {
      const target = this.shortestOffset(offsetForIndex(index, this.count));
      this.currentOffset = target;
      animate(this.arc, {
        strokeDashoffset: target,
        duration: 500,
        ease: 'inOut(2)',
      });
    }

    if (this.isMobile()) {
      this.swapMobile();
    }
  }

  setMobileContent() {
    if (!this.mobilePanel) return;
    const item = this.boxes[this.activeIndex];
    if (!item) return;
    this.mobileTitre.innerHTML =
      item.querySelector('.block-etapes-circulaires__titre')?.innerHTML || '';
    this.mobileTexte.innerHTML =
      item.querySelector('.block-etapes-circulaires__texte')?.innerHTML || '';
  }

  swapMobile() {
    if (!this.mobilePanel) return;

    if (PREFERS_REDUCED_MOTION) {
      this.setMobileContent();
      this.mobilePanel.style.opacity = 1;
      return;
    }

    animate(this.mobilePanel, {
      opacity: [1, 0],
      duration: 180,
      ease: 'out(2)',
      onComplete: () => {
        this.setMobileContent();
        animate(this.mobilePanel, {
          opacity: [0, 1],
          duration: 220,
          ease: 'out(2)',
        });
      },
    });
  }

  observe() {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting || this.revealed) return;
          this.revealed = true;
          io.unobserve(entry.target);
          this.reveal();
        });
      },
      { threshold: 0.2 },
    );

    io.observe(this.root);
  }

  reveal() {
    if (PREFERS_REDUCED_MOTION) {
      if (this.image) this.image.style.opacity = 1;
      this.nums.forEach((n) => (n.style.opacity = 1));
      this.boxes.forEach((b) => (b.style.opacity = 1));
      if (this.ring) this.ring.style.opacity = 1;
      if (this.mobilePanel) {
        this.setMobileContent();
        this.mobilePanel.style.opacity = 1;
      }
      return;
    }

    if (this.image) {
      animate(this.image, {
        opacity: [0, 1],
        scale: [0.85, 1],
        duration: 700,
        ease: 'out(3)',
      });
    }

    if (this.ring) {
      animate(this.ring, {
        opacity: [0, 1],
        duration: 700,
        delay: 200,
        ease: 'out(2)',
      });
    }

    animate(this.nums, {
      opacity: [0, 1],
      duration: 500,
      delay: stagger(120, { start: 250 }),
      ease: 'out(3)',
    });

    animate(this.boxes, {
      opacity: [0, 1],
      duration: 500,
      delay: stagger(120, { start: 350 }),
      ease: 'out(3)',
    });

    if (this.isMobile() && this.mobilePanel) {
      animate(this.mobilePanel, {
        opacity: [0, 1],
        duration: 500,
        delay: 250 + this.count * 120,
        ease: 'out(2)',
        onBegin: () => this.setMobileContent(),
      });
    }
  }
}

const init = () => {
  document
    .querySelectorAll('.block-etapes-circulaires')
    .forEach((el) => new EtapesCirculaires(el));
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}

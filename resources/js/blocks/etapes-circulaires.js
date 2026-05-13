import { animate, stagger } from 'animejs';

const MOBILE_BREAKPOINT = 991;
const RING_GAP_DEG = 6;
const PREFERS_REDUCED_MOTION = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const positionItem = (item, angleDeg, radiusPercent, isMobile) => {
  const rad = (angleDeg * Math.PI) / 180;
  const x = Math.sin(rad) * radiusPercent;
  const y = -Math.cos(rad) * radiusPercent;

  item.style.setProperty('--x', `${x}%`);
  item.style.setProperty('--y', `${y}%`);

  let side = 'right';
  if (isMobile) {
    side = 'mobile';
  } else if (angleDeg > 45 && angleDeg < 135) {
    side = 'right';
  } else if (angleDeg >= 135 && angleDeg <= 225) {
    side = 'bottom';
  } else if (angleDeg > 225 && angleDeg < 315) {
    side = 'left';
  } else {
    side = 'top';
  }
  item.dataset.side = side;
};

const segmentLength = (count) => ((360 / count - RING_GAP_DEG) / 360) * 100;

const angleFor = (index, count) => (360 / count) * (index + 0.5);

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

    this.items = Array.from(root.querySelectorAll('.block-etapes-circulaires__item'));
    this.center = root.querySelector('.block-etapes-circulaires__center');
    this.ring = root.querySelector('.block-etapes-circulaires__ring');
    this.arc = root.querySelector('.block-etapes-circulaires__ring-arc');
    this.mobilePanel = root.querySelector('.block-etapes-circulaires__mobile-panel');
    this.mobileTitre = root.querySelector('.block-etapes-circulaires__mobile-titre');
    this.mobileTexte = root.querySelector('.block-etapes-circulaires__mobile-texte');

    this.activeIndex = 0;
    this.revealed = false;

    this.layout();
    this.bind();
    this.observe();
  }

  isMobile() {
    return window.matchMedia(`(max-width: ${MOBILE_BREAKPOINT}px)`).matches;
  }

  layout() {
    const mobile = this.isMobile();
    const radius = 48;

    this.items.forEach((item, i) => {
      positionItem(item, angleFor(i, this.count), radius, mobile);
    });

    if (this.arc) {
      const segLen = segmentLength(this.count);
      this.arc.setAttribute('stroke-dasharray', `${segLen} ${100 - segLen}`);
      this.arc.setAttribute('stroke-dashoffset', offsetForIndex(this.activeIndex, this.count));
    }

    if (mobile) {
      this.setMobileContent();
    }
  }

  bind() {
    this.items.forEach((item) => {
      const idx = parseInt(item.dataset.index, 10);

      item.addEventListener('mouseenter', () => {
        if (this.isMobile()) return;
        this.setActive(idx);
      });

      item.addEventListener('focus', () => {
        if (this.isMobile()) return;
        this.setActive(idx);
      });

      item.addEventListener('click', () => {
        if (!this.isMobile()) return;
        this.setActive(idx);
      });
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => this.layout(), 150);
    });
  }

  setActive(index) {
    if (index === this.activeIndex) return;
    this.activeIndex = index;

    this.items.forEach((item) => {
      const isActive = parseInt(item.dataset.index, 10) === index;
      if (isActive) {
        item.setAttribute('data-active', '');
      } else {
        item.removeAttribute('data-active');
      }
    });

    if (this.arc) {
      const target = offsetForIndex(index, this.count);
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
    const item = this.items[this.activeIndex];
    if (!item) return;
    this.mobileTitre.innerHTML = item.querySelector('.block-etapes-circulaires__titre')?.innerHTML || '';
    this.mobileTexte.innerHTML = item.querySelector('.block-etapes-circulaires__texte')?.innerHTML || '';
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
      if (this.center) this.center.style.opacity = 1;
      this.items.forEach((item) => (item.style.opacity = 1));
      if (this.ring) this.ring.style.opacity = 1;
      if (this.mobilePanel) {
        this.setMobileContent();
        this.mobilePanel.style.opacity = 1;
      }
      return;
    }

    if (this.center) {
      animate(this.center, {
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

    animate(this.items, {
      opacity: [0, 1],
      duration: 600,
      delay: stagger(140, { start: 300 }),
      ease: 'out(3)',
    });

    if (this.isMobile() && this.mobilePanel) {
      animate(this.mobilePanel, {
        opacity: [0, 1],
        duration: 500,
        delay: 300 + this.count * 140,
        ease: 'out(2)',
        onBegin: () => this.setMobileContent(),
      });
    }
  }
}

const init = () => {
  document.querySelectorAll('.block-etapes-circulaires').forEach((el) => new EtapesCirculaires(el));
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}

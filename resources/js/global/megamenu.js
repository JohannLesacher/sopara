import { animate, eases } from 'animejs';

const OPEN_DURATION = 280;
const SWAP_DURATION = 220;
const CLOSE_DURATION = 200;
const CLOSE_INTENT_DELAY = 120;

export const initMegamenu = () => {
  const container = document.querySelector('[data-megamenu-container]');
  const triggers = document.querySelectorAll('[data-megamenu-trigger]');
  const banner = document.querySelector('.banner');

  if (!container || !triggers.length || !banner) return;

  const cache = new Map();
  let activeTrigger = null;
  let closeTimer = null;
  let activeAnimations = []; // Suivi de TOUTES les animations en cours
  let isOpen = false;

  const updateContainerTop = () => {
    if (banner.classList.contains('is-menu-open')) {
      const headerHeight =
        parseFloat(
          getComputedStyle(banner).getPropertyValue('--header-height'),
        ) || 0;
      const nav = banner.querySelector('.banner__nav--left');
      const navHeight = nav ? nav.offsetHeight : 0;
      container.style.top = `${headerHeight + navHeight}px`;
    } else {
      container.style.top = '';
    }
  };

  const escapeHtml = (str = '') =>
    String(str).replace(
      /[&<>"']/g,
      (c) =>
        ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#39;',
        })[c],
    );

  const parseChildren = (raw) => {
    try {
      const parsed = JSON.parse(raw || '[]');
      return Array.isArray(parsed) ? parsed : Object.values(parsed);
    } catch {
      return [];
    }
  };

  const buildContent = (trigger) => {
    if (cache.has(trigger)) return cache.get(trigger);

    const imageElement = trigger.dataset.megamenuImage;
    const children = parseChildren(trigger.dataset.megamenuChildren);

    const wrapper = document.createElement('div');
    wrapper.className = 'megamenu-container__wrapper';

    const imageHtml = imageElement
      ? `<div class="megamenu-container__image">
           ${imageElement}
         </div>`
      : '';

    const renderItems = (items) =>
      items
        .map((item) => {
          const hasChildren =
            Array.isArray(item.children) && item.children.length > 0;
          return `
      <li class="megamenu-container__item megamenu-container__item--${escapeHtml(item.megamenu_style || 'classique')}">
        ${item.megamenu_style !== 'invisible' ? `<a href="${escapeHtml(item.url)}">${escapeHtml(item.label)}</a>` : ''}
        ${item.megamenu_style === 'bouton' ? `<a href="${escapeHtml(item.url)}" class="megamenu-container__item__button">${escapeHtml(item.megamenu_bouton)}</a>` : ''}
        ${hasChildren ? `<ul class="megamenu-container__sublist">${renderItems(item.children)}</ul>` : ''}
      </li>
    `;
        })
        .join('');

    const itemsHtml = renderItems(children);

    wrapper.innerHTML = `
      <div class="megamenu-container__content">
        <ul class="megamenu-container__list">${itemsHtml}</ul>
      </div>
      ${imageHtml}
    `;

    cache.set(trigger, wrapper);
    return wrapper;
  };

  const cancelCurrent = () => {
    activeAnimations.forEach((anim) => anim.pause());
    activeAnimations = [];
  };

  const registerAnim = (anim) => {
    activeAnimations.push(anim);
    return anim;
  };

  const animateItemsIn = (wrapper) => {
    const items = wrapper.querySelectorAll('.megamenu-container__item');
    if (!items.length) return;
    registerAnim(
      animate(items, {
        opacity: [0, 1],
        translateY: [12, 0],
        delay: (_, i) => i * 30,
        duration: 320,
        ease: eases.outQuad,
      }),
    );
  };

  const open = (trigger) => {
    cancelCurrent();
    updateContainerTop();

    if (banner) banner.classList.add('has-megamenu-active');

    // On clone pour éviter que remove() détruise le nœud actif lors de survols rapides
    const wrapper = buildContent(trigger).cloneNode(true);
    container.replaceChildren(wrapper);

    if (!container.classList.contains('is-active')) {
      container.style.opacity = '0';
    }

    container.classList.add('is-active');
    container.setAttribute('aria-hidden', 'false');
    trigger.setAttribute('aria-expanded', 'true');

    // On utilise la valeur cible (1) pour permettre une reprise fluide si l'animation de fermeture a été interrompue
    registerAnim(
      animate(container, {
        opacity: 1,
        duration: OPEN_DURATION,
        ease: eases.outQuad,
      }),
    );

    animateItemsIn(wrapper);
    isOpen = true;
  };

  const swap = (trigger) => {
    cancelCurrent();
    updateContainerTop();
    const oldWrappers = Array.from(container.children);
    const newWrapper = buildContent(trigger).cloneNode(true);

    if (!oldWrappers.length) return open(trigger);

    const oldHeight = container.offsetHeight;
    container.appendChild(newWrapper);
    newWrapper.style.position = 'absolute';
    newWrapper.style.inset = '0';
    newWrapper.style.opacity = '0';

    const newHeight = newWrapper.offsetHeight;

    // On garantit que le conteneur finit son apparition même si "open" a été interrompu
    const containerAnimProps = {
      opacity: 1,
      duration: SWAP_DURATION,
      ease: eases.outQuad,
      onComplete: () => {
        container.style.height = '';
        container.style.transform = '';
      },
    };

    if (Math.abs(newHeight - oldHeight) > 2) {
      containerAnimProps.height = [oldHeight, newHeight];
    }

    registerAnim(animate(container, containerAnimProps));

    oldWrappers.forEach((oldWrapper) => {
      registerAnim(
        animate(oldWrapper, {
          opacity: 0,
          duration: SWAP_DURATION,
          ease: eases.outQuad,
          onComplete: () => oldWrapper.remove(),
        }),
      );
    });

    registerAnim(
      animate(newWrapper, {
        opacity: 1,
        duration: SWAP_DURATION,
        ease: eases.outQuad,
        onComplete: () => {
          newWrapper.style.position = '';
          newWrapper.style.inset = '';
        },
      }),
    );

    animateItemsIn(newWrapper);
  };

  const close = () => {
    cancelCurrent();
    triggers.forEach((t) => t.classList.remove('is-active'));
    if (activeTrigger) {
      activeTrigger.setAttribute('aria-expanded', 'false');
    }

    registerAnim(
      animate(container, {
        opacity: 0,
        duration: CLOSE_DURATION,
        ease: eases.outQuad,
        onComplete: () => {
          container.classList.remove('is-active');
          container.setAttribute('aria-hidden', 'true');
          container.replaceChildren();
          container.style.height = '';
          container.style.opacity = '';
          container.style.transform = '';

          if (banner) banner.classList.remove('has-megamenu-active');
        },
      }),
    );

    activeTrigger = null;
    isOpen = false;
  };

  const scheduleClose = () => {
    clearTimeout(closeTimer);
    closeTimer = setTimeout(close, CLOSE_INTENT_DELAY);
  };

  const cancelClose = () => {
    clearTimeout(closeTimer);
    closeTimer = null;
  };

  triggers.forEach((trigger) => {
    trigger.setAttribute('aria-expanded', 'false');

    trigger.addEventListener('mouseenter', () => {
      cancelClose();
      if (activeTrigger === trigger) return;

      if (isOpen && activeTrigger) {
        activeTrigger.setAttribute('aria-expanded', 'false');
        activeTrigger = trigger;
        swap(trigger);
        trigger.setAttribute('aria-expanded', 'true');
      } else {
        activeTrigger = trigger;
        open(trigger);
      }
    });

    trigger.addEventListener('mouseleave', (e) => {
      if (!container.contains(e.relatedTarget)) scheduleClose();
    });

    trigger.addEventListener('click', (e) => {
      const link = e.target.closest('a');
      if (link && trigger.contains(link)) e.preventDefault();

      cancelClose();
      if (activeTrigger === trigger && isOpen) return;

      triggers.forEach((t) => t.classList.remove('is-active'));
      trigger.classList.add('is-active');

      if (isOpen && activeTrigger) {
        activeTrigger.setAttribute('aria-expanded', 'false');
        activeTrigger = trigger;
        swap(trigger);
        trigger.setAttribute('aria-expanded', 'true');
      } else {
        activeTrigger = trigger;
        open(trigger);
      }
    });

    trigger.addEventListener('focus', () => {
      cancelClose();
      if (activeTrigger !== trigger) {
        activeTrigger = trigger;
        isOpen ? swap(trigger) : open(trigger);
      }
    });
  });

  container.addEventListener('mouseenter', cancelClose);
  container.addEventListener('mouseleave', (e) => {
    const movingToTrigger = Array.from(triggers).some((t) =>
      t.contains(e.relatedTarget),
    );
    if (!movingToTrigger) scheduleClose();
  });

  window.addEventListener('resize', updateContainerTop, { passive: true });

  let wasMenuOpen = banner.classList.contains('is-menu-open');
  new MutationObserver(() => {
    const isMenuOpen = banner.classList.contains('is-menu-open');
    if (wasMenuOpen && !isMenuOpen && isOpen) close();
    wasMenuOpen = isMenuOpen;
    updateContainerTop();
  }).observe(banner, {
    attributes: true,
    attributeFilter: ['class'],
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen) {
      close();
      activeTrigger?.focus();
    }
  });
};

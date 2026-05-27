class Tabs {
  static MOBILE_MQ = '(max-width: 1099px)';

  static init() {
    this.tabsBlocks = document.querySelectorAll('.block-tabs');
    if (this.tabsBlocks.length === 0) return;

    this.tabsBlocks.forEach((tabBlock, index) => {
      this.setupTabs(tabBlock, index);
      this.setupMobileHeaderPreview(tabBlock);
    });
  }

  static setupMobileHeaderPreview(tabBlock) {
    const wrappers = tabBlock.querySelectorAll(
      '.block-tab-wrapper[data-mobile-header-preview]',
    );
    if (!wrappers.length) return;

    const mq = window.matchMedia(this.MOBILE_MQ);

    const apply = () => {
      wrappers.forEach((wrapper) => {
        const header = wrapper.querySelector('.block-tab__header');
        const content = wrapper.querySelector('.block-tab__content');
        if (!header || !content) return;

        const moved = wrapper.querySelector('.block-tab__header-preview');

        if (mq.matches) {
          if (moved) return;
          const first = content.firstElementChild;
          if (!first) return;
          first.classList.add('block-tab__header-preview');
          const title = header.querySelector('.block-tab__header-title');
          title ? title.after(first) : header.appendChild(first);
        } else if (moved) {
          moved.classList.remove('block-tab__header-preview');
          content.insertBefore(moved, content.firstChild);
        }
      });
    };

    apply();
    mq.addEventListener('change', apply);
  }

  static setupTabs(tabBlock, blockIndex) {
    const navContainer = document.createElement('div');
    navContainer.classList.add('block-tabs__nav');
    navContainer.setAttribute('role', 'tablist');

    const tabWrappers = tabBlock.querySelectorAll('.block-tab-wrapper');

    tabWrappers.forEach((wrapper, tabIndex) => {
      const originalHeader = wrapper.querySelector('.block-tab__header');
      if (!originalHeader) return;

      const tabId = `tab-${blockIndex}-${tabIndex}`;
      const panelId = `panel-${blockIndex}-${tabIndex}`;

      originalHeader.setAttribute('role', 'button');
      originalHeader.setAttribute(
        'aria-expanded',
        tabIndex === 0 ? 'true' : 'false',
      );
      originalHeader.setAttribute('aria-controls', panelId);

      const clonedHeader = originalHeader.cloneNode(true);
      const clonedTitle = clonedHeader.querySelector(
        '.block-tab__header-title',
      );
      if (clonedTitle) {
        const span = document.createElement('span');
        span.className = clonedTitle.className;
        span.innerHTML = clonedTitle.innerHTML;
        clonedTitle.replaceWith(span);
      }
      clonedHeader.classList.add('block-tabs__nav-item');
      clonedHeader.setAttribute('role', 'tab');
      clonedHeader.setAttribute('id', tabId);
      clonedHeader.setAttribute(
        'aria-selected',
        tabIndex === 0 ? 'true' : 'false',
      );
      clonedHeader.setAttribute('tabindex', tabIndex === 0 ? '0' : '-1');

      wrapper.setAttribute('role', 'tabpanel');
      wrapper.setAttribute('id', panelId);
      wrapper.setAttribute('aria-labelledby', tabId);

      if (tabIndex === 0) {
        clonedHeader.classList.add('is-active');
        originalHeader.classList.add('is-active');
        wrapper.classList.add('is-active');
      }

      const toggleFn = () => this.switchTab(tabBlock, tabIndex);

      clonedHeader.addEventListener('click', toggleFn);
      originalHeader.addEventListener('click', toggleFn);
      clonedHeader.addEventListener('keydown', (e) =>
        this.handleKeyboard(e, navContainer),
      );

      navContainer.appendChild(clonedHeader);
    });

    tabBlock.prepend(navContainer);
  }

  static switchTab(tabBlock, targetIndex) {
    const navItems = tabBlock.querySelectorAll('.block-tabs__nav-item');
    const wrappers = tabBlock.querySelectorAll('.block-tab-wrapper');
    const isMobile = window.innerWidth < 1100;

    wrappers.forEach((wrapper, idx) => {
      const navItem = navItems[idx];
      const originalHeader = wrapper.querySelector('.block-tab__header');
      const isTarget = idx === targetIndex;

      if (isTarget) {
        const isActive = wrapper.classList.contains('is-active');
        const newState = isMobile ? !isActive : true;

        wrapper.classList.toggle('is-active', newState);
        navItem.classList.toggle('is-active', newState);
        originalHeader.classList.toggle('is-active', newState);

        navItem.setAttribute('aria-selected', newState);
        originalHeader.setAttribute('aria-expanded', newState);

        // If newState is true, find if there is a slider inside the tab and refresh it
        if (newState) {
          const event = new Event('tabChanged');
          document.dispatchEvent(event);
        }
      } else {
        if (!isMobile) {
          wrapper.classList.remove('is-active');
          navItem.classList.remove('is-active');
          originalHeader.classList.remove('is-active');
          navItem.setAttribute('aria-selected', 'false');
          originalHeader.setAttribute('aria-expanded', 'false');
        }
      }
    });
  }

  static handleKeyboard(e, navContainer) {
    const tabs = Array.from(navContainer.querySelectorAll('[role="tab"]'));
    const index = tabs.indexOf(document.activeElement);
    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
      let nextIndex = e.key === 'ArrowRight' ? index + 1 : index - 1;
      if (nextIndex >= tabs.length) nextIndex = 0;
      if (nextIndex < 0) nextIndex = tabs.length - 1;
      tabs[nextIndex].click();
      tabs[nextIndex].focus();
    }
  }
}

document.addEventListener('DOMContentLoaded', () => Tabs.init());

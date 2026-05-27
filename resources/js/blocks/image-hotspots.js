class ImageHotspots {
  static init() {
    this.blocks = document.querySelectorAll('.block-image-hotspots');

    this.blocks.forEach((block) => {
      const wrapper = block.querySelector('.block-image-hotspots__wrapper');
      const markers = block.querySelectorAll('.block-image-hotspots__marker');
      const points = block.querySelectorAll('.block-image-hotspots__point');
      const toggles = block.querySelectorAll('.block-image-hotspots__panel-toggle');

      const applyCoords = (el) => {
        el.style.setProperty('--x', `${el.dataset.x}%`);
        el.style.setProperty('--y', `${el.dataset.y}%`);
      };
      markers.forEach(applyCoords);
      points.forEach(applyCoords);

      const byIndex = (collection, index) =>
        Array.from(collection).find((el) => el.dataset.index === String(index)) || null;

      const positionPanel = (marker) => {
        const index = marker.dataset.index;
        const point = byIndex(points, index);
        const panel = point ? point.querySelector('.block-image-hotspots__panel') : null;
        if (!panel) return;

        panel.dataset.posX = 'right';
        panel.dataset.posY = 'bottom';

        const markerRect = marker.getBoundingClientRect();
        const panelRect = panel.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        const spaceRight = viewportWidth - markerRect.left;
        const spaceBottom = viewportHeight - markerRect.top;

        panel.dataset.posX = spaceRight < panelRect.width ? 'left' : 'right';
        panel.dataset.posY = spaceBottom < panelRect.height ? 'top' : 'bottom';
      };

      const closeAll = (exceptIndex = null) => {
        markers.forEach((m) => {
          if (m.dataset.index !== exceptIndex) m.setAttribute('aria-expanded', 'false');
        });
        toggles.forEach((t) => {
          const point = t.closest('.block-image-hotspots__point');
          if (point && point.dataset.index !== exceptIndex) t.setAttribute('aria-expanded', 'false');
        });
        points.forEach((p) => {
          if (p.dataset.index !== exceptIndex) p.removeAttribute('data-open');
        });
      };

      const openIndex = (index) => {
        closeAll(index);
        const marker = byIndex(markers, index);
        const point = byIndex(points, index);
        const toggle = point ? point.querySelector('.block-image-hotspots__panel-toggle') : null;
        if (marker) marker.setAttribute('aria-expanded', 'true');
        if (toggle) toggle.setAttribute('aria-expanded', 'true');
        if (point) point.setAttribute('data-open', 'true');
        if (marker) positionPanel(marker);
      };

      const closeIndex = (index) => {
        const marker = byIndex(markers, index);
        const point = byIndex(points, index);
        const toggle = point ? point.querySelector('.block-image-hotspots__panel-toggle') : null;
        if (marker) marker.setAttribute('aria-expanded', 'false');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
        if (point) point.removeAttribute('data-open');
      };

      let hoverTimer = null;
      const clearHoverTimer = () => {
        if (hoverTimer) {
          clearTimeout(hoverTimer);
          hoverTimer = null;
        }
      };

      markers.forEach((marker) => {
        marker.addEventListener('click', (e) => {
          e.stopPropagation();
          clearHoverTimer();
          const isOpen = marker.getAttribute('aria-expanded') === 'true';
          if (isOpen) closeIndex(marker.dataset.index);
          else openIndex(marker.dataset.index);
        });

        marker.addEventListener('mouseenter', () => {
          clearHoverTimer();
          if (marker.getAttribute('aria-expanded') === 'true') return;
          hoverTimer = setTimeout(() => openIndex(marker.dataset.index), 500);
        });

        marker.addEventListener('mouseleave', clearHoverTimer);
      });

      toggles.forEach((toggle) => {
        toggle.addEventListener('click', (e) => {
          e.stopPropagation();
          const point = toggle.closest('.block-image-hotspots__point');
          if (!point) return;
          const index = point.dataset.index;
          const isOpen = toggle.getAttribute('aria-expanded') === 'true';
          if (isOpen) closeIndex(index);
          else openIndex(index);
        });
      });

      const closeButtons = block.querySelectorAll('.block-image-hotspots__close');
      closeButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          clearHoverTimer();
          closeAll();
        });
      });

      document.addEventListener('click', (e) => {
        if (
          !e.target.closest('.block-image-hotspots__marker') &&
          !e.target.closest('.block-image-hotspots__point')
        ) {
          closeAll();
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAll();
      });

      window.addEventListener('resize', () => {
        const open = block.querySelector('.block-image-hotspots__marker[aria-expanded="true"]');
        if (open) positionPanel(open);
      });
    });
  }
}

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', () => ImageHotspots.init())
  : ImageHotspots.init();

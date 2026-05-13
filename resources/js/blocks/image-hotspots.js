class ImageHotspots {
  static init() {
    this.blocks = document.querySelectorAll('.block-image-hotspots');

    this.blocks.forEach((block) => {
      const wrapper = block.querySelector('.block-image-hotspots__wrapper');
      const markers = block.querySelectorAll('.block-image-hotspots__marker');

      block.querySelectorAll('.block-image-hotspots__point').forEach(point => {
        point.style.setProperty('--x', `${point.dataset.x}%`);
        point.style.setProperty('--y', `${point.dataset.y}%`);
      });

      const closeAll = (except = null) => {
        markers.forEach((m) => {
          if (m !== except) m.setAttribute('aria-expanded', 'false');
        });
      };

      const positionTooltip = (marker) => {
        const tooltip = marker.nextElementSibling;
        if (!tooltip || !wrapper) return;

        tooltip.dataset.posX = 'right';
        tooltip.dataset.posY = 'bottom';

        const markerRect = marker.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        const spaceRight = viewportWidth - markerRect.left;
        const spaceBottom = viewportHeight - markerRect.top;

        tooltip.dataset.posX = spaceRight < tooltipRect.width ? 'left' : 'right';
        tooltip.dataset.posY = spaceBottom < tooltipRect.height ? 'top' : 'bottom';
      };

      const openMarker = (marker) => {
        closeAll(marker);
        marker.setAttribute('aria-expanded', 'true');
        positionTooltip(marker);
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
          if (isOpen) {
            marker.setAttribute('aria-expanded', 'false');
          } else {
            openMarker(marker);
          }
        });

        marker.addEventListener('mouseenter', () => {
          clearHoverTimer();
          if (marker.getAttribute('aria-expanded') === 'true') return;
          hoverTimer = setTimeout(() => openMarker(marker), 500);
        });

        marker.addEventListener('mouseleave', clearHoverTimer);
      });

      document.addEventListener('click', (e) => {
        if (!e.target.closest('.block-image-hotspots__point')) closeAll();
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAll();
      });

      window.addEventListener('resize', () => {
        const open = block.querySelector('.block-image-hotspots__marker[aria-expanded="true"]');
        if (open) positionTooltip(open);
      });
    });
  }
}

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', () => ImageHotspots.init())
  : ImageHotspots.init();

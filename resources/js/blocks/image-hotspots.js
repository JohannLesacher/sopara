class ImageHotspots {
  static init() {
    this.blocks = document.querySelectorAll('.block-image-hotspots');

    this.blocks.forEach((block) => {
      const wrapper = block.querySelector('.block-image-hotspots__wrapper');
      const markers = block.querySelectorAll('.block-image-hotspots__marker');
      const points = block.querySelectorAll('.block-image-hotspots__point');
      const cards = block.querySelectorAll('.block-image-hotspots__mobile-card');

      points.forEach(point => {
        point.style.setProperty('--x', `${point.dataset.x}%`);
        point.style.setProperty('--y', `${point.dataset.y}%`);
      });

      const markerByIndex = (index) => {
        const point = block.querySelector(`.block-image-hotspots__point[data-index="${index}"]`);
        return point ? point.querySelector('.block-image-hotspots__marker') : null;
      };

      const cardByIndex = (index) => {
        return block.querySelector(`.block-image-hotspots__mobile-card[data-index="${index}"]`);
      };

      const indexOfMarker = (marker) => {
        const point = marker.closest('.block-image-hotspots__point');
        return point ? point.dataset.index : null;
      };

      const closeAllCards = (except = null) => {
        cards.forEach((c) => {
          if (c !== except) c.setAttribute('aria-expanded', 'false');
        });
      };

      const closeAll = (except = null) => {
        markers.forEach((m) => {
          if (m !== except) m.setAttribute('aria-expanded', 'false');
        });
        const exceptIndex = except ? indexOfMarker(except) : null;
        cards.forEach((c) => {
          if (c.dataset.index !== exceptIndex) c.setAttribute('aria-expanded', 'false');
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
        const idx = indexOfMarker(marker);
        const card = idx !== null ? cardByIndex(idx) : null;
        if (card) card.setAttribute('aria-expanded', 'true');
        positionTooltip(marker);
      };

      const toggleCard = (card) => {
        const isOpen = card.getAttribute('aria-expanded') === 'true';
        const marker = markerByIndex(card.dataset.index);
        if (isOpen) {
          card.setAttribute('aria-expanded', 'false');
          if (marker) marker.setAttribute('aria-expanded', 'false');
        } else {
          closeAllCards(card);
          closeAll(marker);
          card.setAttribute('aria-expanded', 'true');
          if (marker) {
            marker.setAttribute('aria-expanded', 'true');
            positionTooltip(marker);
          }
        }
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

      cards.forEach((card) => {
        card.addEventListener('click', (e) => {
          e.stopPropagation();
          toggleCard(card);
        });
      });

      const closeButtons = block.querySelectorAll('.block-image-hotspots__tooltip-close');
      closeButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          clearHoverTimer();
          closeAll();
        });
      });

      document.addEventListener('click', (e) => {
        if (
          !e.target.closest('.block-image-hotspots__point') &&
          !e.target.closest('.block-image-hotspots__mobile-card')
        ) {
          closeAll();
        }
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

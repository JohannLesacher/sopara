export const initHeroBannerMaxWidth = () => {
  const covers = document.querySelectorAll('.wp-block-cover.is-style-hero-banner');
  if (!covers.length) return;

  const grid = document.querySelector('.main');
  if (!grid) return;

  const offset = 605;
  const breakpoint = 1200;
  let raf = null;

  const measureContentWidth = () => {
    const probe = document.createElement('div');
    probe.style.gridColumn = 'content';
    probe.style.visibility = 'hidden';
    probe.style.height = '0';
    grid.appendChild(probe);
    const width = probe.offsetWidth;
    grid.removeChild(probe);
    return width;
  };

  const update = () => {
    const isWide = window.innerWidth >= breakpoint;
    const contentWidth = isWide ? measureContentWidth() : 0;

    covers.forEach((cover) => {
      const inner = cover.querySelector('.wp-block-cover__inner-container');
      if (!inner) return;

      inner.style.maxWidth = isWide ? `${contentWidth - offset}px` : '';
    });
  };

  const onResize = () => {
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(update);
  };

  update();
  window.addEventListener('resize', onResize);
};

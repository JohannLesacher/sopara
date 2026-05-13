document.querySelectorAll('.block-texte-defilant').forEach((block) => {
  const track = block.querySelector('.block-texte-defilant__track');

  const setup = () => {
    track.querySelectorAll('[aria-hidden="true"]').forEach((el) => el.remove());

    const original = track.querySelector('.block-texte-defilant__text');
    const itemWidth = original.offsetWidth;
    const copies = Math.ceil(block.offsetWidth / itemWidth) + 2;

    for (let i = 0; i < copies; i++) {
      const clone = document.createElement('span');
      clone.className = original.className;
      clone.setAttribute('aria-hidden', 'true');
      clone.innerHTML = original.innerHTML;
      track.appendChild(clone);
    }

    block.style.setProperty('--item-width', `${itemWidth}px`);
  };

  setup();
  window.addEventListener('resize', setup);
});

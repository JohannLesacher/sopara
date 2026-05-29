const SELECTOR = '.wp-block-group.is-style-galery-grid';
const ALIGN_CLASSES = ['alignfull', 'alignwide'];
const SKIP_THRESHOLD = 5;
const BASE_ITEMS = 10;
const BASE_COLS = 8;

function transform(group) {
  if (group.dataset.hsInit) return;

  const itemCount = group.children.length;
  if (itemCount <= SKIP_THRESHOLD) return;

  group.dataset.hsInit = 'true';
  const extraItems = Math.max(0, itemCount - BASE_ITEMS);
  const colCount = BASE_COLS + Math.ceil(extraItems / 2) * 2;

  const outer = document.createElement('div');
  outer.className = 'horizontal-scroll horizontal-scroll--galery';
  outer.setAttribute('js-horizontal-scroll', '');

  for (const cls of ALIGN_CLASSES) {
    if (group.classList.contains(cls)) {
      outer.classList.add(cls);
      group.classList.remove(cls);
    }
  }

  const wrapper = document.createElement('div');
  wrapper.className = 'horizontal-scroll__wrapper';
  wrapper.setAttribute('js-horizontal-scroll_wrapper', '');

  const scroller = document.createElement('div');
  scroller.className = 'horizontal-scroll__scroller';
  scroller.setAttribute('js-horizontal-scroll_scroller', '');

  group.parentNode.insertBefore(outer, group);
  wrapper.appendChild(group);
  outer.appendChild(wrapper);
  outer.appendChild(scroller);

  group.classList.add('horizontal-scroll__view');
  group.setAttribute('js-horizontal-scroll_view', '');

  outer.style.setProperty('--galery-cols', colCount);
}

export function initGroupHorizontalScroll() {
  const groups = document.querySelectorAll(SELECTOR);
  for (const group of groups) transform(group);
}

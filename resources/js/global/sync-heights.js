const BREAKPOINT = 768;
const SELECTOR = '.sync-element-heights';

const groups = new Map();
let rafId = null;

const reset = (rows) => {
  rows.forEach((row) => {
    row.forEach((el) => {
      el.style.height = '';
    });
  });
};

const sync = (group) => {
  if (window.innerWidth < BREAKPOINT) {
    reset(group.rows);
    return;
  }

  group.rows.forEach((row) => {
    row.forEach((el) => {
      el.style.height = '';
    });

    const max = row.reduce(
      (acc, el) => Math.max(acc, el.getBoundingClientRect().height),
      0,
    );

    row.forEach((el) => {
      el.style.height = `${max}px`;
    });
  });
};

const buildRows = (container) => {
  const columns = Array.from(container.children);
  if (columns.length === 0) return [];

  const rowCount = Math.min(...columns.map((col) => col.children.length));
  const rows = [];

  for (let i = 0; i < rowCount; i++) {
    rows.push(columns.map((col) => col.children[i]));
  }

  return rows;
};

const syncAll = () => {
  rafId = null;
  groups.forEach(sync);
};

const schedule = () => {
  if (rafId !== null) return;
  rafId = requestAnimationFrame(syncAll);
};

const observe = (container) => {
  const rows = buildRows(container);
  if (rows.length === 0) return;

  let lastWidth = container.getBoundingClientRect().width;

  const observer = new ResizeObserver((entries) => {
    const width = entries[0].contentRect.width;
    if (width === lastWidth) return;
    lastWidth = width;
    schedule();
  });

  observer.observe(container);

  const group = { rows, observer };
  groups.set(container, group);
  sync(group);
};

const destroy = (container) => {
  const group = groups.get(container);
  if (!group) return;
  group.observer.disconnect();
  reset(group.rows);
  groups.delete(container);
};

const init = () => {
  document.querySelectorAll(SELECTOR).forEach(observe);
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}

export { observe, destroy };

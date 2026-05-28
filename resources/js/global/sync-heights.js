const BREAKPOINT = 768;
const SELECTOR = '.sync-element-heights';

const groups = new Map();
let rafId = null;

const NO_DESCEND_TAGS = new Set(['UL', 'OL']);

const canDescend = (members) =>
  members.every((el) => !NO_DESCEND_TAGS.has(el.tagName));

const buildLevel = (containers) => {
  const rowCount = Math.min(...containers.map((c) => c.children.length));
  if (!Number.isFinite(rowCount) || rowCount === 0) return [];

  const rows = [];
  for (let i = 0; i < rowCount; i++) {
    const members = containers.map((c) => c.children[i]);
    const subRows = canDescend(members) ? buildLevel(members) : [];
    rows.push({ members, subRows });
  }
  return rows;
};

const resetRows = (rows) => {
  rows.forEach((row) => {
    row.members.forEach((el) => {
      el.style.height = '';
    });
    resetRows(row.subRows);
  });
};

const applyRows = (rows) => {
  rows.forEach((row) => {
    const max = row.members.reduce(
      (acc, el) => Math.max(acc, el.getBoundingClientRect().height),
      0,
    );
    row.members.forEach((el) => {
      el.style.height = `${max}px`;
    });
    applyRows(row.subRows);
  });
};

const sync = (group) => {
  if (window.innerWidth < BREAKPOINT) {
    resetRows(group.rows);
    return;
  }

  resetRows(group.rows);
  applyRows(group.rows);
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
  const rows = buildLevel(Array.from(container.children));
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
  resetRows(group.rows);
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

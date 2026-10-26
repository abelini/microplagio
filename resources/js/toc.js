export function initToc() {
  const links = Array.from(document.querySelectorAll('[data-toc-link]'));

  if (!links.length) {
    return;
  }

  const entries = [];

  links.forEach((link) => {
    const href = link.getAttribute('href') || '';
    let id = href.replace(/^#/, '');

    try {
      id = decodeURIComponent(id);
    } catch {
      // Se usa el id tal cual.
    }

    const target = document.getElementById(id);

    if (target) {
      entries.push({ target, link });
    }
  });

  if (!entries.length) {
    return;
  }

  const setActive = (activeLink) => {
    links.forEach((link) => {
      link.classList.toggle('is-active', link === activeLink);
    });
  };

  const update = () => {
    let current = null;

    entries.forEach((entry) => {
      if (entry.target.getBoundingClientRect().top <= 160) {
        current = entry;
      }
    });

    if (current) {
      setActive(current.link);
    } else {
      links.forEach((link) => link.classList.remove('is-active'));
    }
  };

  let scheduled = false;

  window.addEventListener(
    'scroll',
    () => {
      if (scheduled) {
        return;
      }

      scheduled = true;
      window.requestAnimationFrame(() => {
        scheduled = false;
        update();
      });
    },
    { passive: true }
  );

  update();
}

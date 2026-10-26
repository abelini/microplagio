export function initNav() {
  const drawer = document.querySelector('[data-nav-drawer]');
  const toggle = document.querySelector('[data-nav-toggle]');

  if (!drawer || !toggle) {
    return;
  }

  let lastFocus = null;

  const open = () => {
    lastFocus = document.activeElement;
    drawer.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    document.body.classList.add('is-locked');

    const first = drawer.querySelector('a, button');
    window.setTimeout(() => first?.focus(), 40);
  };

  const close = () => {
    drawer.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('is-locked');

    if (lastFocus instanceof HTMLElement) {
      lastFocus.focus();
    }
  };

  toggle.addEventListener('click', () => {
    if (drawer.hidden) {
      open();
    } else {
      close();
    }
  });

  drawer.querySelectorAll('[data-nav-close]').forEach((element) => {
    element.addEventListener('click', close);
  });

  drawer.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', close);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !drawer.hidden) {
      close();
    }
  });

  document.addEventListener('mp:overlay-open', () => {
    if (!drawer.hidden) {
      close();
    }
  });

  window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
    if (event.matches && !drawer.hidden) {
      close();
    }
  });
}

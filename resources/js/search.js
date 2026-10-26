export function initSearch() {
  const overlay = document.querySelector('[data-search-overlay]');

  if (!overlay) {
    return;
  }

  const input = overlay.querySelector('[data-search-input]');
  const results = overlay.querySelector('[data-search-results]');
  const openers = document.querySelectorAll('[data-search-open]');
  const closers = overlay.querySelectorAll('[data-search-close]');
  const data = window.mpData || {};
  const restUrl = data.restUrl || '/wp-json/';
  const homeUrl = data.homeUrl || '/';

  const typeLabels = {
    post: 'entrada',
    page: 'página',
    category: 'categoría',
    post_tag: 'etiqueta',
    attachment: 'archivo',
  };

  let timer = null;
  let controller = null;
  let lastFocus = null;
  let activeIndex = -1;
  let links = [];

  const empty = (html) => {
    results.replaceChildren();
    const paragraph = document.createElement('p');
    paragraph.className = 'search-overlay__hint';
    paragraph.innerHTML = html;
    results.append(paragraph);
  };

  const html = (key, fallback) => (data.strings && data.strings[key]) || fallback;

  const open = () => {
    lastFocus = document.activeElement;
    overlay.hidden = false;
    document.body.classList.add('is-locked');
    document.dispatchEvent(new CustomEvent('mp:overlay-open'));

    window.setTimeout(() => {
      input.focus();
      input.select();
    }, 40);
  };

  const close = () => {
    overlay.hidden = true;
    document.body.classList.remove('is-locked');
    document.dispatchEvent(new CustomEvent('mp:overlay-close'));

    if (lastFocus instanceof HTMLElement) {
      lastFocus.focus();
    }
  };

  const render = (items) => {
    results.replaceChildren();
    activeIndex = -1;
    links = [];

    if (!items.length) {
      empty(html('noResults', 'Sin resultados. Ni una coincidencia.'));
      return;
    }

    const list = document.createElement('ul');
    list.className = 'search-list';

    items.forEach((item) => {
      const listItem = document.createElement('li');
      const link = document.createElement('a');
      link.href = item.url;
      link.className = 'search-result';

      const type = document.createElement('span');
      type.className = 'search-result__type';
      type.textContent = typeLabels[item.subtype] || item.subtype || 'post';

      const title = document.createElement('span');
      title.className = 'search-result__title';
      title.textContent = item.title || 'Sin título';

      link.append(type, title);
      listItem.append(link);
      list.append(listItem);
      links.push(link);
    });

    results.append(list);
  };

  const search = async () => {
    const term = input.value.trim();

    if (term.length < 2) {
      empty('Escribe al menos dos caracteres…');
      return;
    }

    empty(html('searching', 'Buscando…'));

    controller?.abort();
    controller = new AbortController();

    try {
      const endpoint = `${restUrl}wp/v2/search?search=${encodeURIComponent(term)}&per_page=8&_fields=title,url,subtype`;
      const response = await fetch(endpoint, {
        signal: controller.signal,
        headers: { Accept: 'application/json' },
      });

      if (!response.ok) {
        throw new Error(String(response.status));
      }

      const items = await response.json();
      render(Array.isArray(items) ? items : []);
    } catch (error) {
      if (error.name !== 'AbortError') {
        empty(html('error', 'No se pudo buscar. Inténtalo de nuevo.'));
      }
    }
  };

  openers.forEach((button) => button.addEventListener('click', open));
  closers.forEach((element) => element.addEventListener('click', close));

  input.addEventListener('input', () => {
    window.clearTimeout(timer);
    timer = window.setTimeout(search, 240);
  });

  input.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
      if (!links.length) {
        return;
      }

      event.preventDefault();
      const direction = event.key === 'ArrowDown' ? 1 : -1;
      activeIndex = (activeIndex + direction + links.length) % links.length;

      links.forEach((link, index) => {
        link.classList.toggle('is-active', index === activeIndex);
      });

      links[activeIndex].scrollIntoView({ block: 'nearest' });
    }

    if (event.key === 'Enter') {
      if (activeIndex >= 0 && links[activeIndex]) {
        event.preventDefault();
        window.location.href = links[activeIndex].href;
        return;
      }

      if (input.value.trim().length >= 2) {
        event.preventDefault();
        window.location.href = `${homeUrl}?s=${encodeURIComponent(input.value.trim())}`;
      }
    }
  });

  document.addEventListener('keydown', (event) => {
    const element = document.activeElement;
    const typing =
      element instanceof HTMLElement &&
      (['INPUT', 'TEXTAREA', 'SELECT'].includes(element.tagName) || element.isContentEditable);

    if ((event.key === 'k' && (event.metaKey || event.ctrlKey)) || (event.key === '/' && !typing)) {
      event.preventDefault();
      overlay.hidden ? open() : close();
      return;
    }

    if (event.key === 'Escape' && !overlay.hidden) {
      event.preventDefault();
      close();
    }
  });
}

const strings = (window.mpData && window.mpData.i18n) || {};

export function i18n(key, fallback = '') {
  return strings[key] || fallback;
}

export async function copyText(text) {
  if (navigator.clipboard && window.isSecureContext) {
    try {
      await navigator.clipboard.writeText(text);
      return true;
    } catch {
      // Se intenta el método clásico.
    }
  }

  const area = document.createElement('textarea');
  area.value = text;
  area.setAttribute('readonly', '');
  area.style.position = 'fixed';
  area.style.top = '0';
  area.style.opacity = '0';
  document.body.append(area);
  area.select();

  let ok = false;

  try {
    ok = document.execCommand('copy');
  } catch {
    ok = false;
  }

  area.remove();

  return ok;
}

export function flashLabel(button, text) {
  const label = button.querySelector('[data-copy-label]') || button;
  const original = label.textContent;

  button.classList.add('is-done');
  label.textContent = text;

  window.setTimeout(() => {
    label.textContent = original;
    button.classList.remove('is-done');
  }, 1800);
}

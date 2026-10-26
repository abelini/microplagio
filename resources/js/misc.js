import { copyText, flashLabel, i18n } from './clipboard.js';

export function initMisc() {
  const head = document.querySelector('[data-site-head]');

  if (head) {
    const update = () => {
      head.classList.toggle('is-scrolled', window.scrollY > 8);
    };

    window.addEventListener('scroll', update, { passive: true });
    update();
  }

  document.querySelectorAll('[data-top]').forEach((button) => {
    button.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });

  document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-link]');

    if (!button) {
      return;
    }

    const url = button.getAttribute('data-url') || window.location.href;
    const ok = await copyText(url);

    flashLabel(button, ok ? i18n('copied', '¡Copiado!') : i18n('copyError', 'No se pudo copiar'));
  });
}

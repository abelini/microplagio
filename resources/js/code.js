import { copyText, flashLabel, i18n } from './clipboard.js';

export function initCode() {
  document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-code]');

    if (!button) {
      return;
    }

    const panel = button.closest('.mp-code');
    const code = panel?.querySelector('.mp-code__code');

    if (!code) {
      return;
    }

    const text = code.innerText.replace(/\n+$/, '');
    const ok = await copyText(text);

    flashLabel(button, ok ? i18n('copied', '¡Copiado!') : i18n('copyError', 'No se pudo copiar'));
  });
}

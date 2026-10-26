import '../css/app.css';

import { initNav } from './nav.js';
import { initSearch } from './search.js';
import { initCode } from './code.js';
import { initToc } from './toc.js';
import { initMisc } from './misc.js';

function boot() {
  initNav();
  initSearch();
  initCode();
  initToc();
  initMisc();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}

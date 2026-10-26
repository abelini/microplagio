<div class="search-overlay" data-search-overlay hidden>
    <div class="search-overlay__backdrop" data-search-close></div>

    <div class="search-overlay__panel" role="dialog" aria-modal="true" aria-labelledby="search-title">
        <h2 id="search-title" class="screen-reader-text"><?php esc_html_e('Buscar en el blog', 'microplagio'); ?></h2>

        <div class="search-overlay__bar">
            <span class="search-overlay__prompt" aria-hidden="true">&gt;</span>
            <input
                type="search"
                class="search-overlay__input"
                data-search-input
                placeholder="<?php esc_attr_e('grep «algo interesante» …', 'microplagio'); ?>"
                autocomplete="off"
                spellcheck="false"
                aria-label="<?php esc_attr_e('Buscar', 'microplagio'); ?>"
            >
            <button type="button" class="search-overlay__esc" data-search-close aria-label="<?php esc_attr_e('Cerrar búsqueda', 'microplagio'); ?>">esc</button>
        </div>

        <div class="search-overlay__results" data-search-results>
            <p class="search-overlay__hint">
                <?php esc_html_e('Escribe para buscar entre los artículos.', 'microplagio'); ?>
                <span aria-hidden="true">↵</span>
            </p>
        </div>

        <div class="search-overlay__foot">
            <span><kbd>↑</kbd><kbd>↓</kbd> <?php esc_html_e('navegar', 'microplagio'); ?></span>
            <span><kbd>↵</kbd> <?php esc_html_e('abrir', 'microplagio'); ?></span>
            <span><kbd>esc</kbd> <?php esc_html_e('cerrar', 'microplagio'); ?></span>
        </div>
    </div>
</div>

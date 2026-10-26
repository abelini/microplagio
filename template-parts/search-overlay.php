<div class="search-overlay" data-search-overlay hidden>
    <div class="search-overlay__backdrop" data-search-close></div>

    <div class="search-overlay__panel" role="dialog" aria-modal="true" aria-labelledby="search-title">
        <h2 id="search-title" class="screen-reader-text"><?php echo esc_html('Buscar en el blog'); ?></h2>

        <div class="search-overlay__bar">
            <span class="search-overlay__prompt" aria-hidden="true">&gt;</span>
            <input
                type="search"
                class="search-overlay__input"
                data-search-input
                placeholder="<?php echo esc_attr('grep «algo interesante» …'); ?>"
                autocomplete="off"
                spellcheck="false"
                aria-label="<?php echo esc_attr('Buscar'); ?>"
            >
            <button type="button" class="search-overlay__esc" data-search-close aria-label="<?php echo esc_attr('Cerrar búsqueda'); ?>">esc</button>
        </div>

        <div class="search-overlay__results" data-search-results>
            <p class="search-overlay__hint">
                <?php echo esc_html('Escribe para buscar entre los artículos.'); ?>
                <span aria-hidden="true">↵</span>
            </p>
        </div>

        <div class="search-overlay__foot">
            <span><kbd>↑</kbd><kbd>↓</kbd> <?php echo esc_html('navegar'); ?></span>
            <span><kbd>↵</kbd> <?php echo esc_html('abrir'); ?></span>
            <span><kbd>esc</kbd> <?php echo esc_html('cerrar'); ?></span>
        </div>
    </div>
</div>

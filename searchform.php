<?php

declare(strict_types=1);

/**
 * Formulario de búsqueda.
 */

$fieldId = wp_unique_id('search-field-');

?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="<?php echo esc_attr($fieldId); ?>" class="screen-reader-text">
        <?php esc_html_e('Buscar', 'microplagio'); ?>
    </label>
    <span class="search-form__prompt" aria-hidden="true">&gt;</span>
    <input
        id="<?php echo esc_attr($fieldId); ?>"
        type="search"
        name="s"
        class="search-form__input"
        value="<?php echo esc_attr(get_search_query()); ?>"
        placeholder="<?php esc_attr_e('grep …', 'microplagio'); ?>"
    >
    <button type="submit" class="search-form__submit">
        <span aria-hidden="true">↵</span>
        <span class="screen-reader-text"><?php esc_html_e('Buscar', 'microplagio'); ?></span>
    </button>
</form>

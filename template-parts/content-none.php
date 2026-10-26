<?php

declare(strict_types=1);

$query = get_search_query();
?>
<div class="empty">
    <p class="kicker">// <?php esc_html_e('sin resultados', 'microplagio'); ?></p>

    <h2 class="empty__title"><?php esc_html_e('Ni una coincidencia.', 'microplagio'); ?></h2>

    <div class="term empty__term">
        <div class="term-bar">
            <span class="term-dots" aria-hidden="true"><i></i><i></i><i></i></span>
            <span class="term-bar__title">~/bin/grep</span>
        </div>
        <pre class="empty__code"><span class="empty__prompt">$</span> grep -ri "<?php echo esc_html($query !== '' ? $query : '*'); ?>" ./posts

grep: no such file or directory
<span class="empty__prompt">$</span> <span class="empty__caret" aria-hidden="true">█</span></pre>
    </div>

    <p class="empty__text"><?php esc_html_e('Prueba con otra palabra o vuelve al inicio.', 'microplagio'); ?></p>

    <div class="empty__actions">
        <?php get_search_form(); ?>
        <a class="btn btn--marker" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Volver al inicio', 'microplagio'); ?></a>
    </div>
</div>

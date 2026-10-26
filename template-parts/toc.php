<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Toc;

$items = is_array($args['items'] ?? null) ? $args['items'] : [];
$html  = Toc::render($items);

if ($html === '') {
    return;
}

$count = count($items);
?>
<aside class="post-toc" aria-label="<?php esc_attr_e('Índice del artículo', 'microplagio'); ?>">
    <div class="post-toc__inner">
        <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML construido y escapado en Toc. ?>
        <a class="post-toc__top" href="#main">
            <span aria-hidden="true">↑</span> <?php esc_html_e('volver arriba', 'microplagio'); ?>
        </a>
    </div>
</aside>

<details class="toc-mobile">
    <summary>
        <span class="kicker">// <?php esc_html_e('índice', 'microplagio'); ?></span>
        <span class="toc-mobile__count"><?php echo esc_html(sprintf(_n('%d sección', '%d secciones', $count, 'microplagio'), $count)); ?></span>
    </summary>
    <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
</details>

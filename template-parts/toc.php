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
<aside class="post-toc" aria-label="<?php echo esc_attr('Índice del artículo'); ?>">
    <div class="post-toc__inner">
        <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML construido y escapado en Toc. ?>
        <a class="post-toc__top" href="#main">
            <i class="fa-solid fa-arrow-up"></i> <?php echo esc_html('volver arriba'); ?>
        </a>
    </div>
</aside>

<details class="toc-mobile">
    <summary>
        <span class="kicker">// <?php echo esc_html('índice'); ?></span>
        <span class="toc-mobile__count"><?php echo esc_html(sprintf(($count === 1 ? '%d sección' : '%d secciones'), $count)); ?></span>
    </summary>
    <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
</details>

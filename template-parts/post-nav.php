<?php

declare(strict_types=1);

$previous = get_previous_post();
$next     = get_next_post();

if (! $previous && ! $next) {
    return;
}
?>
<nav class="post-nav" aria-label="<?php esc_attr_e('Artículos anteriores y siguientes', 'microplagio'); ?>">
    <?php if ($previous): ?>
        <a class="post-nav__item post-nav__item--prev" href="<?php echo esc_url((string) get_permalink($previous)); ?>">
            <span class="post-nav__dir">← <?php esc_html_e('anterior', 'microplagio'); ?></span>
            <span class="post-nav__title"><?php echo esc_html(get_the_title($previous)); ?></span>
        </a>
    <?php else: ?>
        <span></span>
    <?php endif; ?>

    <?php if ($next): ?>
        <a class="post-nav__item post-nav__item--next" href="<?php echo esc_url((string) get_permalink($next)); ?>">
            <span class="post-nav__dir"><?php esc_html_e('siguiente', 'microplagio'); ?> →</span>
            <span class="post-nav__title"><?php echo esc_html(get_the_title($next)); ?></span>
        </a>
    <?php endif; ?>
</nav>

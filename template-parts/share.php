<?php

declare(strict_types=1);

$title = get_the_title();
$url   = (string) get_permalink();
$x     = 'https://x.com/intent/post?text=' . rawurlencode($title) . '&url=' . rawurlencode($url);
$hn    = 'https://news.ycombinator.com/submitlink?u=' . rawurlencode($url) . '&t=' . rawurlencode($title);
?>
<div class="share">
    <span class="share__label">// <?php esc_html_e('compartir', 'microplagio'); ?></span>

    <button type="button" class="share__btn" data-copy-link data-url="<?php echo esc_url($url); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7 0l2-2a5 5 0 0 0-7-7l-1 1"></path><path d="M14 11a5 5 0 0 0-7 0l-2 2a5 5 0 0 0 7 7l1-1"></path></svg>
        <span data-copy-label><?php esc_html_e('copiar enlace', 'microplagio'); ?></span>
    </button>

    <a class="share__btn" href="<?php echo esc_url($x); ?>" target="_blank" rel="noopener noreferrer">X<span class="screen-reader-text"> <?php esc_html_e('(se abre en una pestaña nueva)', 'microplagio'); ?></span></a>
    <a class="share__btn" href="<?php echo esc_url($hn); ?>" target="_blank" rel="noopener noreferrer">HN<span class="screen-reader-text"> <?php esc_html_e('(se abre en una pestaña nueva)', 'microplagio'); ?></span></a>
</div>

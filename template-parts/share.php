<?php

declare(strict_types=1);

$title = get_the_title();
$url   = (string) get_permalink();
$x     = 'https://x.com/intent/post?text=' . rawurlencode($title) . '&url=' . rawurlencode($url);
$hn    = 'https://news.ycombinator.com/submitlink?u=' . rawurlencode($url) . '&t=' . rawurlencode($title);
?>
<div class="share">
    <span class="share__label">// <?php echo esc_html('compartir'); ?></span>

    <button type="button" class="share__btn" data-copy-link data-url="<?php echo esc_url($url); ?>">
        <i class="fa-solid fa-link"></i>
        <span data-copy-label><?php echo esc_html('copiar enlace'); ?></span>
    </button>

    <a class="share__btn" href="<?php echo esc_url($x); ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-x-twitter"></i><span class="screen-reader-text"> <?php echo esc_html('(se abre en una pestaña nueva)'); ?></span></a>
    <a class="share__btn" href="<?php echo esc_url($hn); ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-hacker-news"></i><span class="screen-reader-text"> <?php echo esc_html('(se abre en una pestaña nueva)'); ?></span></a>
</div>

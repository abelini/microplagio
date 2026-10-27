<?php
declare(strict_types=1);

$title  = get_the_title();
$url    = (string) get_permalink();
$x      = 'https://x.com/intent/post?text=' . rawurlencode($title) . '&url=' . rawurlencode($url);
$ln     = 'https://www.linkedin.com/shareArticle?url=' . rawurlencode($url) . '&title=' . rawurlencode($title);
?>
<div class="share">
	<span class="share__label">// <?php echo esc_html('compartir'); ?></span>

	<button type="button" class="share__btn" data-copy-link data-url="<?php echo esc_url($url); ?>">
		<i class="fa-solid fa-link"></i>
		<span data-copy-label><?php echo esc_html('copiar enlace'); ?></span>
	</button>

	<a class="share__btn" href="<?php echo esc_url($x); ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-x-twitter"></i><span class="screen-reader-text"> <?php echo esc_html('(se abre en una pestaña nueva)'); ?></span></a>
	<a class="share__btn" href="<?php echo esc_url($ln); ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-linkedin-in"></i><span class="screen-reader-text"> <?php echo esc_html('LinkedIn'); ?></span></a>
	<a class="share__btn" href="https://mastodon.social/@microplagio" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-mastodon"></i><span class="screen-reader-text"> <?php echo esc_html('Mastodon'); ?></span></a>
	<a class="share__btn" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-facebook"></i><span class="screen-reader-text"> <?php echo esc_html('Facebook'); ?></span></a>
	<a class="share__btn" href="https://wa.me/?text=<?php echo urlencode(get_the_title()); ?>%20<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i><span class="screen-reader-text"> <?php echo esc_html('WhatsApp'); ?></span></a>
</div>
<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Options;

$socials = [
    'github'   => Options::get('mp_social_github'),
    'x'        => Options::get('mp_social_x'),
    'mastodon' => Options::get('mp_social_mastodon'),
    'linkedin' => Options::get('mp_social_linkedin'),
    'email'    => Options::get('mp_social_email'),
];

$socials = array_filter($socials, static fn (string $url): bool => trim($url) !== '');

$categories = get_categories([
    'number'     => 6,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'hide_empty' => true,
]);
?>
<footer class="site-foot">
    <div class="wrap">
        <div class="site-foot__grid">
            <div class="site-foot__brand">
                <a class="brand brand--foot" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <span class="brand__text"><?php echo esc_html(strtolower(get_bloginfo('name'))); ?></span><span class="brand__cursor" aria-hidden="true">_</span>
                </a>
                <p class="site-foot__tagline"><?php echo esc_html(get_bloginfo('description')); ?></p>
                <p class="site-foot__note"><?php echo esc_html(Options::get('mp_footer_note')); ?></p>
                <p class="site-foot__sys"><span class="prompt" aria-hidden="true">$</span> neofetch</p>
                <p class="site-foot__sys site-foot__sys--out">WordPress · PHP · Vite</p>
            </div>

            <nav class="site-foot__col" aria-label="<?php esc_attr_e('Secciones', 'microplagio'); ?>">
                <p class="site-foot__title">// <?php esc_html_e('secciones', 'microplagio'); ?></p>
                <ul>
                    <?php foreach ($categories as $category): ?>
                        <li><a href="<?php echo esc_url((string) get_category_link($category)); ?>"><?php echo esc_html(mb_strtolower($category->name)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <nav class="site-foot__col" aria-label="<?php esc_attr_e('Más', 'microplagio'); ?>">
                <p class="site-foot__title">// <?php esc_html_e('más', 'microplagio'); ?></p>
                <ul>
                    <li><a href="<?php echo esc_url((string) get_post_type_archive_link('post')); ?>"><?php esc_html_e('archivo', 'microplagio'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/?s=')); ?>"><?php esc_html_e('buscar', 'microplagio'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_feed_link()); ?>">rss</a></li>
                    <li><a href="<?php echo esc_url(home_url('/feed/atom/')); ?>">atom</a></li>
                    <?php if (is_active_sidebar('footer-1')): ?>
                        <li class="site-foot__widget"><?php dynamic_sidebar('footer-1'); ?></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="site-foot__col">
                <p class="site-foot__title">// <?php esc_html_e('elsewhere', 'microplagio'); ?></p>
                <?php if ($socials !== []): ?>
                    <ul class="site-foot__social">
                        <?php foreach ($socials as $network => $url): ?>
                            <li>
                                <a href="<?php echo esc_url($url); ?>" <?php echo $network === 'email' ? '' : 'target="_blank" rel="me noopener noreferrer"'; ?>>
                                    <?php echo esc_html($network); ?> <span aria-hidden="true">↗</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="site-foot__muted"><?php esc_html_e('Configura tus redes en el Personalizador.', 'microplagio'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="site-foot__bottom">
            <p>
                © <?php
                $mpStartYear = Options::get('mp_start_year');
                $mpThisYear  = wp_date('Y');
                echo esc_html($mpStartYear !== '' && $mpStartYear !== $mpThisYear ? $mpStartYear . '–' . $mpThisYear : $mpThisYear);
                ?>
                <?php echo esc_html(get_bloginfo('name')); ?>
            </p>
            <p class="site-foot__git"><span aria-hidden="true">$</span> git log --oneline -1 <span class="site-foot__hash"><?php echo esc_html(Options::get('mp_commit_hash')); ?></span> publicar algo nuevo</p>
            <button type="button" class="site-foot__top" data-top>
                <?php esc_html_e('volver arriba', 'microplagio'); ?> <span aria-hidden="true">↑</span>
            </button>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

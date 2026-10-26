<?php

declare(strict_types=1);

get_header();

$requestPath = isset($_SERVER['REQUEST_URI'])
    ? (string) wp_parse_url((string) wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH)
    : '/';
?>
<main id="main" class="site-main">
    <div class="wrap">
        <div class="notfound">
            <p class="kicker">// <?php esc_html_e('error 404', 'microplagio'); ?></p>

            <h1 class="notfound__title">404</h1>

            <div class="term notfound__term">
                <div class="term-bar">
                    <span class="term-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                    <span class="term-bar__title">~/bin/bash</span>
                </div>
                <pre class="notfound__code"><span class="notfound__prompt">$</span> cd <?php echo esc_html($requestPath !== '' ? $requestPath : '/'); ?>

bash: cd: no such file or directory
<span class="notfound__prompt">$</span> <span class="empty__caret" aria-hidden="true">█</span></pre>
            </div>

            <p class="notfound__text">
                <?php esc_html_e('La página que buscas no existe, se mudó o nunca estuvo aquí. Puede que el enlace esté mal escrito.', 'microplagio'); ?>
            </p>

            <div class="notfound__actions">
                <?php get_search_form(); ?>
                <a class="btn btn--marker" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Volver al inicio', 'microplagio'); ?></a>
            </div>

            <div class="notfound__latest">
                <p class="kicker">// <?php esc_html_e('mientras tanto', 'microplagio'); ?></p>
                <h2 class="section-head__title"><?php esc_html_e('Lo último que escribí', 'microplagio'); ?></h2>

                <?php
                $latest = new WP_Query([
                    'posts_per_page'      => 3,
                    'ignore_sticky_posts' => true,
                    'no_found_rows'       => true,
                ]);
                ?>

                <?php if ($latest->have_posts()): ?>
                    <div class="cards cards--3">
                        <?php while ($latest->have_posts()): $latest->the_post(); ?>
                            <?php get_template_part('template-parts/card'); ?>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>

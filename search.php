<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Breadcrumbs;
use Theme\Microplagio\Support\Pagination;

get_header();

$count   = (int) $GLOBALS['wp_query']->found_posts;
$query   = get_search_query();
?>
<main id="main" class="site-main">
    <div class="wrap">
        <?php Breadcrumbs::render(); ?>

        <header class="archive-head">
            <p class="kicker">// grep</p>
            <h1 class="archive-head__title">
                <?php
                printf(
                    /* translators: %s: término de búsqueda. */
                    esc_html__('Resultados para «%s»', 'microplagio'),
                    esc_html($query)
                );
                ?>
            </h1>
            <p class="archive-head__count">
                <?php
                printf(
                    /* translators: %s: número de coincidencias. */
                    esc_html(_n('%s coincidencia', '%s coincidencias', $count, 'microplagio')),
                    esc_html(number_format_i18n($count))
                );
                ?>
            </p>

            <div class="archive-head__search">
                <?php get_search_form(); ?>
            </div>
        </header>

        <div class="home-grid">
            <div class="home-grid__main">
                <?php if (have_posts()): ?>
                    <div class="cards">
                        <?php while (have_posts()): the_post(); ?>
                            <?php get_template_part('template-parts/card'); ?>
                        <?php endwhile; ?>
                    </div>

                    <?php Pagination::render(); ?>
                <?php else: ?>
                    <?php get_template_part('template-parts/content-none'); ?>
                <?php endif; ?>
            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>
<?php get_footer(); ?>

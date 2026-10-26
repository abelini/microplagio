<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Pagination;

get_header();
?>
<main id="main" class="site-main">
    <div class="wrap">
        <header class="archive-head">
            <p class="kicker">// <?php echo esc_html('archivo'); ?></p>
            <h1 class="archive-head__title"><?php echo esc_html(wp_strip_all_tags(get_the_archive_title())); ?></h1>
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

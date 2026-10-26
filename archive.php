<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Breadcrumbs;
use Theme\Microplagio\Support\Pagination;
use Theme\Microplagio\Support\PostMeta;

get_header();

$count = (int) $GLOBALS['wp_query']->found_posts;

if (is_category()) {
    $heading = single_cat_title('', false);
    $kicker  = 'categoría';
} elseif (is_tag()) {
    $heading = '#' . single_tag_title('', false);
    $kicker  = 'etiqueta';
} elseif (is_author()) {
    $heading = (string) get_the_author_meta('display_name', (int) get_queried_object_id());
    $kicker  = 'autor';
} elseif (is_date()) {
    $heading = wp_strip_all_tags(get_the_archive_title());
    $kicker  = 'archivo';
} else {
    $heading = wp_strip_all_tags(get_the_archive_title());
    $kicker  = 'archivo';
}

$description = get_the_archive_description();
?>
<main id="main" class="site-main">
    <div class="wrap">
        <?php Breadcrumbs::render(); ?>

        <header class="archive-head">
            <p class="kicker">// <?php echo esc_html($kicker); ?></p>

            <?php if (is_author()): ?>
                <div class="archive-head__author">
                    <?php echo get_avatar((int) get_queried_object_id(), 88, '', $heading, ['class' => 'archive-head__avatar']); ?>
                    <div>
                        <h1 class="archive-head__title"><?php echo esc_html($heading); ?></h1>
                        <p class="archive-head__count">
                            <?php
                            printf(
                                /* translators: %s: número de artículos. */
                                esc_html(_n('%s artículo publicado', '%s artículos publicados', $count, 'microplagio')),
                                esc_html(number_format_i18n($count))
                            );
                            ?>
                        </p>
                    </div>
                </div>

                <?php if (get_the_author_meta('description', (int) get_queried_object_id()) !== ''): ?>
                    <p class="archive-head__desc"><?php echo wp_kses_post(PostMeta::authorBio()); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <h1 class="archive-head__title"><?php echo esc_html($heading); ?></h1>
                <p class="archive-head__count">
                    <?php
                    printf(
                        /* translators: %s: número de artículos. */
                        esc_html(_n('%s artículo', '%s artículos', $count, 'microplagio')),
                        esc_html(number_format_i18n($count))
                    );
                    ?>
                </p>

                <?php if (is_string($description) && trim($description) !== ''): ?>
                    <div class="archive-head__desc"><?php echo wp_kses_post($description); ?></div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (is_category()): ?>
                <?php
                $term = get_queried_object();
                $subcategories = $term instanceof WP_Term ? get_categories([
                    'parent'     => $term->term_id,
                    'hide_empty' => true,
                ]) : [];
                ?>

                <?php if ($subcategories !== []): ?>
                    <ul class="chips">
                        <?php foreach ($subcategories as $subcategory): ?>
                            <li>
                                <a class="chip" href="<?php echo esc_url((string) get_category_link($subcategory)); ?>">
                                    <?php echo esc_html(mb_strtolower($subcategory->name)); ?>
                                    <span><?php echo esc_html(number_format_i18n((int) $subcategory->count)); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
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

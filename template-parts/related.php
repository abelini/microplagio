<?php

declare(strict_types=1);

use Theme\Microplagio\Support\PostMeta;

$currentId = (int) get_the_ID();
$category  = PostMeta::primaryCategory($currentId);
$categories = $category instanceof WP_Term ? [$category->term_id] : wp_get_post_categories($currentId);

$related = new WP_Query([
    'posts_per_page'      => 3,
    'post__not_in'        => [$currentId],
    'category__in'        => $categories,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
    'orderby'             => 'rand',
]);

if (! $related->have_posts()) {
    $related = new WP_Query([
        'posts_per_page'      => 3,
        'post__not_in'        => [$currentId],
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ]);
}

if (! $related->have_posts()) {
    wp_reset_postdata();

    return;
}
?>
<section class="related" aria-labelledby="related-title">
    <div class="section-head">
        <div>
            <p class="kicker">// <?php echo esc_html('sigue leyendo'); ?></p>
            <h2 id="related-title" class="section-head__title"><?php echo esc_html('También te puede interesar'); ?></h2>
        </div>
    </div>

    <div class="related__grid">
        <?php while ($related->have_posts()): $related->the_post(); ?>
            <?php get_template_part('template-parts/card'); ?>
        <?php endwhile; ?>
    </div>
</section>
<?php wp_reset_postdata(); ?>

<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Breadcrumbs;
use Theme\Microplagio\Support\PostMeta;
use Theme\Microplagio\Support\Toc;

get_header();

while (have_posts()) :
    the_post();

    $content = apply_filters('the_content', get_the_content());
    $content = str_replace(']]>', ']]&gt;', $content);
    $toc     = Toc::inject((string) $content);
    $term    = PostMeta::primaryCategory();
    ?>
    <main id="main" class="site-main">
        <div class="wrap">
            <?php Breadcrumbs::render(); ?>

            <header class="post-head">
                <?php if ($term instanceof WP_Term): ?>
                    <p class="kicker">
                        // <?php
                        echo esc_html(implode('/', array_map(
                            static fn (WP_Term $crumb): string => $crumb->slug,
                            PostMeta::categoryPath($term)
                        )));
                        ?>
                    </p>
                <?php else: ?>
                    <p class="kicker">// <?php echo esc_html('artículo'); ?></p>
                <?php endif; ?>

                <h1 class="post-head__title"><?php the_title(); ?></h1>

                <?php if (has_excerpt()): ?>
                    <p class="post-head__deck"><?php echo esc_html(PostMeta::excerpt(46)); ?></p>
                <?php endif; ?>

                <?php get_template_part('template-parts/post-meta'); ?>
            </header>
        </div>

        <?php if (has_post_thumbnail()): ?>
            <figure class="post-cover">
                <div class="wrap">
                    <div class="post-cover__frame">
                        <?php the_post_thumbnail('mp-hero', ['class' => 'post-cover__img']); ?>
                    </div>

                    <?php if (get_the_post_thumbnail_caption() !== ''): ?>
                        <figcaption class="post-cover__caption"><?php echo esc_html(get_the_post_thumbnail_caption()); ?></figcaption>
                    <?php endif; ?>
                </div>
            </figure>
        <?php endif; ?>

        <div class="wrap">
            <div class="post-layout">
                <?php get_template_part('template-parts/toc', null, ['items' => $toc['items']]); ?>

                <article <?php post_class('post-body'); ?>>
                    <div class="prose-mp">
                        <?php echo $toc['html']; // phpcs:ignore WordPress.Security.EscapeOutput -- Contenido filtrado por WordPress. ?>
                    </div>

                    <?php get_template_part('template-parts/share'); ?>
                    <?php get_template_part('template-parts/author-box'); ?>
                    <?php get_template_part('template-parts/post-nav'); ?>
                    <?php get_template_part('template-parts/related'); ?>

                    <?php comments_template(); ?>
                </article>
            </div>
        </div>
    </main>
    <?php
endwhile;

get_footer();

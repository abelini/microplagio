<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Breadcrumbs;
use Theme\Microplagio\Support\Toc;

get_header();

while (have_posts()) :
    the_post();

    $content = apply_filters('the_content', get_the_content());
    $toc     = Toc::inject((string) $content);
    ?>
    <main id="main" class="site-main">
        <div class="wrap">
            <?php Breadcrumbs::render(); ?>

            <header class="post-head post-head--page">
                <p class="kicker">// <?php echo esc_html('página'); ?></p>
                <h1 class="post-head__title"><?php the_title(); ?></h1>
            </header>

            <div class="post-layout post-layout--page">
                <?php if (count($toc['items']) >= 2): ?>
                    <?php get_template_part('template-parts/toc', null, ['items' => $toc['items']]); ?>
                <?php endif; ?>

                <article <?php post_class('post-body'); ?>>
                    <div class="prose-mp">
                        <?php echo $toc['html']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </div>

                    <?php get_template_part('template-parts/share'); ?>

                    <?php if (comments_open() || get_comments_number() > 0): ?>
                        <?php comments_template(); ?>
                    <?php endif; ?>
                </article>
            </div>
        </div>
    </main>
    <?php
endwhile;

get_footer();

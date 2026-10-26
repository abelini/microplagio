<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Options;
use Theme\Microplagio\Support\Pagination;

get_header();

$total = (int) $GLOBALS['wp_query']->found_posts;
?>
<main id="main" class="site-main">
    <section class="intro">
        <div class="wrap intro__grid">
            <div class="intro__lead">
                <p class="kicker"><?php echo esc_html(Options::get('mp_kicker')); ?></p>

                <div class="manifesto">
                    <span class="manifesto__open" aria-hidden="true">/*</span>
                    <h1 class="manifesto__text"><?php echo wp_kses_post(Options::get('mp_intro')); ?></h1>
                    <span class="manifesto__close" aria-hidden="true">*/</span>
                </div>

                <p class="intro__sign">
                    — <?php echo esc_html(Options::get('mp_author')); ?> · <?php printf(esc_html__('desde %s', 'microplagio'), esc_html(Options::get('mp_start_year'))); ?>
                </p>
            </div>

            <div class="intro__side">
                <p class="intro__file"><span aria-hidden="true">$</span> cat ~/about.txt</p>
                <p class="intro__text"><?php echo esc_html(Options::get('mp_intro_text')); ?></p>

                <div class="intro__links">
                    <a href="#latest"><?php esc_html_e('↓ últimos artículos', 'microplagio'); ?></a>
                    <a href="<?php echo esc_url(get_feed_link()); ?>">rss</a>
                    <a href="<?php echo esc_url(home_url('/?s=')); ?>">grep</a>
                </div>

                <?php $lastModified = (int) get_lastpostmodified('gmt'); ?>
                <p class="intro__deploy">
                    <span class="dot" aria-hidden="true"></span>
                    <?php esc_html_e('último commit:', 'microplagio'); ?>
                    <span class="intro__deploy-build"><?php echo esc_html(Options::get('mp_commit_hash')); ?></span>
                    <?php if ($lastModified > 0): ?>
                        <span aria-hidden="true">·</span>
                        <span><?php printf(esc_html__('hace %s', 'microplagio'), esc_html(human_time_diff($lastModified, time()))); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </section>

    <?php if (have_posts() && ! is_paged()): ?>
        <?php the_post(); ?>
        <section class="wrap hero-wrap" aria-label="<?php esc_attr_e('Último artículo', 'microplagio'); ?>">
            <?php get_template_part('template-parts/hero'); ?>
        </section>
        <?php rewind_posts(); ?>
    <?php endif; ?>

    <section id="latest" class="wrap home-grid">
        <div class="home-grid__main">
            <div class="section-head">
                <div>
                    <p class="kicker">// <?php esc_html_e('archivo', 'microplagio'); ?></p>
                    <h2 class="section-head__title"><?php esc_html_e('Últimos artículos', 'microplagio'); ?></h2>
                </div>
                <p class="section-head__meta">
                    <?php
                    printf(
                        /* translators: %s: número de artículos. */
                        esc_html(_n('%s entrada', '%s entradas', $total, 'microplagio')),
                        esc_html(number_format_i18n($total))
                    );
                    ?>
                </p>
            </div>

            <?php if (have_posts()): ?>
                <div class="cards">
                    <?php $isFirst = ! is_paged(); ?>
                    <?php while (have_posts()): the_post(); ?>
                        <?php
                        if ($isFirst) {
                            $isFirst = false;
                            continue;
                        }

                        get_template_part('template-parts/card');
                        ?>
                    <?php endwhile; ?>
                </div>

                <?php Pagination::render(); ?>
            <?php else: ?>
                <?php get_template_part('template-parts/content-none'); ?>
            <?php endif; ?>
        </div>

        <?php get_sidebar(); ?>
    </section>
</main>
<?php get_footer(); ?>

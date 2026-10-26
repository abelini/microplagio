<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Placeholder;
use Theme\Microplagio\Support\PostMeta;

$category  = PostMeta::primaryCategory();
$permalink = (string) get_permalink();
$slug      = (string) get_post_field('post_name');
$diff      = PostMeta::diffStats();
?>
<article <?php post_class('hero'); ?>>
    <div class="hero__copy">
        <p class="kicker">// <?php esc_html_e('lo último', 'microplagio'); ?></p>

        <h2 class="hero__title">
            <a href="<?php echo esc_url($permalink); ?>"><?php the_title(); ?></a>
        </h2>

        <p class="hero__excerpt"><?php echo esc_html(PostMeta::excerpt(40)); ?></p>

        <div class="byline">
            <?php echo PostMeta::avatar(40, 'byline__avatar'); // phpcs:ignore WordPress.Security.EscapeOutput ?>
            <div class="byline__text">
                <span class="byline__name"><?php the_author(); ?></span>
                <span class="byline__meta">
                    <time datetime="<?php echo esc_attr(PostMeta::dateIso()); ?>"><?php echo esc_html(PostMeta::date()); ?></time>
                    <span aria-hidden="true">·</span>
                    <span><?php echo esc_html(PostMeta::readingTime()); ?></span>
                </span>
            </div>
        </div>

        <a class="btn btn--ink" href="<?php echo esc_url($permalink); ?>">
            <?php esc_html_e('Leer el artículo', 'microplagio'); ?>
            <span aria-hidden="true">→</span>
        </a>
    </div>

    <div class="hero__media">
        <figure class="term term--media">
            <div class="term-bar">
                <span class="term-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="term-bar__title">~/posts/<?php echo esc_html($slug); ?>.md</span>
                <span class="term-bar__meta">
                    <span>+<?php echo esc_html(number_format_i18n($diff['additions'])); ?></span>
                    <span class="minus">−<?php echo esc_html(number_format_i18n($diff['deletions'])); ?></span>
                </span>
            </div>

            <a class="term__frame" href="<?php echo esc_url($permalink); ?>" tabindex="-1" aria-hidden="true">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('mp-hero', ['class' => 'hero__img', 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high']); ?>
                <?php else: ?>
                    <img
                        class="hero__img"
                        src="<?php echo esc_url(Placeholder::url(get_the_title(), $category instanceof WP_Term ? $category->name : 'microplagio')); ?>"
                        alt=""
                        width="1280"
                        height="720"
                    >
                <?php endif; ?>
            </a>
        </figure>

        <?php if ($category instanceof WP_Term): ?>
            <p class="hero__caption">
                <a href="<?php echo esc_url((string) get_category_link($category)); ?>"><?php echo esc_html(mb_strtolower($category->name)); ?></a>
                <span aria-hidden="true">·</span>
                <span><?php echo esc_html(PostMeta::commentsLabel()); ?></span>
            </p>
        <?php endif; ?>
    </div>
</article>

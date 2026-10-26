<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Placeholder;
use Theme\Microplagio\Support\PostMeta;

$category  = PostMeta::primaryCategory();
$permalink = (string) get_permalink();
$comments  = (int) get_comments_number();
?>
<article <?php post_class('card'); ?>>
    <a class="card__media" href="<?php echo esc_url($permalink); ?>" tabindex="-1" aria-hidden="true">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('mp-card', ['class' => 'card__img', 'alt' => '', 'loading' => 'lazy']); ?>
        <?php else: ?>
            <img
                class="card__img"
                src="<?php echo esc_url(Placeholder::url(get_the_title(), $category instanceof WP_Term ? $category->name : 'microplagio')); ?>"
                alt=""
                width="760"
                height="428"
                loading="lazy"
            >
        <?php endif; ?>

        <?php if ($category instanceof WP_Term): ?>
            <span class="card__cat"><?php echo esc_html(mb_strtolower($category->name)); ?></span>
        <?php endif; ?>
    </a>

    <div class="card__body">
        <h3 class="card__title">
            <a href="<?php echo esc_url($permalink); ?>"><?php the_title(); ?></a>
        </h3>

        <p class="card__excerpt"><?php echo esc_html(PostMeta::excerpt(24)); ?></p>

        <div class="card__meta">
            <time datetime="<?php echo esc_attr(PostMeta::dateIso()); ?>"><?php echo esc_html(PostMeta::date()); ?></time>
            <span aria-hidden="true">·</span>
            <span><?php echo esc_html(PostMeta::readingTime()); ?></span>

            <?php if ($comments > 0): ?>
                <span aria-hidden="true">·</span>
                <a class="card__comments" href="<?php echo esc_url($permalink . '#comments'); ?>">
                    <?php echo esc_html(PostMeta::commentsLabel()); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>

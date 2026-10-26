<?php

declare(strict_types=1);

use Theme\Microplagio\Support\PostMeta;

$args = wp_parse_args($args ?? [], [
    'class'         => '',
    'show_comments' => true,
]);

$comments = (int) get_comments_number();
?>
<div class="byline <?php echo esc_attr((string) $args['class']); ?>">
    <?php echo PostMeta::avatar(40, 'byline__avatar'); // phpcs:ignore WordPress.Security.EscapeOutput ?>

    <div class="byline__text">
        <span class="byline__name"><?php the_author_posts_link(); ?></span>
        <span class="byline__meta">
            <time datetime="<?php echo esc_attr(PostMeta::dateIso()); ?>"><?php echo esc_html(PostMeta::date()); ?></time>
            <span aria-hidden="true">·</span>
            <span><?php echo esc_html(PostMeta::readingTime()); ?></span>

            <?php if ($comments > 0 && $args['show_comments']): ?>
                <span aria-hidden="true">·</span>
                <a href="#comments"><?php echo esc_html(PostMeta::commentsLabel()); ?></a>
            <?php endif; ?>
        </span>
    </div>
</div>

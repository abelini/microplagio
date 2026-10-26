<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Options;
use Theme\Microplagio\Support\PostMeta;
use Theme\Microplagio\Support\Site;

$mpProgress = Options::percent('mp_book_progress');
$mpFilled   = (int) round($mpProgress / 100 * 8);
$mpBar      = str_repeat('█', $mpFilled) . str_repeat('░', max(0, 8 - $mpFilled));

?>
<aside class="sidebar" aria-label="<?php echo esc_attr('Barra lateral'); ?>">
    <section class="widget widget--terminal">
            <div class="term-bar">
                <span class="term-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="term-bar__title">~/ahora</span>
            </div>
            <div class="widget--terminal__body">
                <p class="kicker">// <?php echo esc_html('ahora'); ?></p>
                <ul class="now-list">
                    <?php if (Options::has('mp_book_title')): ?>
                        <li>
                            <span aria-hidden="true">&gt;</span>
                            <?php echo esc_html('leyendo:'); ?>
                            <?php echo esc_html(Options::get('mp_book_title')); ?>
                            <span class="now-progress" aria-hidden="true">[<?php echo esc_html($mpBar); ?>] <?php echo esc_html($mpProgress); ?>%</span>
                            <span class="screen-reader-text"><?php printf(esc_html('Progreso de lectura: %d%%'), $mpProgress); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php foreach (preg_split('/\R/', Options::get('mp_now')) ?: [] as $line): ?>
                        <?php if (trim($line) === '') { continue; } ?>
                        <li><span aria-hidden="true">&gt;</span> <?php echo esc_html($line); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p class="now-uptime">
                    uptime: <?php echo esc_html(Site::uptime()); ?>
                    <span aria-hidden="true">·</span>
                    posts: <?php echo esc_html(number_format_i18n(Site::publishedCount())); ?>
                </p>
                <p class="now-cursor"><span aria-hidden="true">█</span></p>
            </div>
        </section>

        <section class="widget">
            <p class="kicker">// <?php echo esc_html('lo más leído'); ?></p>
            <h2 class="widget__title"><?php echo esc_html('Populares'); ?></h2>

            <?php
            $popular = new WP_Query([
                'posts_per_page'      => 5,
                'orderby'             => ['comment_count' => 'DESC', 'date' => 'DESC'],
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
                'post__not_in'        => is_singular() ? [get_the_ID()] : [],
            ]);
            ?>

            <?php if ($popular->have_posts()): ?>
                <ol class="rank">
                    <?php $rank = 0; ?>
                    <?php while ($popular->have_posts()): $popular->the_post(); $rank++; ?>
                        <li class="rank__item">
                            <span class="rank__n" aria-hidden="true"><?php echo esc_html(str_pad((string) $rank, 2, '0', STR_PAD_LEFT)); ?></span>
                            <a class="rank__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            <span class="rank__meta"><?php echo esc_html(PostMeta::commentsLabel()); ?></span>
                        </li>
                    <?php endwhile; ?>
                </ol>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </section>

        <section class="widget">
            <p class="kicker">// <?php echo esc_html('conversación'); ?></p>
            <h2 class="widget__title"><?php echo esc_html('Comentarios recientes'); ?></h2>

            <?php
            $recent = get_comments([
                'number'      => 4,
                'status'      => 'approve',
                'post_status' => 'publish',
                'orderby'     => 'comment_date_gmt',
                'order'       => 'DESC',
            ]);
            ?>

            <?php if ($recent !== []): ?>
                <ul class="recent-comments">
                    <?php foreach ($recent as $comment): ?>
                        <li>
                            <a href="<?php echo esc_url(get_comment_link($comment)); ?>">
                                <span class="recent-comments__author">@<?php echo esc_html($comment->comment_author); ?></span>
                                <span class="recent-comments__post"><?php echo esc_html(get_the_title($comment->comment_post_ID)); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="widget__muted"><?php echo esc_html('Todavía no hay comentarios. Sé el primero.'); ?></p>
            <?php endif; ?>
        </section>

        <section class="widget">
            <p class="kicker">// <?php echo esc_html('etiquetas'); ?></p>
            <h2 class="widget__title"><?php echo esc_html('Temas sueltos'); ?></h2>
            <div class="tag-cloud">
                <?php
                wp_tag_cloud([
                    'smallest' => 12,
                    'largest'  => 12,
                    'unit'     => 'px',
                    'number'   => 18,
                    'orderby'  => 'count',
                    'order'    => 'DESC',
                ]);
                ?>
            </div>
        </section>
</aside>

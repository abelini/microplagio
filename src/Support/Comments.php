<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

use WP_Comment;

/**
 * Render de comentarios con estética de hilo de code review.
 */
final class Comments
{
    /**
     * @param WP_Comment          $comment Comentario actual.
     * @param array<string, mixed> $args    Argumentos de wp_list_comments.
     * @param int                  $depth   Profundidad.
     */
    public static function render(WP_Comment $comment, array $args, int $depth): void
    {
        $isAuthor = (int) $comment->user_id > 0 && (int) $comment->user_id === (int) get_the_author_meta('ID');
        ?>
        <li id="comment-<?php comment_ID(); ?>" <?php comment_class('comment' . ($isAuthor ? ' comment--author' : '')); ?>>
            <article class="comment__body">
                <header class="comment__head">
                    <span class="comment__avatar"><?php echo get_avatar($comment, 44); ?></span>
                    <span class="comment__who">
                        <span class="comment__author">@<?php echo esc_html(get_comment_author($comment)); ?></span>
                        <?php if ($isAuthor): ?>
                            <span class="comment__badge"><?php echo esc_html('autor'); ?></span>
                        <?php endif; ?>
                    </span>
                    <a class="comment__date" href="<?php echo esc_url(get_comment_link($comment)); ?>">
                        <time datetime="<?php echo esc_attr(get_comment_time(DATE_W3C)); ?>">
                            <?php echo esc_html(get_comment_date('j M Y · H:i', $comment)); ?>
                        </time>
                    </a>
                    <span class="comment__id">#<?php echo esc_html((string) $comment->comment_ID); ?></span>
                </header>

                <div class="comment__content prose-mp prose-mp--sm">
                    <?php if ($comment->comment_approved === '0'): ?>
                        <p class="comment__pending"><?php echo esc_html('Tu comentario está pendiente de moderación.'); ?></p>
                    <?php endif; ?>
                    <?php comment_text($comment); ?>
                </div>

                <footer class="comment__actions">
                    <?php
                    comment_reply_link(array_merge($args, [
                        'add_below' => 'comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'] ?? 5,
                        'reply_text' => '↳ responder',
                    ]), $comment);
                    ?>
                    <?php edit_comment_link('editar', '<span class="comment__edit">', '</span>'); ?>
                </footer>
            </article>
        <?php
        // El cierre de </li> lo gestiona Walker_Comment.
    }
}

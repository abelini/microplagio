<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Comments;

if (post_password_required()) {
    return;
}

$count    = (int) get_comments_number();
$commenter = wp_get_current_commenter();
?>
<section id="comments" class="comments">
    <header class="comments__head">
        <div>
            <p class="kicker">// <?php echo esc_html('code review'); ?></p>
            <h2 class="comments__title">
                <?php
                printf(
                    /* translators: %s: número de comentarios. */
                    esc_html(($count === 1 ? '%s comentario' : '%s comentarios')),
                    esc_html(number_format_i18n($count))
                );
                ?>
            </h2>
        </div>

        <p class="comments__count" aria-hidden="true">[<?php echo esc_html(str_pad((string) $count, 2, '0', STR_PAD_LEFT)); ?>]</p>
    </header>

    <?php if (have_comments()): ?>
        <ol class="comments__list">
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 44,
                'callback'    => [Comments::class, 'render'],
            ]);
            ?>
        </ol>

        <?php
        the_comments_pagination([
            'prev_text' => '<i class="fa-solid fa-arrow-left"></i> anteriores',
            'next_text' => 'siguientes <i class="fa-solid fa-arrow-right"></i>',
            'class'     => 'comments__pagination',
        ]);
        ?>
    <?php endif; ?>

    <?php if (! comments_open() && $count > 0): ?>
        <p class="comments__closed"><?php echo esc_html('Los comentarios están cerrados.'); ?></p>
    <?php endif; ?>

    <?php
    comment_form([
        'class_form'           => 'comment-form',
        'class_submit'         => 'btn btn--ink',
        'title_reply'          => 'Deja un comentario',
        'title_reply_before'   => '<h3 class="comment-form__title">',
        'title_reply_after'    => '</h3>',
        'title_reply_to'       => 'Responde a %s',
        'cancel_reply_link'    => 'cancelar respuesta',
        'comment_notes_before' => '<p class="comment-form__notes">' . esc_html('Tu correo no se publica. Los campos obligatorios están marcados con *.') . '</p>',
        'comment_notes_after'  => '',
        'comment_field'        => sprintf(
            '<p class="field field--full"><label for="comment">%s <span class="req" aria-hidden="true">*</span></label><textarea id="comment" name="comment" rows="6" required aria-required="true"></textarea></p>',
            esc_html('Comentario')
        ),
        'fields'               => [
            'author' => sprintf(
                '<p class="field"><label for="author">%s <span class="req" aria-hidden="true">*</span></label><input id="author" name="author" type="text" value="%s" autocomplete="name" required aria-required="true"></p>',
                esc_html('Nombre'),
                esc_attr($commenter['comment_author'])
            ),
            'email'  => sprintf(
                '<p class="field"><label for="email">%s <span class="req" aria-hidden="true">*</span></label><input id="email" name="email" type="email" value="%s" autocomplete="email" required aria-required="true"></p>',
                esc_html('Correo'),
                esc_attr($commenter['comment_author_email'])
            ),
            'url'    => sprintf(
                '<p class="field field--full"><label for="url">%s</label><input id="url" name="url" type="url" value="%s" autocomplete="url"></p>',
                esc_html('Web (opcional)'),
                esc_attr($commenter['comment_author_url'])
            ),
        ],
        'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s">',
        'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
    ]);
    ?>
</section>

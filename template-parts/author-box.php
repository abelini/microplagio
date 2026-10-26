<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Options;
use Theme\Microplagio\Support\PostMeta;

$authorId = (int) get_the_author_meta('ID');

$socials = array_filter([
    'github'   => Options::get('mp_social_github'),
    'x'        => Options::get('mp_social_x'),
    'mastodon' => Options::get('mp_social_mastodon'),
    'linkedin' => Options::get('mp_social_linkedin'),
    'email'    => Options::get('mp_social_email'),
], static fn (string $url): bool => trim($url) !== '');
?>
<section class="author-box">
    <div class="author-box__avatar">
        <?php echo get_avatar($authorId, 112, '', get_the_author(), ['class' => 'author-box__img', 'loading' => 'lazy']); ?>
    </div>

    <div class="author-box__body">
        <p class="kicker">// whoami</p>
        <h2 class="author-box__name"><?php the_author(); ?></h2>
        <p class="author-box__bio"><?php echo wp_kses_post(PostMeta::authorBio()); ?></p>

        <div class="author-box__links">
            <a class="author-box__more" href="<?php echo esc_url((string) get_author_posts_url($authorId)); ?>">
                <?php esc_html_e('todos sus artículos', 'microplagio'); ?> <span aria-hidden="true">→</span>
            </a>

            <?php foreach ($socials as $network => $url): ?>
                <a href="<?php echo esc_url($url); ?>" <?php echo $network === 'email' ? '' : 'target="_blank" rel="me noopener noreferrer"'; ?>>
                    <?php echo esc_html($network); ?> <span aria-hidden="true">↗</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

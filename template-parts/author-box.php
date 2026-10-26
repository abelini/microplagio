<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Options;
use Theme\Microplagio\Support\PostMeta;

$authorId = (int) get_the_author_meta('ID');

$socials = array_filter([
    'github'   => ['url' => Options::get('mp_social_github'), 'icon' => 'fa-brands fa-github'],
    'x'        => ['url' => Options::get('mp_social_x'), 'icon' => 'fa-brands fa-x-twitter'],
    'mastodon' => ['url' => Options::get('mp_social_mastodon'), 'icon' => 'fa-brands fa-mastodon'],
    'linkedin' => ['url' => Options::get('mp_social_linkedin'), 'icon' => 'fa-brands fa-linkedin'],
    'email'    => ['url' => Options::get('mp_social_email'), 'icon' => 'fa-solid fa-envelope'],
], static fn (array $social): bool => trim($social['url']) !== '');
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
                <?php echo esc_html('todos sus artículos'); ?> <i class="fa-solid fa-arrow-right"></i>
            </a>

            <?php foreach ($socials as $network => $social): ?>
                <a href="<?php echo esc_url($social['url']); ?>" <?php echo $network === 'email' ? '' : 'target="_blank" rel="me noopener noreferrer"'; ?>>
                    <i class="<?php echo esc_attr($social['icon']); ?>" aria-hidden="true"></i>
                    <?php echo esc_html($network); ?>
                    <?php if ($network !== 'email'): ?>
                        <i class="fa-solid fa-arrow-up-right-from-square"></i><span class="screen-reader-text"> <?php echo esc_html('(se abre en una pestaña nueva)'); ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Options;

$socials = [
    'github'   => ['url' => Options::get('mp_social_github'), 'icon' => 'fa-brands fa-github', 'sri' => ''],
    'x'        => ['url' => Options::get('mp_social_x'), 'icon' => 'fa-brands fa-x-twitter', 'sri' => ''],
    'mastodon' => ['url' => Options::get('mp_social_mastodon'), 'icon' => 'fa-brands fa-mastodon', 'sri' => ''],
    'linkedin' => ['url' => Options::get('mp_social_linkedin'), 'icon' => 'fa-brands fa-linkedin', 'sri' => ''],
    'email'    => ['url' => Options::get('mp_social_email'), 'icon' => 'fa-solid fa-envelope', 'sri' => ''],
];

$socials = array_filter($socials, static fn (array $social): bool => trim($social['url']) !== '');

$categories = get_categories([
    'number'     => 6,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'hide_empty' => true,
]);
?>
<footer class="site-foot">
    <div class="wrap">
        <div class="site-foot__grid">
            <div class="site-foot__brand">
                <a class="brand brand--foot" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <span class="brand__text"><?php echo esc_html(strtolower(get_bloginfo('name'))); ?></span><span class="brand__cursor" aria-hidden="true">_</span>
                </a>
                <p class="site-foot__tagline"><?php echo esc_html(get_bloginfo('description')); ?></p>
                <p class="site-foot__note"><?php echo esc_html(Options::get('mp_footer_note')); ?></p>
                <p class="site-foot__sys"><span class="prompt" aria-hidden="true">$</span> neofetch</p>
                <p class="site-foot__sys site-foot__sys--out">WordPress · PHP · Vite</p>
            </div>

            <nav class="site-foot__col" aria-label="<?php echo esc_attr('Secciones'); ?>">
                <p class="site-foot__title">// <?php echo esc_html('secciones'); ?></p>
                <ul>
                    <?php foreach ($categories as $category): ?>
                        <li><a href="<?php echo esc_url((string) get_category_link($category)); ?>"><?php echo esc_html(mb_strtolower($category->name)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <nav class="site-foot__col" aria-label="<?php echo esc_attr('Más'); ?>">
                <p class="site-foot__title">// <?php echo esc_html('más'); ?></p>
                <ul>
                    <li><a href="<?php echo esc_url((string) get_post_type_archive_link('post')); ?>"><?php echo esc_html('archivo'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/?s=')); ?>"><?php echo esc_html('buscar'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_feed_link()); ?>">rss</a></li>
                    <li><a href="<?php echo esc_url(home_url('/feed/atom/')); ?>">atom</a></li>
                </ul>
            </nav>

            <div class="site-foot__col">
                <p class="site-foot__title">// <?php echo esc_html('elsewhere'); ?></p>
                <?php if ($socials !== []): ?>
                    <ul class="site-foot__social">
                    <?php foreach ($socials as $network => $social): ?>
                        <li>
                            <a href="<?php echo esc_url($social['url']); ?>" <?php echo $network === 'email' ? '' : 'target="_blank" rel="me noopener noreferrer"'; ?>>
                                <i class="<?php echo esc_attr($social['icon']); ?>"></i>
                                <?php echo esc_html($network); ?>
                                <?php if ($network !== 'email'): ?>
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i><span class="screen-reader-text"> <?php echo esc_html('(se abre en una pestaña nueva)'); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="site-foot__muted"><?php echo esc_html('Configura tus redes en el Personalizador.'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="site-foot__bottom">
            <p>
                © <?php
                $mpStartYear = Options::get('mp_start_year');
                $mpThisYear  = wp_date('Y');
                echo esc_html($mpStartYear !== '' && $mpStartYear !== $mpThisYear ? $mpStartYear . '–' . $mpThisYear : $mpThisYear);
                ?>
                <?php echo esc_html(get_bloginfo('name')); ?>
            </p>
            <p class="site-foot__git"><span aria-hidden="true">$</span> git log --oneline -1 <span class="site-foot__hash"><?php echo esc_html(Options::get('mp_commit_hash')); ?></span> publicar algo nuevo</p>
            <button type="button" class="site-foot__top" data-top>
                <?php echo esc_html('volver arriba'); ?> <i class="fa-solid fa-arrow-up"></i>
            </button>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

<?php

declare(strict_types=1);

use Theme\Microplagio\Support\Nav;
use Theme\Microplagio\Support\Options;
use Theme\Microplagio\Theme;

$mpHandle  = Options::handle();
$mpBranch  = Options::get('mp_branch');
$mpHash    = Options::get('mp_commit_hash');
$mpVersion = Theme::getInstance()->version();

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="theme-color" content="#14161a">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%2314161A'/%3E%3Ctext x='32' y='43' font-family='monospace' font-size='25' font-weight='bold' text-anchor='middle' fill='%23FFE45C'%3E%3E_%3C/text%3E%3C/svg%3E">
    <?php wp_head(); ?>
</head>
<body <?php body_class('mp-body'); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php echo esc_html('Saltar al contenido'); ?></a>

<div class="topbar">
    <div class="wrap topbar__inner">
        <a class="topbar__path" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="topbar__prompt"><?php echo esc_html($mpHandle); ?>@microplagio:~$</span>
            <span class="topbar__cmd">tail -f blog.log</span>
            <span class="topbar__caret" aria-hidden="true">▌</span>
        </a>

        <div class="topbar__right">
            <span class="topbar__build" title="<?php echo esc_attr('Último commit'); ?>"><?php echo esc_html($mpBranch . '@' . $mpHash); ?></span>

            <span class="topbar__status">
                <span class="dot" aria-hidden="true"></span>
                <?php echo esc_html(Options::get('mp_status')); ?>
            </span>

            <button type="button" class="topbar__search" data-search-open aria-label="<?php echo esc_attr('Buscar en el blog'); ?>">
                <span class="topbar__prompt" aria-hidden="true">&gt;_</span>
                <span><?php echo esc_html('grep'); ?></span>
                <kbd><?php echo esc_html(wp_is_mobile() ? '/' : '⌘K'); ?></kbd>
            </button>
        </div>
    </div>
</div>

<header class="site-head" data-site-head>
    <div class="wrap site-head__inner">
        <?php if (has_custom_logo()): ?>
            <div class="brand brand--logo">
                <?php the_custom_logo(); ?>
            </div>
        <?php else: ?>
            <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                <span class="brand__mark" aria-hidden="true">&gt;_</span>
                <span class="brand__word">
                    <span class="brand__text"><?php echo esc_html(strtolower(get_bloginfo('name'))); ?></span><span class="brand__cursor" aria-hidden="true">_</span>
                </span>
            </a>
        <?php endif; ?>

        <nav class="site-nav" aria-label="<?php echo esc_attr('Navegación principal'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav__list',
                'fallback_cb'    => [Nav::class, 'fallback'],
                'depth'          => 1,
            ]);
            ?>
        </nav>

        <div class="site-head__actions">
            <span class="head-build"><span class="dot" aria-hidden="true"></span> v<?php echo esc_html($mpVersion); ?></span>

            <button type="button" class="icon-btn topbar__search-mobile" data-search-open aria-label="<?php echo esc_attr('Buscar'); ?>">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <button type="button" class="icon-btn nav-burger" data-nav-toggle aria-expanded="false" aria-controls="nav-drawer" aria-label="<?php echo esc_attr('Abrir menú'); ?>">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<div class="statusbar" aria-hidden="true">
    <div class="wrap statusbar__inner">
        <span class="statusbar__item statusbar__branch">git:(<?php echo esc_html($mpBranch); ?>)<span class="statusbar__dirty">*</span></span>
        <span class="statusbar__item"><?php echo esc_html($mpHash); ?></span>
        <span class="statusbar__item statusbar__ahead">↑1</span>
        <span class="statusbar__spacer"></span>
        <span class="statusbar__item statusbar__opt">UTF-8</span>
        <span class="statusbar__item statusbar__opt">LF</span>
        <span class="statusbar__item">PHP</span>
        <span class="statusbar__item statusbar__opt">WP</span>
        <span class="statusbar__item statusbar__opt">Vite</span>
    </div>
</div>

<div id="nav-drawer" class="drawer" data-nav-drawer hidden>
    <div class="drawer__backdrop" data-nav-close></div>
    <div class="drawer__panel" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr('Menú'); ?>">
        <div class="drawer__head">
            <span class="topbar__path">~/menu</span>
            <button type="button" class="icon-btn" data-nav-close aria-label="<?php echo esc_attr('Cerrar menú'); ?>">✕</button>
        </div>

        <p class="kicker">// <?php echo esc_html('navegación'); ?></p>
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'drawer__nav',
            'fallback_cb'    => [Nav::class, 'fallback'],
            'depth'          => 1,
        ]);
        ?>

        <p class="kicker drawer__kicker">// <?php echo esc_html('categorías'); ?></p>
        <ul class="drawer__cats">
            <?php foreach (get_categories(['number' => 8, 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true]) as $category): ?>
                <li>
                    <a href="<?php echo esc_url((string) get_category_link($category)); ?>">
                        <span><?php echo esc_html(mb_strtolower($category->name)); ?></span>
                        <span class="drawer__count"><?php echo esc_html(number_format_i18n((int) $category->count)); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <button type="button" class="btn btn--marker drawer__search" data-search-open>
            <span aria-hidden="true">&gt;_</span> <?php echo esc_html('Buscar en el blog'); ?>
        </button>
    </div>
</div>

<?php get_template_part('template-parts/search-overlay'); ?>

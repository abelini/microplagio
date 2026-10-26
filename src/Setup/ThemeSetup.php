<?php

declare(strict_types=1);

namespace Theme\Microplagio\Setup;

/**
 * Configuración básica del tema.
 */
final class ThemeSetup
{
    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('after_setup_theme', [$this, 'contentWidth'], 0);
    }

    public function setup(): void
    {
        load_theme_textdomain('microplagio', get_template_directory() . '/languages');

        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        add_theme_support('editor-styles');
        add_theme_support('customize-selective-refresh-widgets');

        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ]);

        add_theme_support('custom-logo', [
            'height'      => 64,
            'width'       => 240,
            'flex-height' => true,
            'flex-width'  => true,
            'unlink-homepage-logo' => false,
        ]);

        add_image_size('mp-hero', 1280, 720, true);
        add_image_size('mp-card', 760, 428, true);
        add_image_size('mp-thumb', 160, 160, true);

        add_filter('excerpt_length', static fn (): int => 34, 20);
        add_filter('excerpt_more', static fn (): string => '…');
        add_filter('wp_lazy_loading_enabled', static fn (): bool => true);
    }

    public function contentWidth(): void
    {
        $GLOBALS['content_width'] = 720;
    }
}

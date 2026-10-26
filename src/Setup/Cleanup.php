<?php

declare(strict_types=1);

namespace Theme\Microplagio\Setup;

/**
 * Limpieza de cabeceras y scripts innecesarios.
 */
final class Cleanup
{
    public function register(): void
    {
        add_action('init', [$this, 'head']);
        add_filter('the_generator', '__return_empty_string');
        add_filter('wp_headers', [$this, 'headers']);
    }

    public function head(): void
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('template_redirect', 'rest_output_link_header', 11);

        add_filter('emoji_svg_url', '__return_false');
        add_filter('xmlrpc_enabled', '__return_false');
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    public function headers(array $headers): array
    {
        unset($headers['X-Pingback']);

        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';

        return $headers;
    }
}

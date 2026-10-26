<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

/**
 * Menú de navegación de reserva cuando no hay uno asignado.
 */
final class Nav
{
    /**
     * @param array<string, mixed> $args
     */
    public static function fallback(array $args = []): void
    {
        $categories = get_categories([
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => 5,
            'parent'     => 0,
            'hide_empty' => true,
        ]);

        $blogUrl = get_option('page_for_posts')
            ? get_permalink((int) get_option('page_for_posts'))
            : home_url('/');

        $class = isset($args['menu_class']) && is_string($args['menu_class']) && $args['menu_class'] !== ''
            ? $args['menu_class']
            : 'nav__list';

        echo '<ul class="' . esc_attr($class) . '">';
        echo '<li class="menu-item"><a href="' . esc_url(home_url('/')) . '">inicio</a></li>';

        foreach ($categories as $category) {
            printf(
                '<li class="menu-item"><a href="%s">%s</a></li>',
                esc_url((string) get_category_link($category)),
                esc_html(mb_strtolower($category->name))
            );
        }

        echo '<li class="menu-item"><a href="' . esc_url((string) $blogUrl) . '">archivo</a></li>';
        echo '</ul>';
    }
}

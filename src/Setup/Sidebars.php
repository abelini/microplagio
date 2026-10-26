<?php

declare(strict_types=1);

namespace Theme\Microplagio\Setup;

/**
 * Barras laterales y widgets.
 */
final class Sidebars
{
    public function register(): void
    {
        add_action('widgets_init', [$this, 'registerSidebars']);
    }

    public function registerSidebars(): void
    {
        register_sidebar([
            'name'          => __('Pie · columna 1', 'microplagio'),
            'id'            => 'footer-1',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget__title">',
            'after_title'   => '</h2>',
        ]);
    }
}

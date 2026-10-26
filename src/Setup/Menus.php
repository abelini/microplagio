<?php

declare(strict_types=1);

namespace Theme\Microplagio\Setup;

/**
 * Ubicaciones de menú del tema.
 */
final class Menus
{
    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'registerMenus']);
    }

    public function registerMenus(): void
    {
        register_nav_menus([
            'primary' => 'Menú principal',
            'footer'  => 'Menú del pie',
            'social'  => 'Redes sociales',
        ]);
    }
}

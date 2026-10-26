<?php

declare(strict_types=1);

namespace Theme\Microplagio;

use Theme\Microplagio\Code\BlockRenderer;
use Theme\Microplagio\Setup\Cleanup;
use Theme\Microplagio\Setup\Customizer;
use Theme\Microplagio\Setup\Editor;
use Theme\Microplagio\Setup\Enqueue;
use Theme\Microplagio\Setup\Menus;
use Theme\Microplagio\Setup\MetaTags;
use Theme\Microplagio\Setup\Sidebars;
use Theme\Microplagio\Setup\ThemeSetup;

/**
 * Contenedor principal del tema.
 *
 * Registra todos los módulos una sola vez y expone rutas y versión.
 */
final class Theme
{
    private static ?self $instance = null;

    /**
     * @var array<int, object>
     */
    private array $modules = [];

    private bool $booted = false;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->booted = true;

        $this->modules = [
            new ThemeSetup(),
            new Enqueue(),
            new Menus(),
            new Sidebars(),
            new Editor(),
            new Customizer(),
            new Cleanup(),
            new MetaTags(),
            new BlockRenderer(),
        ];

        foreach ($this->modules as $module) {
            if (method_exists($module, 'register')) {
                $module->register();
            }
        }
    }

    public function version(): string
    {
        $version = wp_get_theme()->get('Version');

        return is_string($version) && $version !== '' ? $version : '1.0.0';
    }

    public function path(string $relative = ''): string
    {
        return get_theme_file_path($relative);
    }

    public function uri(string $relative = ''): string
    {
        return get_theme_file_uri($relative);
    }
}

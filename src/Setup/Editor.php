<?php

declare(strict_types=1);

namespace Theme\Microplagio\Setup;

/**
 * Estilos del editor de bloques.
 */
final class Editor
{
    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'editorStyles'], 20);
    }

    public function editorStyles(): void
    {
        $path = get_theme_file_path('assets/build/.vite/manifest.json');

        if (! file_exists($path)) {
            return;
        }

        $manifest = json_decode((string) file_get_contents($path), true);

        if (! is_array($manifest) || ! isset($manifest['resources/js/app.js']['css'])) {
            return;
        }

        $styles = [];

        foreach ((array) $manifest['resources/js/app.js']['css'] as $css) {
            $styles[] = get_theme_file_uri('assets/build/' . $css);
        }

        if ($styles !== []) {
            add_editor_style($styles);
        }
    }
}

<?php

declare(strict_types=1);

namespace Theme\Microplagio\Setup;

use Theme\Microplagio\Code\Languages;
use Theme\Microplagio\Theme;

/**
 * Carga de assets compilados por Vite.
 *
 * En desarrollo se conecta al servidor de Vite (5173) cuando
 * `VITE_DEV` está definido en wp-config.php. En producción lee el
 * manifest y sirve los archivos con hash de assets/build/.
 */
final class Enqueue
{
    private const DEV_SERVER = 'http://localhost:5173';

    /**
     * @var array<string, mixed>|null
     */
    private ?array $manifest = null;

    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'frontend']);
        add_action('enqueue_block_editor_assets', [$this, 'editor']);
        add_filter('script_loader_tag', [$this, 'typeModule'], 10, 3);
    }

    public function frontend(): void
    {
        $version = Theme::getInstance()->version();

        if ($this->isDev()) {
            wp_enqueue_script('mp-vite-client', self::DEV_SERVER . '/@vite/client', [], null, false);
            wp_enqueue_script('mp-app', self::DEV_SERVER . '/resources/js/app.js', [], null, true);
            $this->localize('mp-app');

            return;
        }

        $entry = $this->entry('resources/js/app.js');

        if ($entry === null) {
            return;
        }

        $base = get_theme_file_uri('assets/build/');

        foreach ($entry['css'] ?? [] as $index => $css) {
            wp_enqueue_style('mp-app-' . $index, $base . $css, [], $version);
        }

        wp_enqueue_script('mp-app', $base . $entry['file'], [], $version, true);

        foreach ($entry['imports'] ?? [] as $import) {
            $chunk = $this->entry($import);

            if ($chunk !== null) {
                wp_enqueue_script('mp-preload-' . md5($import), $base . $chunk['file'], [], $version, true);
            }
        }

        $this->localize('mp-app');
    }

    public function editor(): void
    {
        $deps = [
            'wp-blocks',
            'wp-hooks',
            'wp-compose',
            'wp-block-editor',
            'wp-components',
            'wp-element',
            'wp-i18n',
        ];

        if ($this->isDev()) {
            wp_enqueue_script('mp-editor', self::DEV_SERVER . '/resources/js/editor.js', $deps, null, true);
            $this->localizeEditor();

            return;
        }

        $entry = $this->entry('resources/js/editor.js');

        if ($entry === null) {
            return;
        }

        wp_enqueue_script(
            'mp-editor',
            get_theme_file_uri('assets/build/' . $entry['file']),
            $deps,
            Theme::getInstance()->version(),
            true
        );

        $this->localizeEditor();
    }

    /**
     * Añade type="module" a los scripts de Vite (dev y prod).
     *
     * @param string $tag    Etiqueta generada por WordPress.
     * @param string $handle Identificador del script.
     * @param string $src    URL del script.
     */
    public function typeModule(string $tag, string $handle, string $src): string
    {
        if (! in_array($handle, ['mp-app', 'mp-editor', 'mp-vite-client'], true)) {
            return $tag;
        }

        if (str_contains($tag, 'type="module"')) {
            return $tag;
        }

        return str_replace('<script ', '<script type="module" ', $tag);
    }

    private function localize(string $handle): void
    {
        $data = [
            'restUrl'  => esc_url_raw(rest_url()),
            'homeUrl'  => esc_url_raw(home_url('/')),
            'searchUrl' => esc_url_raw(home_url('/')),
            'strings'  => [
                'searching' => 'Buscando…',
                'noResults' => 'Sin resultados. Ni una coincidencia.',
                'error'     => 'No se pudo buscar. Inténtalo de nuevo.',
                'results'   => 'resultados',
                'copy'      => 'Copiar',
                'copied'    => '¡Copiado!',
                'copyError' => 'No se pudo copiar',
            ],
        ];

        wp_add_inline_script(
            $handle,
            'window.mpData = ' . wp_json_encode($data) . ';',
            'before'
        );
    }

    private function localizeEditor(): void
    {
        wp_add_inline_script(
            'mp-editor',
            'window.mpCodeLanguages = ' . wp_json_encode(Languages::options()) . ';',
            'before'
        );
    }

    private function isDev(): bool
    {
        return defined('VITE_DEV') && (bool) VITE_DEV;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function entry(string $key): ?array
    {
        $manifest = $this->manifest();

        if ($manifest === null || ! isset($manifest[$key]) || ! is_array($manifest[$key])) {
            return null;
        }

        return $manifest[$key];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function manifest(): ?array
    {
        if ($this->manifest !== null) {
            return $this->manifest;
        }

        $path = get_theme_file_path('assets/build/.vite/manifest.json');

        if (! file_exists($path)) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded)) {
            return null;
        }

        return $this->manifest = $decoded;
    }
}

<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

/**
 * Opciones del tema con valores por defecto.
 */
final class Options
{
    /**
     * @var array<string, string>
     */
    public const DEFAULTS = [
        'mp_status'        => 'blog en línea',
        'mp_kicker'        => '// hola, mundo',
        'mp_intro'         => 'Escribo sobre <mark>tecnología</mark>, ciencia y las rarezas que encuentro en el camino.',
        'mp_intro_text'    => 'Apuntes, experimentos y cosas que probablemente solo a mí me parecen interesantes. Sin prisa, sin algoritmo.',
        'mp_now'           => "construyendo: este blog\nescuchando: synthwave de fondo\njugando: Hades II",
        'mp_book_title'    => 'El nombre del viento',
        'mp_book_progress' => '42',
        'mp_author'        => 'Abel Botello',
        'mp_start_year'    => '2015',
        'mp_branch'        => 'main',
        'mp_commit_hash'   => 'eb3a162',
        'mp_footer_note'   => 'Hecho con WordPress, Vite y demasiado café.',
        'mp_social_github'   => '',
        'mp_social_x'        => '',
        'mp_social_mastodon' => '',
        'mp_social_linkedin' => '',
        'mp_social_email'    => '',
    ];

    public static function get(string $key): string
    {
        $default = self::DEFAULTS[$key] ?? '';
        $value   = get_theme_mod($key, $default);

        return is_string($value) ? $value : $default;
    }

    public static function has(string $key): bool
    {
        return trim(self::get($key)) !== '';
    }

    /**
     * Valor entero acotado entre 0 y 100.
     */
    public static function percent(string $key): int
    {
        $value = (int) self::get($key);

        return max(0, min(100, $value));
    }

    /**
     * Nombre de usuario decorativo para la terminal (primer nombre en minúsculas).
     */
    public static function handle(): string
    {
        $author = trim(self::get('mp_author'));
        $first  = preg_split('/\s+/u', $author)[0] ?? 'abel';

        return mb_strtolower($first);
    }
}

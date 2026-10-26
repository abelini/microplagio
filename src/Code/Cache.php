<?php

declare(strict_types=1);

namespace Theme\Microplagio\Code;

/**
 * Caché de bloques resaltados.
 *
 * Evita re-ejecutar el highlighter en cada petición. La clave incluye
 * una versión para invalidar todo al cambiar el algoritmo o el tema.
 */
final class Cache
{
    private const VERSION = 'v1';
    private const TTL = 7 * DAY_IN_SECONDS;

    public static function get(string $language, string $code): ?string
    {
        $value = get_transient(self::key($language, $code));

        return is_string($value) ? $value : null;
    }

    public static function set(string $language, string $code, string $html): void
    {
        set_transient(self::key($language, $code), $html, self::TTL);
    }

    private static function key(string $language, string $code): string
    {
        return 'mp_code_' . self::VERSION . '_' . md5($language . '|' . $code);
    }
}

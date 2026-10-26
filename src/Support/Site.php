<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

/**
 * Datos calculados del sitio para la terminal y los widgets.
 */
final class Site
{
    /**
     * Fecha del primer artículo publicado (cacheada por petición).
     */
    public static function firstPostDate(): ?\DateTimeImmutable
    {
        static $loaded = false;
        static $date = null;

        if ($loaded) {
            return $date;
        }

        $loaded = true;

        $ids = get_posts([
            'numberposts'      => 1,
            'post_type'        => 'post',
            'post_status'      => 'publish',
            'orderby'          => 'date',
            'order'            => 'ASC',
            'fields'           => 'ids',
            'no_found_rows'    => true,
            'suppress_filters' => true,
        ]);

        if ($ids === []) {
            return null;
        }

        $timestamp = (int) get_post_time('U', true, (int) $ids[0], true);

        if ($timestamp <= 0) {
            return null;
        }

        $date = (new \DateTimeImmutable('@' . $timestamp))->setTimezone(wp_timezone());

        return $date;
    }

    /**
     * Tiempo desde el primer artículo, en formato `11y 2m`.
     */
    public static function uptime(): string
    {
        $first = self::firstPostDate();

        if (! $first instanceof \DateTimeImmutable) {
            return '—';
        }

        $diff = $first->diff(new \DateTimeImmutable('now', wp_timezone()));

        if ($diff->y <= 0 && $diff->m <= 0) {
            return sprintf('%dd', max(0, $diff->d));
        }

        return sprintf('%dy %dm', $diff->y, $diff->m);
    }

    /**
     * Número de artículos publicados.
     */
    public static function publishedCount(): int
    {
        $counts = wp_count_posts('post');

        return isset($counts->publish) ? (int) $counts->publish : 0;
    }
}

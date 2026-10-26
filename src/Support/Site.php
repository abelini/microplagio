<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

use DateInterval;
use DateTimeImmutable;

/**
 * Datos calculados del sitio para la terminal y los widgets.
 */
final class Site
{
    /**
     * Fecha del primer artículo publicado (cacheada por petición).
     */
    public static function firstPostDate(): DateTimeImmutable
    {
        static $date = null;

        if ($date !== null) {
            return $date;
        }

        $ids = get_posts([
            'numberposts' => 1,
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'ASC',
            'fields' => 'ids',
            'no_found_rows' => true,
            'suppress_filters' => true,
        ]);

        return $date = (new DateTimeImmutable('@' . (int) get_post_time('U', true, (int) $ids[0], true)))
            ->setTimezone(wp_timezone());
    }

    /**
     * Intervalo desde el primer artículo hasta ahora.
     */
    public static function uptimeInterval(): DateInterval
    {
        return self::firstPostDate()->diff(new DateTimeImmutable('now', wp_timezone()));
    }

    /**
     * Tiempo desde el primer artículo, en formato `11y 2m`.
     */
    public static function uptime(): string
    {
        $diff = self::uptimeInterval();

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

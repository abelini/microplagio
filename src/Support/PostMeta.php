<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

use WP_Term;

/**
 * Metadatos y utilidades de los artículos.
 */
final class PostMeta
{
    /**
     * @var array<int, string>
     */
    private const MONTHS = [
        1  => 'ene',
        2  => 'feb',
        3  => 'mar',
        4  => 'abr',
        5  => 'may',
        6  => 'jun',
        7  => 'jul',
        8  => 'ago',
        9  => 'sep',
        10 => 'oct',
        11 => 'nov',
        12 => 'dic',
    ];

    public static function date(?int $postId = null): string
    {
        $timestamp = (int) get_post_time('U', true, $postId, true);
        $month     = (int) wp_date('n', $timestamp);

        return sprintf(
            '%d %s %s',
            (int) wp_date('j', $timestamp),
            self::MONTHS[$month] ?? '',
            wp_date('Y', $timestamp)
        );
    }

    public static function dateIso(?int $postId = null): string
    {
        return (string) get_the_date(DATE_W3C, $postId);
    }

    public static function wordCount(?int $postId = null): int
    {
        $postId  = $postId ?? (int) get_the_ID();
        $content = (string) get_post_field('post_content', $postId);
        $content = wp_strip_all_tags(strip_shortcodes($content));
        $words   = preg_split('/\s+/u', trim($content)) ?: [];
        $words   = array_filter($words, static fn (string $word): bool => $word !== '');

        return count($words);
    }

    public static function readingTime(?int $postId = null): string
    {
        $minutes = max(1, (int) ceil(self::wordCount($postId) / 220));

        return sprintf('%d min', $minutes);
    }

    /**
     * Estadísticas decorativas de diff para la ventana del destacado.
     *
     * @return array{additions: int, deletions: int}
     */
    public static function diffStats(?int $postId = null): array
    {
        return [
            'additions' => self::wordCount($postId),
            'deletions' => 0,
        ];
    }

    public static function primaryCategory(?int $postId = null): ?WP_Term
    {
        $postId ??= (int) get_the_ID();

        $terms = get_the_category($postId);

        if ($terms === []) {
            return null;
        }

        usort($terms, static function (WP_Term $a, WP_Term $b): int {
            $depthA = count(get_ancestors($a->term_id, 'category', 'taxonomy'));
            $depthB = count(get_ancestors($b->term_id, 'category', 'taxonomy'));

            return $depthB <=> $depthA ?: strcasecmp($a->name, $b->name);
        });

        return $terms[0] ?? null;
    }

    /**
     * Devuelve la ruta completa de una categoría (de raíz a hoja).
     *
     * @return array<int, WP_Term>
     */
    public static function categoryPath(WP_Term $term): array
    {
        $path = [];

        foreach (array_reverse(get_ancestors($term->term_id, $term->taxonomy, 'taxonomy')) as $id) {
            $ancestor = get_term($id, $term->taxonomy);

            if ($ancestor instanceof WP_Term) {
                $path[] = $ancestor;
            }
        }

        $path[] = $term;

        return $path;
    }

    public static function excerpt(int $words = 26): string
    {
        return wp_trim_words(wp_strip_all_tags((string) get_the_excerpt()), $words, '…');
    }

    public static function commentsLabel(?int $postId = null): string
    {
        $count = (int) get_comments_number($postId);

        return sprintf(
            /* translators: %s: número de comentarios. */
            ($count === 1 ? '%s comentario' : '%s comentarios'),
            number_format_i18n($count)
        );
    }

    public static function avatar(int $size = 48, string $class = 'avatar'): string
    {
        $avatar = get_avatar(
            (int) get_the_author_meta('ID'),
            $size,
            '',
            get_the_author(),
            [
                'class'   => $class,
                'loading' => 'lazy',
            ]
        );

        return is_string($avatar) ? $avatar : '';
    }

    public static function authorBio(): string
    {
        $bio = (string) get_the_author_meta('description');

        if (trim($bio) === '') {
            $bio = 'Escribo en Microplagio sobre las cosas que me quitan el sueño: código, ciencia, cine y algún que otro experimento fallido.';
        }

        return $bio;
    }
}

<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

use WP_Term;


/**
 * Migas de pan con estética de ruta de archivos.
 */
final class Breadcrumbs
{
    public static function render(): void
    {
        $parts = self::parts();

        if ($parts === []) {
            return;
        }

        echo '<nav class="crumbs" aria-label="' . esc_attr('Ruta de navegación') . '">';
        echo '<a class="crumbs__root" href="' . esc_url(home_url('/')) . '">~</a>';

        foreach ($parts as $part) {
            echo '<span class="crumbs__sep" aria-hidden="true">/</span>';

            if ($part['url'] !== null) {
                echo '<a class="crumbs__link" href="' . esc_url($part['url']) . '">' . esc_html($part['label']) . '</a>';
            } else {
                echo '<span class="crumbs__current" aria-current="page">' . esc_html($part['label']) . '</span>';
            }
        }

        echo '</nav>';
    }

    /**
     * @return array<int, array{label: string, url: string|null}>
     */
    private static function parts(): array
    {
        $parts = [];

        if (is_front_page() || is_home()) {
            return $parts;
        }

        if (is_singular()) {
            $term = is_singular('post') ? PostMeta::primaryCategory() : null;

            if ($term instanceof WP_Term) {
                foreach (PostMeta::categoryPath($term) as $crumb) {
                    $parts[] = [
                        'label' => $crumb->slug,
                        'url'   => get_term_link($crumb),
                    ];
                }
            }

            $parts[] = ['label' => wp_trim_words(get_the_title(), 8, '…'), 'url' => null];

            return $parts;
        }

        if (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();

            if ($term instanceof WP_Term) {
                if (is_tag()) {
                    $parts[] = ['label' => '#' . $term->slug, 'url' => null];

                    return $parts;
                }

                foreach (PostMeta::categoryPath($term) as $crumb) {
                    $parts[] = [
                        'label' => $crumb->slug,
                        'url'   => $crumb->term_id === $term->term_id ? null : get_term_link($crumb),
                    ];
                }
            }

            return $parts;
        }

        if (is_search()) {
            $parts[] = ['label' => 'grep "' . get_search_query() . '"', 'url' => null];

            return $parts;
        }

        if (is_author()) {
            $parts[] = ['label' => '@' . get_the_author_meta('nickname', (int) get_queried_object_id()), 'url' => null];

            return $parts;
        }

        if (is_404()) {
            $parts[] = ['label' => '404', 'url' => null];

            return $parts;
        }

        if (is_archive()) {
            $parts[] = ['label' => wp_strip_all_tags(get_the_archive_title()), 'url' => null];
        }

        return $parts;
    }
}

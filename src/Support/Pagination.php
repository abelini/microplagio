<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

/**
 * Paginación del listado de artículos.
 */
final class Pagination
{
    public static function render(): void
    {
        $links = paginate_links([
            'type'      => 'array',
            'mid_size'  => 1,
            'end_size'  => 1,
            'prev_text' => '← ' . __('anterior', 'microplagio'),
            'next_text' => __('siguiente', 'microplagio') . ' →',
        ]);

        if (is_array($links) && $links !== []) {
            echo '<nav class="pagination" aria-label="' . esc_attr__('Paginación', 'microplagio') . '">';

            foreach ($links as $link) {
                echo '<span class="pagination__item">' . $link . '</span>';
            }

            echo '</nav>';
        }

        global $wp_query;

        $total   = isset($wp_query->found_posts) ? (int) $wp_query->found_posts : 0;
        $max     = isset($wp_query->max_num_pages) ? (int) $wp_query->max_num_pages : 1;
        $current = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

        $entries = sprintf(
            /* translators: %s: número de artículos. */
            _n('%s entrada', '%s entradas', $total, 'microplagio'),
            number_format_i18n($total)
        );

        echo '<p class="eof"><span>// ' . esc_html__('fin del archivo', 'microplagio') . ' · ' . esc_html($entries);

        if ($max > 1) {
            echo ' · ' . esc_html(sprintf(
                /* translators: 1: página actual, 2: total de páginas. */
                __('página %1$d/%2$d', 'microplagio'),
                $current,
                $max
            ));
        }

        echo '</span></p>';
    }
}

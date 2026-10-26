<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

/**
 * Genera un índice a partir de los encabezados del contenido.
 */
final class Toc
{
    /**
     * Añade ids a los h2/h3 y devuelve el HTML y los elementos del índice.
     *
     * @return array{html: string, items: array<int, array{id: string, text: string, level: int}>}
     */
    public static function inject(string $html): array
    {
        $items = [];
        $used  = [];

        $html = (string) preg_replace_callback(
            '#<h([23])([^>]*)>(.*?)</h\1>#is',
            static function (array $matches) use (&$items, &$used): string {
                $level = (int) $matches[1];
                $attrs = $matches[2];
                $inner = $matches[3];
                $text  = trim(wp_strip_all_tags($inner));

                if ($text === '') {
                    return $matches[0];
                }

                $existing = '';

                if (preg_match('/\bid=(["\'])(.*?)\1/i', $attrs, $idMatch)) {
                    $existing = $idMatch[2];
                    $attrs    = (string) preg_replace('/\s*\bid=(["\']).*?\1/i', '', $attrs);
                }

                $id = $existing !== '' ? $existing : self::uniqueId($text, $used);
                $used[$id] = true;

                $items[] = [
                    'id'    => $id,
                    'text'  => $text,
                    'level' => $level,
                ];

                return sprintf('<h%d%s id="%s">%s</h%d>', $level, $attrs, esc_attr($id), $inner, $level);
            },
            $html
        );

        return ['html' => $html, 'items' => $items];
    }

    /**
     * @param array<int, array{id: string, text: string, level: int}> $items
     */
    public static function render(array $items): string
    {
        if (count($items) < 2) {
            return '';
        }

        $html = '<nav class="toc" aria-label="' . esc_attr__('Índice del artículo', 'microplagio') . '">';
        $html .= '<p class="toc__title">// ' . esc_html__('índice', 'microplagio') . '</p>';
        $html .= '<ol class="toc__list">';

        foreach ($items as $item) {
            $html .= sprintf(
                '<li class="toc__item toc__item--h%d"><a class="toc__link" href="#%s" data-toc-link>%s</a></li>',
                (int) $item['level'],
                esc_attr($item['id']),
                esc_html($item['text'])
            );
        }

        $html .= '</ol></nav>';

        return $html;
    }

    /**
     * @param array<string, bool> $used
     */
    private static function uniqueId(string $text, array $used): string
    {
        $base = sanitize_title($text);

        if ($base === '') {
            $base = 'seccion';
        }

        $id      = $base;
        $counter = 2;

        while (isset($used[$id])) {
            $id = $base . '-' . $counter;
            $counter++;
        }

        return $id;
    }
}

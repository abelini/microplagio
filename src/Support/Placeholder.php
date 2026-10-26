<?php

declare(strict_types=1);

namespace Theme\Microplagio\Support;

/**
 * Ilustraciones de reserva para artículos sin imagen destacada.
 *
 * Genera SVG deterministas (según el título) para que cada tarjeta
 * tenga una identidad propia sin depender de archivos externos.
 */
final class Placeholder
{
    /**
     * @var array<int, array{0: string, 1: string, 2: string}>
     */
    private const PALETTES = [
        ['#E8E3D9', '#14161A', '#2C46F5'],
        ['#14161A', '#FAF7F2', '#FFE45C'],
        ['#DCE4FF', '#14161A', '#2C46F5'],
        ['#FFE45C', '#14161A', '#2C46F5'],
        ['#DCEFE5', '#14161A', '#157347'],
        ['#F4DEDC', '#14161A', '#B23A2E'],
    ];

    public static function url(string $seed, string $label = ''): string
    {
        $hash  = crc32($seed);
        $pal   = self::PALETTES[abs($hash) % count(self::PALETTES)];
        $index = abs($hash) % 4;

        $bg     = $pal[0];
        $ink    = $pal[1];
        $accent = $pal[2];

        $label = trim($label) !== '' ? mb_strtolower($label) : 'microplagio';
        $label = mb_substr($label, 0, 24);

        $pattern = match ($index) {
            0 => sprintf(
                '<path d="M0 0h720v405H0z" fill="%1$s"/><g stroke="%3$s" stroke-opacity=".22" stroke-width="1">%2$s</g>',
                $bg,
                implode('', array_map(
                    static fn (int $x): string => sprintf('<path d="M%d 0v405"/>', $x),
                    range(0, 720, 40)
                )),
                $ink
            ),
            1 => sprintf(
                '<path d="M0 0h720v405H0z" fill="%1$s"/><g fill="none" stroke="%3$s" stroke-width="1.5" stroke-opacity=".28">%2$s</g>',
                $bg,
                implode('', array_map(
                    static fn (int $i): string => sprintf('<circle cx="360" cy="202" r="%d"/>', 40 + $i * 42),
                    range(1, 5)
                )),
                $ink
            ),
            2 => sprintf(
                '<path d="M0 0h720v405H0z" fill="%1$s"/><g stroke="%3$s" stroke-width="18" stroke-opacity=".18">%2$s</g>',
                $bg,
                implode('', array_map(
                    static fn (int $x): string => sprintf('<path d="M%d -40l80 485"/>', $x),
                    range(-200, 760, 90)
                )),
                $accent
            ),
            default => sprintf(
                '<path d="M0 0h720v405H0z" fill="%1$s"/><g fill="%3$s" fill-opacity=".2">%2$s</g>',
                $bg,
                implode('', array_map(
                    static fn (int $y): string => sprintf('<rect x="%d" y="%d" width="%d" height="10" rx="5"/>', 40 + ($y % 3) * 24, 40 + $y, 120 + ($y % 5) * 30),
                    range(0, 300, 38)
                )),
                $ink
            ),
        };

        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 720 405" role="img">%s'
            . '<g font-family="ui-monospace, monospace" fill="%s">'
            . '<text x="40" y="348" font-size="14" letter-spacing="2">%s</text>'
            . '<text x="680" y="348" font-size="14" text-anchor="end" opacity=".55">jpg · 1280×720</text>'
            . '</g></svg>',
            $pattern,
            $ink,
            esc_html($label)
        );

        return 'data:image/svg+xml,' . rawurlencode($svg);
    }
}

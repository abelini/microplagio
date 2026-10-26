<?php

declare(strict_types=1);

namespace Theme\Microplagio\Setup;

use Theme\Microplagio\Support\Options;
use WP_Customize_Manager;

/**
 * Opciones del tema en el Personalizador.
 */
final class Customizer
{
    public function register(): void
    {
        add_action('customize_register', [$this, 'registerCustomizer']);
    }

    public function registerCustomizer(WP_Customize_Manager $customize): void
    {
        $customize->add_section('microplagio', [
            'title'       => 'Microplagio',
            'description' => 'Textos de portada, terminal, estado y redes sociales.',
            'priority'    => 30,
        ]);

        $this->text($customize, 'mp_status', 'Estado de la barra superior');
        $this->text($customize, 'mp_kicker', 'Kicker de portada');
        $this->textarea($customize, 'mp_intro', 'Manifiesto de portada', 4, 'wp_kses_post');
        $this->textarea($customize, 'mp_intro_text', 'Entradilla de portada', 4);
        $this->textarea($customize, 'mp_now', 'Panel «ahora» (una línea por elemento)', 6);
        $this->text($customize, 'mp_book_title', 'Libro en curso');
        $this->number($customize, 'mp_book_progress', 'Progreso de lectura (%)', 0, 100);
        $this->text($customize, 'mp_author', 'Autor');
        $this->number($customize, 'mp_start_year', 'Año de inicio', 1990, 2100);
        $this->text($customize, 'mp_branch', 'Rama de git');
        $this->text($customize, 'mp_commit_hash', 'Hash del último commit');
        $this->text($customize, 'mp_footer_note', 'Nota del pie');

        $this->url($customize, 'mp_social_github', 'GitHub');
        $this->url($customize, 'mp_social_x', 'X / Twitter');
        $this->url($customize, 'mp_social_mastodon', 'Mastodon');
        $this->url($customize, 'mp_social_linkedin', 'LinkedIn');
        $this->url($customize, 'mp_social_email', 'Email (mailto)');
    }

    private function text(WP_Customize_Manager $customize, string $id, string $label, string $default = ''): void
    {
        $customize->add_setting($id, [
            'default'           => Options::DEFAULTS[$id] ?? $default,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ]);

        $customize->add_control($id, [
            'label'   => $label,
            'section' => 'microplagio',
            'type'    => 'text',
        ]);
    }

    private function textarea(WP_Customize_Manager $customize, string $id, string $label, int $rows = 4, string $sanitize = 'sanitize_textarea_field'): void
    {
        $customize->add_setting($id, [
            'default'           => Options::DEFAULTS[$id] ?? '',
            'sanitize_callback' => $sanitize,
            'transport'         => 'refresh',
        ]);

        $customize->add_control($id, [
            'label'   => $label,
            'section' => 'microplagio',
            'type'    => 'textarea',
            'input_attrs' => ['rows' => $rows],
        ]);
    }

    private function number(WP_Customize_Manager $customize, string $id, string $label, int $min, int $max): void
    {
        $customize->add_setting($id, [
            'default'           => Options::DEFAULTS[$id] ?? '',
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ]);

        $customize->add_control($id, [
            'label'   => $label,
            'section' => 'microplagio',
            'type'    => 'number',
            'input_attrs' => [
                'min' => $min,
                'max' => $max,
            ],
        ]);
    }

    private function url(WP_Customize_Manager $customize, string $id, string $label): void
    {
        $customize->add_setting($id, [
            'default'           => Options::DEFAULTS[$id] ?? '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ]);

        $customize->add_control($id, [
            'label'   => $label,
            'section' => 'microplagio',
            'type'    => 'url',
        ]);
    }
}

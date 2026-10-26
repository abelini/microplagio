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
            'title'       => __('Microplagio', 'microplagio'),
            'description' => __('Textos de portada, terminal, estado y redes sociales.', 'microplagio'),
            'priority'    => 30,
        ]);

        $this->text($customize, 'mp_status', __('Estado de la barra superior', 'microplagio'));
        $this->text($customize, 'mp_kicker', __('Kicker de portada', 'microplagio'));
        $this->textarea($customize, 'mp_intro', __('Manifiesto de portada', 'microplagio'), 4, 'wp_kses_post');
        $this->textarea($customize, 'mp_intro_text', __('Entradilla de portada', 'microplagio'), 4);
        $this->textarea($customize, 'mp_now', __('Panel «ahora» (una línea por elemento)', 'microplagio'), 6);
        $this->text($customize, 'mp_book_title', __('Libro en curso', 'microplagio'));
        $this->number($customize, 'mp_book_progress', __('Progreso de lectura (%)', 'microplagio'), 0, 100);
        $this->text($customize, 'mp_author', __('Autor', 'microplagio'));
        $this->number($customize, 'mp_start_year', __('Año de inicio', 'microplagio'), 1990, 2100);
        $this->text($customize, 'mp_branch', __('Rama de git', 'microplagio'));
        $this->text($customize, 'mp_commit_hash', __('Hash del último commit', 'microplagio'));
        $this->text($customize, 'mp_footer_note', __('Nota del pie', 'microplagio'));

        $this->url($customize, 'mp_social_github', __('GitHub', 'microplagio'));
        $this->url($customize, 'mp_social_x', __('X / Twitter', 'microplagio'));
        $this->url($customize, 'mp_social_mastodon', __('Mastodon', 'microplagio'));
        $this->url($customize, 'mp_social_linkedin', __('LinkedIn', 'microplagio'));
        $this->url($customize, 'mp_social_email', __('Email (mailto)', 'microplagio'));
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

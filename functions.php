<?php

declare(strict_types=1);

/**
 * Punto de entrada del tema Microplagio.
 *
 * @package Microplagio
 */

if (! defined('ABSPATH')) {
    exit;
}

$autoload = __DIR__ . '/vendor/autoload.php';

if (! file_exists($autoload)) {
    wp_die(
        'Falta el directorio <code>vendor/</code>. Ejecuta <code>composer install</code> antes de activar el tema.',
        'Microplagio',
        ['response' => 500]
    );
}

require_once $autoload;

Theme\Microplagio\Theme::getInstance()->boot();

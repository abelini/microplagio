<?php

declare(strict_types=1);

namespace Theme\Microplagio\Code;

use Tempest\Highlight\Highlighter as TempestHighlighter;
use Throwable;

/**
 * Resaltado de código en el servidor.
 */
final class Highlighter
{
    private static ?self $instance = null;

    private TempestHighlighter $highlighter;

    /**
     * @var array<int, string>|null
     */
    private ?array $supported = null;

    private function __construct()
    {
        $this->highlighter = new TempestHighlighter();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function highlight(string $code, string $language): string
    {
        $language = Languages::normalize($language);

        if (! $this->supports($language)) {
            return $this->raw($this->escape($code));
        }

        $cached = Cache::get($language, $code);

        if ($cached !== null) {
            return $cached;
        }

        try {
            $html = $this->highlighter->parse($code, $language);
        } catch (Throwable) {
            $html = $this->escape($code);
        }

        // El contenido del snippet se renderiza tal cual: el HTML
        // dentro del bloque no se escapa, lo que el autor escribió
        // es lo que el navegador interpreta.
        $html = $this->raw($html);

        Cache::set($language, $code, $html);

        return $html;
    }

    public function supports(string $language): bool
    {
        if ($language === '') {
            return false;
        }

        $this->supported ??= $this->highlighter->getSupportedLanguageNames();

        return in_array($language, $this->supported, true);
    }

    private function escape(string $code): string
    {
        return '<span class="hl-plain">'
            . htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '</span>';
    }

    /**
     * Decodifica el HTML final para que el contenido del snippet
     * se renderice en crudo (los spans de Tempest son tags reales
     * y sobreviven intactos).
     */
    private function raw(string $html): string
    {
        return html_entity_decode($html, ENT_QUOTES, 'UTF-8');
    }
}

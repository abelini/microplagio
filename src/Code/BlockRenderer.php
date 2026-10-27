<?php

declare(strict_types=1);

namespace Theme\Microplagio\Code;

/**
 * Sustituye el render del bloque core/code por un panel propio.
 *
 * Es compatible con los atributos que guarda el plugin
 * Syntax-highlighting Code Block (`language`, `showLineNumbers`,
 * `wrapLines`, `highlightedLines`), así que se puede desactivar el
 * plugin sin migrar el contenido existente.
 */
final class BlockRenderer
{
    public function register(): void
    {
        add_filter('render_block', [$this, 'render'], 10, 2);
    }

    /**
     * @param array<string, mixed> $block
     */
    public function render(string $content, array $block): string
    {
        if (($block['blockName'] ?? '') !== 'core/code' || trim($content) === '') {
            return $content;
        }

        // Otro plugin ya resaltó este bloque: no lo tocamos.
        if (str_contains($content, 'shcb-') || str_contains($content, 'hljs')) {
            return $content;
        }

        if (! preg_match('#<pre\b[^>]*>\s*<code\b[^>]*>(.*?)</code>\s*</pre>#is', $content, $matches)) {
            return $content;
        }

        // Normaliza entidades hasta punto fijo (máx. 3 pasadas):
        // corrige tanto el escape simple de WP como el doble
        // (&amp;lt; en la BD, producto de pegar HTML en el editor).
        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($code_string ?? $matches[1], ENT_QUOTES, 'UTF-8');
            if ($decoded === ($code_string ?? $matches[1])) {
                break;
            }
            $code_string = $decoded;
        }
        $code = trim($code_string ?? $matches[1]);
        $code = str_replace(["\r\n", "\r"], "\n", $code);
        $code = rtrim($code);

        if (trim($code) === '') {
            return $content;
        }

        $attrs = is_array($block['attrs'] ?? null) ? $block['attrs'] : [];

        $language = Languages::normalize((string) ($attrs['language'] ?? ''));

        if ($language === '') {
            $language = Languages::guess($code);
        }

        $lineCount   = substr_count($code, "\n") + 1;
        $showNumbers = ! array_key_exists('showLineNumbers', $attrs) || (bool) $attrs['showLineNumbers'];
        $showNumbers = $showNumbers && $lineCount >= 3;
        $wrap        = (bool) ($attrs['wrapLines'] ?? false);
        $highlighted = $this->parseLines($attrs['highlightedLines'] ?? '');

        $html  = Highlighter::getInstance()->highlight($code, $language);
        $lines = $this->splitLines($html, $highlighted);

        $classes = ['mp-code'];

        if ($showNumbers) {
            $classes[] = 'has-line-numbers';
        }

        if ($wrap) {
            $classes[] = 'is-wrapped';
        }

        $label = Languages::label($language);
        $ext   = Languages::extension($language);

        ob_start();
?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-lang="<?php echo esc_attr($language); ?>">
            <div class="mp-code__bar">
                <span class="mp-code__dots" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="mp-code__path">snippet.<?php echo esc_html($ext); ?></span>
                <span class="mp-code__lang"><?php echo esc_html($label); ?></span>
                <button type="button" class="mp-code__copy" data-copy-code>
                    <i class="fa-regular fa-copy"></i>
                    <span data-copy-label><?php echo esc_html('Copiar'); ?></span>
                </button>
            </div>
            <pre class="mp-code__pre" tabindex="0" aria-label="<?php echo esc_attr(sprintf('Código %s', $label)); ?>"><code class="mp-code__code language-<?php echo esc_attr($language); ?>"><?php echo $lines; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML escapado por Tempest. 
                                                                                                                                                                                                ?></code></pre>
        </div>
<?php

        return (string) ob_get_clean();
    }

    /**
     * Divide el HTML resaltado en líneas equilibrando los spans abiertos.
     *
     * @param array<int, bool> $highlighted
     */
    private function splitLines(string $html, array $highlighted): string
    {
        $parts = preg_split('/(<span\b[^>]*>|<\/span>|\n)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        if (! is_array($parts)) {
            return '<span class="mp-code__line">' . $html . '</span>';
        }

        $open    = [];
        $lines   = [];
        $current = '';

        foreach ($parts as $part) {
            if ($part === "\n") {
                $lines[]  = $current . str_repeat('</span>', count($open));
                $current  = implode('', $open);
                continue;
            }

            if (preg_match('/^<span\b/i', $part) === 1) {
                $open[]  = $part;
                $current .= $part;
                continue;
            }

            if (strcasecmp($part, '</span>') === 0) {
                array_pop($open);
                $current .= '</span>';
                continue;
            }

            $current .= $part;
        }

        $lines[] = $current . str_repeat('</span>', count($open));

        $output = '';

        foreach ($lines as $index => $line) {
            $number = $index + 1;
            $class  = 'mp-code__line';

            if (isset($highlighted[$number])) {
                $class .= ' is-highlighted';
            }

            $output .= '<span class="' . $class . '">' . $line . '</span>';
        }

        return $output;
    }

    /**
     * @param mixed $value
     * @return array<int, bool>
     */
    private function parseLines(mixed $value): array
    {
        if (is_array($value)) {
            $value = implode(',', array_map('strval', $value));
        }

        $value = (string) $value;

        if (trim($value) === '') {
            return [];
        }

        $lines = [];

        foreach (explode(',', $value) as $chunk) {
            $chunk = trim($chunk);

            if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $chunk, $range)) {
                $start = (int) $range[1];
                $end   = (int) $range[2];

                if ($start > $end) {
                    [$start, $end] = [$end, $start];
                }

                for ($i = max(1, $start); $i <= min(2000, $end); $i++) {
                    $lines[$i] = true;
                }

                continue;
            }

            if (ctype_digit($chunk)) {
                $lines[(int) $chunk] = true;
            }
        }

        return $lines;
    }
}

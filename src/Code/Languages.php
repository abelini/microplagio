<?php

declare(strict_types=1);

namespace Theme\Microplagio\Code;

/**
 * Catálogo de lenguajes y normalización de alias.
 *
 * Los nombres coinciden con los que entiende tempest/highlight.
 */
final class Languages
{
    /**
     * @var array<string, string>
     */
    private const OPTIONS = [
        ''           => 'Detectar automáticamente',
        'php'        => 'PHP',
        'js'         => 'JavaScript',
        'ts'         => 'TypeScript',
        'jsx'        => 'JSX',
        'tsx'        => 'TSX',
        'python'     => 'Python',
        'bash'       => 'Bash / Shell',
        'sql'        => 'SQL',
        'json'       => 'JSON',
        'yaml'       => 'YAML',
        'html'       => 'HTML',
        'css'        => 'CSS',
        'scss'       => 'SCSS',
        'go'         => 'Go',
        'rust'       => 'Rust',
        'c'          => 'C',
        'cpp'        => 'C++',
        'csharp'     => 'C#',
        'java'       => 'Java',
        'kotlin'     => 'Kotlin',
        'swift'      => 'Swift',
        'ruby'       => 'Ruby',
        'dockerfile' => 'Dockerfile',
        'nginx'      => 'Nginx',
        'apache'     => 'Apache',
        'diff'       => 'Diff',
        'http'       => 'HTTP',
        'twig'       => 'Twig',
        'blade'      => 'Blade',
        'vue'        => 'Vue',
        'svelte'     => 'Svelte',
        'graphql'    => 'GraphQL',
        'toml'       => 'TOML',
        'ini'        => 'INI',
        'dotenv'     => '.env',
        'markdown'   => 'Markdown',
        'xml'        => 'XML',
        'terraform'  => 'Terraform',
        'text'       => 'Texto plano',
    ];

    /**
     * @var array<string, string>
     */
    private const ALIASES = [
        'javascript'     => 'js',
        'node'           => 'js',
        'nodejs'         => 'js',
        'mjs'            => 'js',
        'typescript'     => 'ts',
        'shell'          => 'bash',
        'sh'             => 'bash',
        'zsh'            => 'bash',
        'console'        => 'bash',
        'terminal'       => 'bash',
        'shell-session'  => 'bash',
        'yml'            => 'yaml',
        'c++'            => 'cpp',
        'cplusplus'      => 'cpp',
        'c#'             => 'csharp',
        'cs'             => 'csharp',
        'dotnet'         => 'csharp',
        'plaintext'      => 'text',
        'plain'          => 'text',
        'txt'            => 'text',
        'nohighlight'    => 'text',
        'htm'            => 'html',
        'xhtml'          => 'html',
        'json5'          => 'json',
        'jsonc'          => 'json',
        'docker'         => 'dockerfile',
        'md'             => 'markdown',
        'golang'         => 'go',
        'py'             => 'python',
        'rb'             => 'ruby',
        'rs'             => 'rust',
        'kt'             => 'kotlin',
        'kts'            => 'kotlin',
        'gql'            => 'graphql',
        'tf'             => 'terraform',
        'hcl'            => 'terraform',
        'env'            => 'dotenv',
        'shellsession'   => 'bash',
        'powershell'     => 'text',
        'ps1'            => 'text',
    ];

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return self::OPTIONS;
    }

    public static function normalize(string $language): string
    {
        $language = strtolower(trim($language));

        if ($language === '') {
            return '';
        }

        $language = (string) preg_replace('/^(language|lang)[-_:]/', '', $language);
        $language = trim($language, " \t\n\r\0\x0B`'\"");

        return self::ALIASES[$language] ?? $language;
    }

    public static function label(string $language): string
    {
        $language = self::normalize($language);

        return self::OPTIONS[$language] ?? ucfirst($language);
    }

    public static function extension(string $language): string
    {
        return match ($language) {
            'bash'       => 'sh',
            'python'     => 'py',
            'ruby'       => 'rb',
            'rust'       => 'rs',
            'kotlin'     => 'kt',
            'csharp'     => 'cs',
            'markdown'   => 'md',
            'text'       => 'txt',
            'dotenv'     => 'env',
            'dockerfile' => 'Dockerfile',
            'graphql'    => 'graphql',
            default      => $language,
        };
    }

    /**
     * Heurística conservadora para bloques sin lenguaje declarado.
     */
    public static function guess(string $code): string
    {
        if (str_contains($code, '<?php')) {
            return 'php';
        }

        if (preg_match('/^\s*#!\//', $code)) {
            return 'bash';
        }

        if (preg_match('/^\s*<\?xml/i', $code)) {
            return 'xml';
        }

        if (preg_match('/^\s*(<!doctype html|<html\b|<div\b|<section\b)/i', $code)) {
            return 'html';
        }

        $trimmed = trim($code);

        if ($trimmed !== '' && in_array($trimmed[0], ['{', '['], true)) {
            json_decode($trimmed);

            if (json_last_error() === JSON_ERROR_NONE) {
                return 'json';
            }
        }

        if (preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|CREATE|ALTER|DROP)\b/i', $code)) {
            return 'sql';
        }

        if (preg_match('/^\s*(def |class \w+.*:|import \w+|from [\w.]+ import )/m', $code)) {
            return 'python';
        }

        if (preg_match('/(function\s*\w*\s*\(|=>|console\.(log|warn|error)|document\.|window\.)/', $code)) {
            return 'js';
        }

        if (preg_match('/^\s*(FROM|RUN|CMD|COPY|ENTRYPOINT|WORKDIR)\s/m', $code)) {
            return 'dockerfile';
        }

        if (preg_match('/^\s*(server|location)\s*\{/m', $code)) {
            return 'nginx';
        }

        if (preg_match('/^\s*diff --git/m', $code)) {
            return 'diff';
        }

        if (preg_match('/^\s*\[[a-z0-9_.-]+\]\s*$/im', $code)) {
            return 'ini';
        }

        if (preg_match('/^\s*[A-Z][A-Z0-9_]+=.*$/m', $code)) {
            return 'dotenv';
        }

        return 'text';
    }
}

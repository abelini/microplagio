<?php

declare(strict_types=1);

namespace Theme\Microplagio\Setup;

use Theme\Microplagio\Support\PostMeta;

/**
 * Metadatos sociales y datos estructurados.
 *
 * Se desactiva solo si hay un plugin de SEO conocido activo para
 * evitar etiquetas duplicadas.
 */
final class MetaTags
{
    public function register(): void
    {
        add_action('wp_head', [$this, 'social'], 5);
        add_action('wp_head', [$this, 'schema'], 20);
    }

    public function social(): void
    {
        if ($this->hasSeoPlugin()) {
            return;
        }

        $title       = wp_get_document_title();
        $description = $this->description();
        $image       = $this->image();
        $url         = $this->url();

        echo "\n<!-- Microplagio: metadatos -->\n";

        $this->tag('meta', ['property' => 'og:site_name', 'content' => get_bloginfo('name')]);
        $this->tag('meta', ['property' => 'og:locale', 'content' => get_locale()]);
        $this->tag('meta', ['property' => 'og:type', 'content' => is_single() ? 'article' : 'website']);
        $this->tag('meta', ['property' => 'og:title', 'content' => $title]);
        $this->tag('meta', ['property' => 'og:description', 'content' => $description]);
        $this->tag('meta', ['property' => 'og:url', 'content' => $url]);

        if ($image !== '') {
            $this->tag('meta', ['property' => 'og:image', 'content' => $image]);
        }

        if (is_single()) {
            $this->tag('meta', ['property' => 'article:published_time', 'content' => PostMeta::dateIso()]);
            $this->tag('meta', ['property' => 'article:modified_time', 'content' => (string) get_the_modified_date(DATE_W3C)]);

            foreach (get_the_tags() ?: [] as $tag) {
                $this->tag('meta', ['property' => 'article:tag', 'content' => $tag->name]);
            }
        }

        $this->tag('meta', ['name' => 'twitter:card', 'content' => $image !== '' ? 'summary_large_image' : 'summary']);
        $this->tag('meta', ['name' => 'twitter:title', 'content' => $title]);
        $this->tag('meta', ['name' => 'twitter:description', 'content' => $description]);

        if ($image !== '') {
            $this->tag('meta', ['name' => 'twitter:image', 'content' => $image]);
        }
    }

    public function schema(): void
    {
        if ($this->hasSeoPlugin()) {
            return;
        }

        $graph = [];

        if (is_singular('post')) {
            $graph[] = [
                '@type'            => 'Article',
                '@id'              => get_permalink() . '#article',
                'headline'         => get_the_title(),
                'description'      => $this->description(),
                'datePublished'    => PostMeta::dateIso(),
                'dateModified'     => get_the_modified_date(DATE_W3C),
                'inLanguage'       => get_bloginfo('language'),
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => get_permalink()],
                'author'           => [
                    '@type' => 'Person',
                    'name'  => get_the_author(),
                    'url'   => get_author_posts_url((int) get_the_author_meta('ID')),
                ],
                'publisher'        => [
                    '@type' => 'Organization',
                    'name'  => get_bloginfo('name'),
                ],
            ];

            if (has_post_thumbnail()) {
                $graph[0]['image'] = get_the_post_thumbnail_url(null, 'mp-hero');
            }

            $graph[] = [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => $this->breadcrumbSchema(),
            ];
        } else {
            $graph[] = [
                '@type'      => 'WebSite',
                '@id'        => home_url('/#website'),
                'url'        => home_url('/'),
                'name'       => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'inLanguage' => get_bloginfo('language'),
                'potentialAction' => [
                    '@type'       => 'SearchAction',
                    'target'      => home_url('/?s={search_term_string}'),
                    'query-input' => 'required name=search_term_string',
                ],
            ];
        }

        $data = [
            '@context' => 'https://schema.org',
            '@graph'   => $graph,
        ];

        echo '<script type="application/ld+json">'
            . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . "</script>\n";
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function breadcrumbSchema(): array
    {
        $items  = [];
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => get_bloginfo('name'),
            'item'     => home_url('/'),
        ];

        $position = 2;
        $term     = PostMeta::primaryCategory();

        if ($term instanceof \WP_Term) {
            foreach (PostMeta::categoryPath($term) as $crumb) {
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => $crumb->name,
                    'item'     => get_term_link($crumb),
                ];
            }
        }

        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        ];

        return $items;
    }

    private function description(): string
    {
        if (is_singular()) {
            $excerpt = PostMeta::excerpt(32);

            if ($excerpt !== '') {
                return $excerpt;
            }
        }

        if (is_category() || is_tag() || is_tax()) {
            $description = wp_strip_all_tags((string) term_description());

            if ($description !== '') {
                return wp_trim_words($description, 32, '…');
            }
        }

        $tagline = get_bloginfo('description');

        return is_string($tagline) ? $tagline : '';
    }

    private function image(): string
    {
        if (is_singular() && has_post_thumbnail()) {
            $url = get_the_post_thumbnail_url(null, 'mp-hero');

            if (is_string($url)) {
                return $url;
            }
        }

        $customLogo = get_theme_mod('custom_logo');

        if ($customLogo) {
            $url = wp_get_attachment_image_url((int) $customLogo, 'full');

            if (is_string($url)) {
                return $url;
            }
        }

        return '';
    }

    private function url(): string
    {
        if (is_singular()) {
            return (string) get_permalink();
        }

        if (is_category() || is_tag() || is_tax()) {
            $link = get_term_link((int) get_queried_object_id());

            return is_string($link) ? $link : home_url('/');
        }

        if (is_search()) {
            return get_search_link();
        }

        return home_url('/');
    }

    /**
     * @param array<string, string> $attributes
     */
    private function tag(string $name, array $attributes): void
    {
        $html = '<' . $name;

        foreach ($attributes as $key => $value) {
            if ($value === '') {
                continue;
            }

            $html .= ' ' . $key . '="' . esc_attr($value) . '"';
        }

        echo $html . " />\n";
    }

    private function hasSeoPlugin(): bool
    {
        return defined('WPSEO_VERSION')
            || defined('RANK_MATH_VERSION')
            || defined('SEOPRESS_VERSION')
            || defined('AIOSEO_VERSION');
    }
}

<?php

/**
 * Importa el contenido real de Microplagio al WordPress de pruebas.
 *
 * Uso:
 *   wp eval-file tools/import.php
 *
 * Los datos de origen están en tools/.cache/*.json.
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit(1);
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

wp_set_current_user(1);

$cacheDir   = __DIR__ . '/.cache';
$posts      = json_decode((string) file_get_contents($cacheDir . '/posts.json'), true);
$categories = json_decode((string) file_get_contents($cacheDir . '/categories.json'), true);
$comments   = json_decode((string) file_get_contents($cacheDir . '/comments-1965.json'), true);

if (! is_array($posts) || ! is_array($categories) || ! is_array($comments)) {
    WP_CLI::error('No se pudieron leer los JSON de origen.');
}

/**
 * Log de importación.
 */
function mp_import_log(string $message): void
{
    if (class_exists('WP_CLI')) {
        WP_CLI::log($message);
        return;
    }

    echo $message . PHP_EOL;
}

/**
 * Normaliza la respuesta de term_exists().
 */
function mp_term_id(mixed $result): int
{
    if (is_array($result)) {
        return (int) ($result['term_id'] ?? 0);
    }

    return (int) $result;
}

/**
 * Convierte los <pre> de SHCB/Crayon en bloques core/code.
 */
function mp_convert_code_blocks(string $content): string
{
    $result = preg_replace_callback(
        '#<pre\b([^>]*)>(.*?)</pre>#is',
        static function (array $matches): string {
            $attributes = $matches[1];
            $inner      = $matches[2];
            $language   = '';

            if (preg_match('/data-shcb-language-slug=["\']([^"\']+)["\']/i', $attributes, $languageMatch)) {
                $language = $languageMatch[1];
            } elseif (preg_match('/(?:lang|language)[:\-]([a-z0-9_+#.-]+)/i', $attributes, $languageMatch)) {
                $language = $languageMatch[1];
            }

            if (preg_match('#<code\b[^>]*>(.*?)</code>#is', $inner, $codeMatch)) {
                $inner = $codeMatch[1];
            }

            $inner = preg_replace(
                '#<small\b[^>]*class=["\'][^"\']*shcb-language[^"\']*["\'][^>]*>.*?</small>#is',
                '',
                $inner
            ) ?? $inner;

            $code = html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $code = str_replace(["\r\n", "\r"], "\n", $code);
            $code = rtrim($code);

            if (trim($code) === '') {
                return $matches[0];
            }

            $language = strtolower(trim($language));
            $language = $language === 'shell' ? 'bash' : $language;

            $attrs = $language !== '' ? ' ' . wp_json_encode(['language' => $language]) : '';
            $code  = htmlspecialchars($code, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');

            return "<!-- wp:code{$attrs} -->\n"
                . '<pre class="wp-block-code"><code>' . $code . "</code></pre>\n"
                . '<!-- /wp:code -->';
        },
        $content
    );

    return is_string($result) ? $result : $content;
}

/**
 * Importa una imagen remota y la devuelve como attachment ID.
 *
 * @param array<string, string> $cache
 */
function mp_import_image(string $url, int $postId, string $suffix, array &$cache): int
{
    if (isset($cache[$url])) {
        return $cache[$url];
    }

    $existing = get_posts([
        'post_type'        => 'attachment',
        'post_status'      => 'any',
        'posts_per_page'   => 1,
        'fields'           => 'ids',
        'no_found_rows'    => true,
        'suppress_filters' => true,
        'meta_key'         => '_mp_source_url',
        'meta_value'       => $url,
    ]);

    if ($existing !== []) {
        $cache[$url] = (int) $existing[0];

        return $cache[$url];
    }

    $tmp = download_url($url, 120);

    if (is_wp_error($tmp)) {
        mp_import_log('  ! imagen fallida: ' . $url . ' — ' . $tmp->get_error_message());

        return 0;
    }

    $path = (string) parse_url($url, PHP_URL_PATH);
    $ext  = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
    $ext  = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'jpg';

    $file = [
        'name'     => sprintf('mp-%d-%s.%s', $postId, $suffix, $ext),
        'tmp_name' => $tmp,
    ];

    $attachmentId = media_handle_sideload($file, 0, null, ['post_author' => 1]);

    if (is_wp_error($attachmentId)) {
        @unlink($tmp);
        mp_import_log('  ! attachment fallido: ' . $url . ' — ' . $attachmentId->get_error_message());

        return 0;
    }

    update_post_meta($attachmentId, '_mp_source_url', esc_url_raw($url));
    $cache[$url] = (int) $attachmentId;

    return $cache[$url];
}

// ── Autor ────────────────────────────────────────────────────────────────

$author = get_user_by('login', 'abel-botello');

$authorData = [
    'user_login'          => 'abel-botello',
    'user_email'          => 'abel@microplagio.local',
    'display_name'        => 'Abel Botello',
    'first_name'          => 'Abel',
    'last_name'           => 'Botello',
    'nickname'            => 'Abel Botello',
    'description'         => 'Escribo en Microplagio sobre las cosas que me quitan el sueño: código, ciencia, cine y algún que otro experimento fallido.',
    'role'                => 'author',
    'show_admin_bar_front' => false,
];

if ($author instanceof WP_User) {
    $authorData['ID'] = $author->ID;
    $authorId         = (int) $author->ID;
} else {
    $authorData['user_pass'] = wp_generate_password(32, true, true);
    $authorId                = wp_insert_user($authorData);
}

if (is_wp_error($authorId)) {
    WP_CLI::error('No se pudo crear el autor: ' . $authorId->get_error_message());
}

$authorData['ID'] = $authorId;
unset($authorData['user_pass']);
wp_update_user($authorData);
mp_import_log('✓ Autor: Abel Botello (#' . $authorId . ')');

// ── Categorías ───────────────────────────────────────────────────────────

$categoryMap = [];

foreach ($categories as $category) {
    $existing = term_exists((string) $category['slug'], 'category');
    $termId   = mp_term_id($existing);

    if ($termId === 0) {
        $inserted = wp_insert_term((string) $category['name'], 'category', [
            'slug'        => (string) $category['slug'],
            'description' => (string) ($category['description'] ?? ''),
        ]);

        if (is_wp_error($inserted)) {
            mp_import_log('  ! categoría fallida: ' . $category['name'] . ' — ' . $inserted->get_error_message());
            continue;
        }

        $termId = (int) $inserted['term_id'];
    }

    $categoryMap[(int) $category['id']] = $termId;
}

foreach ($categories as $category) {
    $parent = (int) $category['parent'];

    if ($parent <= 0 || ! isset($categoryMap[$parent], $categoryMap[(int) $category['id']])) {
        continue;
    }

    wp_update_term($categoryMap[(int) $category['id']], 'category', [
        'parent' => $categoryMap[$parent],
    ]);
}

mp_import_log('✓ Categorías: ' . count($categoryMap));

// ── Etiquetas ────────────────────────────────────────────────────────────

$tagMap = [];

foreach ($posts as $post) {
    foreach ($post['_embedded']['wp:term'] ?? [] as $group) {
        foreach ($group as $term) {
            if (($term['taxonomy'] ?? '') !== 'post_tag') {
                continue;
            }

            $oldId = (int) $term['id'];

            if (isset($tagMap[$oldId])) {
                continue;
            }

            $existing = term_exists((string) $term['slug'], 'post_tag');
            $termId   = mp_term_id($existing);

            if ($termId === 0) {
                $inserted = wp_insert_term((string) $term['name'], 'post_tag', [
                    'slug' => (string) $term['slug'],
                ]);

                if (is_wp_error($inserted)) {
                    continue;
                }

                $termId = (int) $inserted['term_id'];
            }

            $tagMap[$oldId] = $termId;
        }
    }
}

mp_import_log('✓ Etiquetas: ' . count($tagMap));

// ── Artículos ────────────────────────────────────────────────────────────

$imageCache = [];
$imported   = 0;

foreach ($posts as $post) {
    $oldId   = (int) $post['id'];
    $content = (string) ($post['content']['rendered'] ?? '');
    $content = mp_convert_code_blocks($content);

    $inlineUrls = [];
    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $imageMatches);
    $inlineUrls = array_values(array_unique($imageMatches[1] ?? []));

    $inlineMap  = [];
    $attachmentIds = [];

    foreach ($inlineUrls as $index => $url) {
        $attachmentId = mp_import_image($url, $oldId, 'inline-' . ($index + 1), $imageCache);

        if ($attachmentId > 0) {
            $inlineMap[$url] = (string) wp_get_attachment_url($attachmentId);
            $attachmentIds[] = $attachmentId;
        }
    }

    foreach ($inlineMap as $remote => $local) {
        $content = str_replace($remote, $local, $content);
    }

    foreach ($inlineUrls as $url) {
        if (isset($inlineMap[$url])) {
            continue;
        }

        $content = preg_replace(
            '#<img\b[^>]*src=["\']' . preg_quote($url, '#') . '["\'][^>]*>#i',
            '',
            $content
        ) ?? $content;
    }

    $content = preg_replace('/\s(?:srcset|sizes)=["\'][^"\']*["\']/i', '', $content) ?? $content;
    $content = preg_replace('/(\sclass=["\'][^"\']*?)\bwp-image-\d+\s*/i', '$1', $content) ?? $content;
    $content = str_replace(
        ['https://blog.galaxyweb.dev', 'http://blog.galaxyweb.dev', '//blog.galaxyweb.dev'],
        untrailingslashit(home_url()),
        $content
    );

    $postCategories = [];

    foreach ($post['_embedded']['wp:term'] ?? [] as $group) {
        foreach ($group as $term) {
            if (($term['taxonomy'] ?? '') !== 'category') {
                continue;
            }

            $newId = $categoryMap[(int) $term['id']] ?? 0;

            if ($newId > 0) {
                $postCategories[] = $newId;
            }
        }
    }

    $postTags = [];

    foreach ($post['_embedded']['wp:term'] ?? [] as $group) {
        foreach ($group as $term) {
            if (($term['taxonomy'] ?? '') !== 'post_tag') {
                continue;
            }

            $newId = $tagMap[(int) $term['id']] ?? 0;

            if ($newId > 0) {
                $postTags[] = $newId;
            }
        }
    }

    $args = [
        'post_title'        => html_entity_decode(strip_tags((string) $post['title']['rendered']), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'post_name'         => (string) $post['slug'],
        'post_content'      => $content,
        'post_status'       => 'publish',
        'post_type'         => 'post',
        'post_author'       => $authorId,
        'post_date'         => (string) $post['date'],
        'post_date_gmt'     => (string) $post['date_gmt'],
        'post_modified'     => (string) ($post['modified'] ?? $post['date']),
        'post_modified_gmt' => (string) ($post['modified_gmt'] ?? $post['date_gmt']),
        'comment_status'    => (string) ($post['comment_status'] ?? 'open'),
        'ping_status'       => (string) ($post['ping_status'] ?? 'closed'),
        'post_category'     => $postCategories,
        'tags_input'        => $postTags,
    ];

    if (get_post($oldId) instanceof WP_Post) {
        $args['ID'] = $oldId;
    } else {
        $args['import_id'] = $oldId;
    }

    $postId = wp_insert_post(wp_slash($args), true);

    if (is_wp_error($postId)) {
        mp_import_log('  ! artículo fallido: ' . $oldId . ' — ' . $postId->get_error_message());
        continue;
    }

    $postId = (int) $postId;

    foreach ($attachmentIds as $attachmentId) {
        wp_update_post([
            'ID'          => $attachmentId,
            'post_parent' => $postId,
        ]);
    }

    $featuredId = 0;
    $featured   = $post['_embedded']['wp:featuredmedia'][0] ?? null;

    if (is_array($featured) && ! empty($featured['source_url'])) {
        $featuredId = mp_import_image((string) $featured['source_url'], $oldId, 'featured', $imageCache);

        if ($featuredId > 0) {
            set_post_thumbnail($postId, $featuredId);

            $alt = trim(strip_tags((string) ($featured['alt_text'] ?? '')));

            if ($alt !== '') {
                update_post_meta($featuredId, '_wp_attachment_image_alt', $alt);
            }

            wp_update_post([
                'ID'          => $featuredId,
                'post_parent' => $postId,
            ]);
        }
    }

    $imported++;
    mp_import_log(sprintf(
        '  · %d → %d  %s  (%d imágenes, featured %d)',
        $oldId,
        $postId,
        mb_substr((string) $args['post_title'], 0, 60),
        count($attachmentIds),
        $featuredId
    ));
}

mp_import_log('✓ Artículos importados: ' . $imported);

// ── Comentario ───────────────────────────────────────────────────────────

foreach ($comments as $comment) {
    $postId = (int) ($comment['post'] ?? 0);

    if ($postId <= 0 || ! get_post($postId) instanceof WP_Post) {
        continue;
    }

    $content = trim(strip_tags(html_entity_decode((string) ($comment['content']['rendered'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

    $existing = get_comments([
        'post_id' => $postId,
        'author'  => (string) ($comment['author_name'] ?? ''),
        'search'  => $content,
        'number'  => 1,
        'status'  => 'approve',
    ]);

    if ($existing !== []) {
        continue;
    }

    $dateGmt = (string) ($comment['date_gmt'] ?? $comment['date'] ?? '');
    $date    = $dateGmt !== '' ? get_date_from_gmt($dateGmt) : (string) ($comment['date'] ?? '');

    $commentId = wp_insert_comment([
        'comment_post_ID'      => $postId,
        'comment_author'       => (string) ($comment['author_name'] ?? 'Anónimo'),
        'comment_author_email' => '',
        'comment_author_url'   => (string) ($comment['author_url'] ?? ''),
        'comment_content'      => $content,
        'comment_date'         => $date,
        'comment_date_gmt'     => $dateGmt,
        'comment_approved'     => 1,
        'comment_type'         => 'comment',
        'user_id'              => 0,
    ]);

    if ($commentId) {
        mp_import_log('✓ Comentario importado en el artículo ' . $postId);
    }
}

// ── Página de archivo ────────────────────────────────────────────────────

$archivePage = get_page_by_path('archivo');

if (! $archivePage instanceof WP_Post) {
    $archivePageId = wp_insert_post(wp_slash([
        'post_title'   => 'Archivo',
        'post_name'    => 'archivo',
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_author'  => $authorId,
        'post_content' => '<!-- wp:latest-posts {"postsToShow":100,"displayPostDate":true} /-->',
    ]));
} else {
    $archivePageId = (int) $archivePage->ID;
}

mp_import_log('✓ Página de archivo: #' . (int) $archivePageId);

// ── Menú principal ───────────────────────────────────────────────────────

$menuName = 'Principal';
$menu     = wp_get_nav_menu_object($menuName);
$menuId   = $menu instanceof WP_Term ? (int) $menu->term_id : (int) wp_create_nav_menu($menuName);

if ($menuId > 0) {
    foreach (wp_get_nav_menu_items($menuId) ?: [] as $item) {
        wp_delete_post((int) $item->ID, true);
    }

    $items = [
        ['title' => 'inicio', 'url' => home_url('/')],
    ];

    foreach (['humor', 'cine', 'programacion', 'ciencia'] as $slug) {
        $term = get_term_by('slug', $slug, 'category');

        if ($term instanceof WP_Term) {
            $items[] = ['title' => mb_strtolower($term->name), 'url' => (string) get_category_link($term)];
        }
    }

    if ($archivePageId > 0) {
        $items[] = ['title' => 'archivo', 'url' => (string) get_permalink($archivePageId)];
    }

    foreach ($items as $item) {
        wp_update_nav_menu_item($menuId, 0, [
            'menu-item-title'  => $item['title'],
            'menu-item-url'    => $item['url'],
            'menu-item-status' => 'publish',
        ]);
    }

    $locations            = get_theme_mod('nav_menu_locations', []);
    $locations['primary'] = $menuId;
    set_theme_mod('nav_menu_locations', $locations);

    mp_import_log('✓ Menú principal: ' . count($items) . ' elementos');
}

// ── Limpieza y ajustes finales ───────────────────────────────────────────

foreach ([1, 2, 3] as $defaultId) {
    $default = get_post($defaultId);

    if ($default instanceof WP_Post && ! in_array($default->post_type, ['post', 'page'], true)) {
        continue;
    }

    if ($default instanceof WP_Post && $default->post_name === 'hello-world') {
        wp_delete_post($defaultId, true);
    }

    if ($default instanceof WP_Post && $default->post_name === 'sample-page') {
        wp_delete_post($defaultId, true);
    }
}

update_option('posts_per_page', 9);
update_option('show_on_front', 'posts');
flush_rewrite_rules(false);

mp_import_log('✓ Listo.');

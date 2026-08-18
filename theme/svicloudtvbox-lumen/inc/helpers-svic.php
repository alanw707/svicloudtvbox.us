<?php
/**
 * Theme-scoped SVICLOUD helper functions.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('svic_current_locale')) {
    function svic_current_locale(): string {
        $locale = apply_filters('svic_current_locale', get_locale());
        if (!is_string($locale) || $locale === '') {
            $locale = 'zh_TW';
        }

        return SVIC_Translator::normalizeLocaleCode($locale);
    }
}

if (!function_exists('svic_localize_brand_in_text')) {
    function svic_localize_brand_in_text(string $text, ?string $locale = null): string {
        $text = (string) $text;
        if ($text === '') {
            return $text;
        }

        if ($locale === null) {
            $locale = svic_current_locale();
        }

        if (!is_string($locale) || stripos($locale, 'zh') === false) {
            return $text;
        }

        $search = [
            'SVICLOUD 10P+',
            'SVICLOUD 10P Plus',
            'SVICLOUD TV BOX',
            'SVICLOUD TV Box',
            'SVICLOUD TV',
            'SVICLOUD',
        ];

        $replace = [
            '小雲電視盒 10P+',
            '小雲電視盒 10P Plus',
            '小雲電視盒',
            '小雲電視盒',
            '小雲電視盒',
            '小雲電視盒',
        ];

        // If there is no markup, perform a simple text replacement.
        if (strpos($text, '<') === false) {
            return str_ireplace($search, $replace, $text);
        }

        // For HTML content, only localise visible text and safe attributes,
        // never URLs such as img/src or anchor hrefs.
        if (!class_exists('DOMDocument')) {
            return str_ireplace($search, $replace, $text);
        }

        $document = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        // Wrap in a body element so we can reliably extract the inner markup.
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' . $text . '</body></html>';
        $loaded = $document->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        if ($loaded === false) {
            libxml_clear_errors();
            libxml_use_internal_errors($internalErrors);
            return str_ireplace($search, $replace, $text);
        }

        $xpath = new DOMXPath($document);

        // Replace in text nodes.
        foreach ($xpath->query('//text()') as $node) {
            if (!($node instanceof DOMText)) {
                continue;
            }
            $node->nodeValue = str_ireplace($search, $replace, $node->nodeValue);
        }

        // Replace in selected attributes that are never expected to hold URLs.
        $attribute_names = ['alt', 'title', 'aria-label'];
        foreach ($attribute_names as $attr_name) {
            foreach ($xpath->query('//*[@' . $attr_name . ']') as $element) {
                if (!($element instanceof DOMElement)) {
                    continue;
                }

                $value = $element->getAttribute($attr_name);
                if ($value === '') {
                    continue;
                }

                // Skip attributes that look like URLs just in case.
                if (preg_match('#https?://|/wp-content/#i', $value)) {
                    continue;
                }

                $element->setAttribute($attr_name, str_ireplace($search, $replace, $value));
            }
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        $body = $document->getElementsByTagName('body')->item(0);
        if (!$body instanceof DOMElement) {
            return str_ireplace($search, $replace, $text);
        }

        $output = '';
        foreach ($body->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return $output !== '' ? $output : str_ireplace($search, $replace, $text);
    }
}

if (!function_exists('svic_language_query_value')) {
    function svic_language_query_value(?string $locale = null): string {
        $normalized = $locale ? SVIC_Translator::normalizeLocaleCode($locale) : svic_current_locale();
        $normalized = strtolower($normalized);

        if ($normalized === '') {
            return 'zh';
        }

        // Preserve zh-cn vs zh-tw so we can map to distinct path prefixes.
        if (strpos($normalized, 'zh_cn') === 0 || strpos($normalized, 'zh-cn') === 0) {
            return 'zh-cn';
        }

        $parts = preg_split('/[-_]/', $normalized);
        $language = is_array($parts) && isset($parts[0]) ? trim((string) $parts[0]) : $normalized;
        if ($language === '') {
            $language = $normalized;
        }

        $language = strtolower($language);

        return $language === '' ? 'zh' : $language;
    }
}

if (!function_exists('svic_url_with_lang')) {
    function svic_url_with_lang($url, ?string $lang = null): string {
        if (!is_string($url) || $url === '') {
            return '';
        }

        $trimmed = trim($url);
        if ($trimmed === '') {
            return '';
        }

        if ($trimmed[0] === '#') {
            return $url;
        }

        if (preg_match('/^(?:mailto|tel|javascript|data):/i', $trimmed)) {
            return $url;
        }

        $langValue = $lang !== null ? strtolower(trim($lang)) : svic_language_query_value();
        if ($langValue === '') {
            return remove_query_arg('lang', $url);
        }

        $homeParts = wp_parse_url(home_url('/'));
        $homeHost  = isset($homeParts['host']) ? strtolower((string) $homeParts['host']) : '';
        $homePath  = isset($homeParts['path']) ? rtrim((string) $homeParts['path'], '/') : '';

        $urlParts = wp_parse_url($url);
        if ($urlParts === false) {
            return $url;
        }

        $isRelative = !isset($urlParts['host']);
        $isInternal = $isRelative;
        if (!$isInternal) {
            $targetHost = strtolower((string) ($urlParts['host'] ?? ''));
            $isInternal = ($homeHost !== '') && $targetHost === $homeHost;

            if (!$isInternal) {
                return remove_query_arg('lang', $url);
            }
        }

        $path = (string) ($urlParts['path'] ?? '');
        if ($path === '' && $isRelative) {
            $path = '/';
        }

        if ($path === '' || $path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }

        if (!$isRelative && $homePath !== '' && $homePath !== '/') {
            if ($path === $homePath) {
                $path = '/';
            } elseif (strpos($path, $homePath . '/') === 0) {
                $path = substr($path, strlen($homePath));
                if ($path === false || $path === '') {
                    $path = '/';
                } elseif ($path[0] !== '/') {
                    $path = '/' . $path;
                }
            }
        }

        if ($path === '') {
            $path = '/';
        }

        $path = svic_normalize_route_path($path);
        $path = svic_route_alias_path_for_locale($path, $langValue);

        $hadTrailingSlash = $path !== '/' && substr($path, -1) === '/';
        $trimmedPath = ltrim($path, '/');

        if ($langValue === 'zh-cn') {
            if ($trimmedPath === '' || $trimmedPath === 'zh-cn') {
                $trimmedPath = 'zh-cn';
                $hadTrailingSlash = true;
            } elseif (strpos($trimmedPath, 'zh-cn/') !== 0) {
                $trimmedPath = 'zh-cn/' . $trimmedPath;
            }
        } elseif ($langValue === 'zh') {
            if ($trimmedPath === '' || $trimmedPath === 'zh') {
                $trimmedPath = 'zh';
                $hadTrailingSlash = true;
            } elseif (strpos($trimmedPath, 'zh/') !== 0) {
                $trimmedPath = 'zh/' . $trimmedPath;
            }
        } else {
            if ($trimmedPath === 'zh' || $trimmedPath === 'zh-cn') {
                $trimmedPath = '';
                $hadTrailingSlash = true;
            } elseif (strpos($trimmedPath, 'zh/') === 0) {
                $trimmedPath = substr($trimmedPath, 3);
            }
            if (strpos($trimmedPath, 'zh-cn/') === 0) {
                $trimmedPath = substr($trimmedPath, 6);
            }
        }

        if ($trimmedPath === '') {
            if ($langValue === 'zh-cn') {
                $localizedPath = '/zh-cn/';
            } elseif ($langValue === 'zh') {
                $localizedPath = '/zh/';
            } else {
                $localizedPath = '/';
            }
        } else {
            $localizedPath = '/' . $trimmedPath;
            if ($hadTrailingSlash && substr($localizedPath, -1) !== '/') {
                $localizedPath .= '/';
            } elseif (!$hadTrailingSlash && substr($localizedPath, -1) === '/' && $localizedPath !== '/') {
                $localizedPath = rtrim($localizedPath, '/');
            }
        }

        $localizedPath = preg_replace('#/+#', '/', $localizedPath);
        $queryParams = [];
        if (isset($urlParts['query']) && $urlParts['query'] !== '') {
            parse_str($urlParts['query'], $queryParams);
            unset($queryParams['lang']);
        }

        $fragment = $urlParts['fragment'] ?? '';

        $localizedUrl = home_url($localizedPath);
        if (!empty($queryParams)) {
            $localizedUrl = add_query_arg($queryParams, $localizedUrl);
        }

        if ($fragment !== '') {
            $localizedUrl .= '#' . $fragment;
        }

        return $localizedUrl;
    }
}

if (!function_exists('svic_normalize_route_path')) {
    function svic_normalize_route_path(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '/';
        }

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $path = preg_replace('#/+#', '/', $path);
        $path = preg_replace_callback('/%[0-9a-fA-F]{2}/', static function ($match) {
            return strtolower($match[0]);
        }, $path);
        if ($path !== '/' && substr($path, -1) !== '/') {
            $path .= '/';
        }

        return $path;
    }
}

if (!function_exists('svic_strip_route_locale_prefix')) {
    function svic_strip_route_locale_prefix(string $path): string
    {
        $normalized = svic_normalize_route_path($path);

        if (strpos($normalized, '/zh-cn/') === 0) {
            $normalized = substr($normalized, 6);
            if ($normalized === false || $normalized === '') {
                $normalized = '/';
            } elseif ($normalized[0] !== '/') {
                $normalized = '/' . $normalized;
            }
            $normalized = svic_normalize_route_path($normalized);
        }

        if (strpos($normalized, '/zh/') === 0) {
            $normalized = substr($normalized, 3);
            if ($normalized === false || $normalized === '') {
                $normalized = '/';
            } elseif ($normalized[0] !== '/') {
                $normalized = '/' . $normalized;
            }
            $normalized = svic_normalize_route_path($normalized);
        }

        return $normalized;
    }
}

if (!function_exists('svic_route_alias_definitions')) {
    function svic_route_alias_definitions(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $aliases = [
            'blog' => [
                'canonical_base' => [
                    'en' => '/blog/',
                    'zh' => '/blog/',
                ],
                'legacy' => [],
            ],
            'faq' => [
                'canonical_base' => [
                    'en' => '/faq/',
                    'zh' => '/faq/',
                ],
                'legacy' => [
                    // Legacy internal link target used by early blog automation drafts.
                    '/blog/svicloud-faq/',
                ],
            ],
            'compare' => [
                'canonical_base' => [
                    'en' => '/compare/',
                    'zh' => '/compare/',
                ],
                // Older Chinese slug /%e6%a9%9f%e5%9e%8b%e6%af%94%e8%bc%83/ should canonicalize to /compare/
                'legacy' => [
                    '/%e6%a9%9f%e5%9e%8b%e6%af%94%e8%bc%83/',
                    '/機型比較/',
                ],
            ],
            'guides' => [
                'canonical_base' => [
                    'en' => '/guides/',
                    'zh' => '/%e4%bd%bf%e7%94%a8%e6%8c%87%e5%8d%97/',
                ],
                'legacy' => [],
            ],
            'svicloud-10p-vs-evpad-10-pro' => [
                'canonical_base' => [
                    'en' => '/svicloud-10p%2bvs-evpad-10-pro-best-family-streaming-box-in-2025/',
                    'zh' => '/svicloud-10p%2bvs-evpad-10-pro-best-family-streaming-box-in-2025/',
                ],
                'legacy' => [
                    // Incorrect shorter slug referenced by some internal links.
                    '/svicloud-10p-plus-vs-evpad-10-pro/',
                ],
            ],
        ];

        foreach ($aliases as $key => $alias) {
            $alias['canonical_base'] = array_map('svic_normalize_route_path', $alias['canonical_base']);
            $alias['legacy']         = array_map('svic_normalize_route_path', $alias['legacy']);
            $aliases[$key]           = $alias;
        }

        return $cache = $aliases;
    }
}

if (!function_exists('svic_route_alias_by_base')) {
    function svic_route_alias_by_base(string $path): ?array
    {
        $base = svic_strip_route_locale_prefix($path);
        foreach (svic_route_alias_definitions() as $alias) {
            if (in_array($base, $alias['canonical_base'], true) || in_array($base, $alias['legacy'], true)) {
                return $alias;
            }
        }

        return null;
    }
}

if (!function_exists('svic_alias_base_for_locale')) {
    function svic_alias_base_for_locale(array $alias, string $langKey): ?string
    {
        $langKey = ($langKey === 'zh' || $langKey === 'zh-cn') ? 'zh' : 'en';
        if (!empty($alias['canonical_base'][$langKey])) {
            return $alias['canonical_base'][$langKey];
        }

        return $alias['canonical_base']['en'] ?? null;
    }
}

if (!function_exists('svic_alias_localized_path')) {
    function svic_alias_localized_path(array $alias, string $langKey): ?string
    {
        $base = svic_alias_base_for_locale($alias, $langKey);
        if (!$base) {
            return null;
        }

        if ($langKey === 'zh-cn') {
            return svic_normalize_route_path('/zh-cn' . $base);
        }

        if ($langKey === 'zh') {
            return svic_normalize_route_path('/zh' . $base);
        }

        return $base;
    }
}

if (!function_exists('svic_route_alias_path_for_locale')) {
    function svic_route_alias_path_for_locale(string $path, string $langKey): string
    {
        $alias = svic_route_alias_by_base($path);
        if (!$alias) {
            return svic_normalize_route_path($path);
        }

        $target = svic_alias_base_for_locale($alias, $langKey);
        if (!$target) {
            return svic_normalize_route_path($path);
        }

        return $target;
    }
}

if (!function_exists('svic_alias_resolve_existing_page_id')) {
    function svic_alias_resolve_existing_page_id(array $alias): ?int
    {
        $candidates = array_merge($alias['canonical_base'], $alias['legacy']);

        foreach ($candidates as $base) {
            $slug = trim($base, '/');
            if ($slug === '') {
                continue;
            }

            $page = get_page_by_path($slug);
            if ($page instanceof WP_Post) {
                return (int) $page->ID;
            }
        }

        return null;
    }
}

if (!function_exists('svic_register_route_alias_rewrites')) {
    function svic_register_route_alias_rewrites(): void
    {
        foreach (svic_route_alias_definitions() as $alias) {
            $englishBase = svic_alias_base_for_locale($alias, 'en');
            if (!$englishBase) {
                continue;
            }

            $englishSlug = trim($englishBase, '/');
            if ($englishSlug === '') {
                continue;
            }

            $englishPage = get_page_by_path($englishSlug);
            if ($englishPage instanceof WP_Post) {
                continue;
            }

            $targetPageId = svic_alias_resolve_existing_page_id($alias);
            if (!$targetPageId) {
                continue;
            }

            $pattern = '^' . preg_quote($englishSlug, '#') . '/?$';
            add_rewrite_rule($pattern, 'index.php?page_id=' . $targetPageId, 'top');
        }
    }
}
add_action('init', 'svic_register_route_alias_rewrites', 20);

if (!function_exists('svic_enforce_route_alias_canonical')) {
    function svic_enforce_route_alias_canonical(): void
    {
        if (is_admin() || is_feed() || wp_doing_ajax()) {
            return;
        }

        $originalPath = null;
        if (class_exists('SVIC_Locale_Resolver') && method_exists('SVIC_Locale_Resolver', 'originalRequestPath')) {
            $originalPath = SVIC_Locale_Resolver::originalRequestPath();
        }

        if (!is_string($originalPath) || $originalPath === '') {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            $parsed     = is_string($requestUri) ? wp_parse_url($requestUri, PHP_URL_PATH) : '';
            $originalPath = is_string($parsed) && $parsed !== '' ? $parsed : '/';
        }

        $normalizedOriginal = svic_normalize_route_path($originalPath);

        $alias              = svic_route_alias_by_base($normalizedOriginal);
        if (!$alias) {
            return;
        }

        $requestLang = 'en';
        if ($normalizedOriginal === '/zh-cn/' || strpos($normalizedOriginal, '/zh-cn/') === 0) {
            $requestLang = 'zh-cn';
        } elseif ($normalizedOriginal === '/zh/' || strpos($normalizedOriginal, '/zh/') === 0) {
            $requestLang = 'zh';
        }
        $canonicalPath = svic_alias_localized_path($alias, $requestLang);
        if (!$canonicalPath || $canonicalPath === $normalizedOriginal) {
            return;
        }

        $target = home_url($canonicalPath);
        $query  = isset($_SERVER['QUERY_STRING']) ? (string) $_SERVER['QUERY_STRING'] : '';
        if ($query !== '') {
            $target .= (strpos($target, '?') === false ? '?' : '&') . $query;
        }

        wp_safe_redirect($target, 301);
        exit;
    }
}
add_action('template_redirect', 'svic_enforce_route_alias_canonical', 0);

if (!function_exists('svic_current_base_url')) {
    function svic_current_base_url(): string {
        $path = SVIC_Locale_Resolver::currentRequestPath();
        $url  = home_url($path);

        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (is_string($requestUri) && $requestUri !== '') {
            $parsed = wp_parse_url($requestUri);
            if (is_array($parsed)) {
                if (isset($parsed['query']) && $parsed['query'] !== '') {
                    $query = [];
                    parse_str($parsed['query'], $query);
                    if (!empty($query)) {
                        unset($query['lang']);
                        if (!empty($query)) {
                            $url = add_query_arg($query, $url);
                        }
                    }
                }

                if (isset($parsed['fragment']) && $parsed['fragment'] !== '') {
                    $url .= '#' . $parsed['fragment'];
                }
            }
        }

        return $url;
    }
}

if (!function_exists('svic_theme_image_relative')) {
    function svic_theme_image_relative(string $relative_path): string
    {
        $relative_path = '/' . ltrim($relative_path, '/');
        $webp_candidate = preg_replace('/\\.(png|jpe?g)$/i', '.webp', $relative_path);

        if ($webp_candidate && $webp_candidate !== $relative_path) {
            $webp_path = get_template_directory() . $webp_candidate;
            if (file_exists($webp_path)) {
                return $webp_candidate;
            }
        }

        return $relative_path;
    }
}

if (!function_exists('svic_theme_image_uri')) {
    function svic_theme_image_uri(string $relative_path): string
    {
        $relative = svic_theme_image_relative($relative_path);
        return get_template_directory_uri() . $relative;
    }
}

if (!function_exists('svic_locale_to_hreflang')) {
    function svic_locale_to_hreflang(string $locale): string {
        $locale = trim($locale);
        if ($locale === '') {
            return '';
        }

        $segments = preg_split('/[_-]/', $locale);
        if (!is_array($segments) || !$segments) {
            return strtolower($locale);
        }

        $language = strtolower($segments[0]);
        $variants = array_slice($segments, 1);

        if ($language === 'zh') {
            $variantsUpper = array_map(static function ($value) {
                return strtoupper((string) $value);
            }, $variants);

            // Default to Traditional Chinese, but distinguish Simplified Chinese (zh-CN).
            // - zh_TW -> zh-Hant-US
            // - zh_CN -> zh-Hans-US
            if (in_array('CN', $variantsUpper, true) || in_array('HANS', $variantsUpper, true) || in_array('SG', $variantsUpper, true)) {
                return 'zh-Hans-US';
            }

            return 'zh-Hant-US';
        }

        if (!$variants) {
            return $language;
        }

        $variants = array_map(static function ($value) {
            return strtoupper((string) $value);
        }, $variants);

        return $language . '-' . implode('-', $variants);
    }
}

if (!function_exists('svic_translate')) {
    function svic_translate(string $key, array $replacements = [], ?string $locale = null): string {
        return SVIC_Translator::instance()->translate($key, $replacements, $locale);
    }
}

if (!function_exists('svic_translate_array')) {
    /**
     * Resolve a translation key that points at an array (e.g. a list of items).
     * Falls back to en_US if the current locale is missing the key.
     * Returns [] if the key doesn't exist or doesn't resolve to an array.
     */
    function svic_translate_array(string $key, ?string $locale = null): array {
        $translator = SVIC_Translator::instance();
        $locale     = $locale ?: get_locale();
        $registry   = $translator->registry($locale);

        $walk = function (array $registry, string $key) {
            if (array_key_exists($key, $registry)) {
                return $registry[$key];
            }
            $cursor = $registry;
            foreach (explode('.', $key) as $segment) {
                if (!is_array($cursor) || !array_key_exists($segment, $cursor)) {
                    return null;
                }
                $cursor = $cursor[$segment];
            }
            return $cursor;
        };

        $value = $walk($registry, $key);
        if (!is_array($value)) {
            // Fallback to en_US for missing keys.
            $fallback = $translator->registry('en_US');
            $value    = $walk($fallback, $key);
        }

        return is_array($value) ? $value : [];
    }
}

if (!function_exists('svic_translate_html')) {
    function svic_translate_html(string $key, array $replacements = [], ?string $locale = null): string {
        return esc_html(svic_translate($key, $replacements, $locale));
    }
}

if (!function_exists('svic_translate_attr')) {
    function svic_translate_attr(string $key, array $replacements = [], ?string $locale = null): string {
        return esc_attr(svic_translate($key, $replacements, $locale));
    }
}

if (!function_exists('svic_translate_rich')) {
    function svic_translate_rich(string $key, array $replacements = [], ?string $locale = null): string {
        $text = svic_translate($key, $replacements, $locale);
        /**
         * Allow filters to adjust rich text output (e.g., additional sanitization).
         */
        return apply_filters('svic_translate_rich_text', $text, $key, $locale);
    }
}

if (!function_exists('svic_estimated_read_time')) {
    function svic_estimated_read_time(int $post_id, int $words_per_minute = 220): int {
        $content = get_post_field('post_content', $post_id);
        if (!is_string($content) || $content === '') {
            return 1;
        }

        $sanitized   = wp_strip_all_tags($content, true);
        // str_word_count() ignores CJK; add a CJK character count so Chinese/Japanese/Korean posts aren't treated as 1-word articles.
        $latin_words = str_word_count($sanitized);
        $cjk_chars   = preg_match_all('/[\x{4e00}-\x{9fff}\x{3040}-\x{30ff}\x{ac00}-\x{d7af}]/u', $sanitized, $matches) ?: 0;
        $word_count  = $latin_words + (int) $cjk_chars;
        $wpm_sanitized = max(1, $words_per_minute);

        $minutes = (int) ceil($word_count / $wpm_sanitized);

        return $minutes > 0 ? $minutes : 1;
    }
}

if (!function_exists('svic_post_card_image')) {
    function svic_post_card_image(int $post_id, string $size = 'large', array $attr = []): string {
        if ($post_id <= 0) {
            return '';
        }

        $attr = is_array($attr) ? $attr : [];

        $html = get_the_post_thumbnail($post_id, $size, $attr);
        if (is_string($html) && $html !== '') {
            return $html;
        }

        $content = get_post_field('post_content', $post_id);
        if (is_string($content) && $content !== '') {
            if (preg_match('/<img[^>]+src=["\"]([^"\"]+)["\"][^>]*>/i', $content, $image_match)) {
                $img_html = $image_match[0];
                $src      = $image_match[1] ?? '';
                if ($src !== '') {
                    $alt = '';
                    if (preg_match('/alt=["\"](.*?)["\"]/i', $img_html, $alt_match)) {
                        $alt = $alt_match[1] ?? '';
                    }

                    $attributes = svic_prepare_image_attributes($attr, [
                        'src'     => esc_url($src),
                        'alt'     => $alt !== '' ? $alt : get_the_title($post_id),
                        'loading' => 'lazy',
                        'decoding'=> 'async',
                        'sizes'   => '(max-width: 600px) 92vw, 360px',
                    ]);

                    return '<img ' . $attributes . ' />';
                }
            }
        }

        $fallback_relative = apply_filters('svic_blog_fallback_image', '/assets/images/svicloud-hero-product.webp', $post_id, $size, $attr);
        $meta = function_exists('svic_get_theme_image_meta') ? svic_get_theme_image_meta($fallback_relative) : [
            'url' => get_template_directory_uri() . '/' . ltrim((string) $fallback_relative, '/'),
            'width' => null,
            'height' => null,
        ];

        if (empty($meta['url'])) {
            return '';
        }

        $fallback_alt = svic_translate('blog.card.fallback_alt', ['title' => get_the_title($post_id)]);
        if (!is_string($fallback_alt) || trim($fallback_alt) === '' || trim($fallback_alt) === 'fallback_alt') {
            $fallback_alt = get_the_title($post_id);
        }

        $attr_defaults = [
            'src'     => esc_url($meta['url']),
            'alt'     => $fallback_alt,
            'loading' => 'lazy',
            'decoding'=> 'async',
            'sizes'   => '(max-width: 600px) 92vw, 360px',
        ];

        if (!empty($meta['width'])) {
            $attr_defaults['width'] = (string) (int) $meta['width'];
        }
        if (!empty($meta['height'])) {
            $attr_defaults['height'] = (string) (int) $meta['height'];
        }

        $attributes = svic_prepare_image_attributes($attr, $attr_defaults);

        return '<img ' . $attributes . ' />';
    }
}

if (!function_exists('svic_prepare_image_attributes')) {
    function svic_prepare_image_attributes(array $attr, array $overrides = []): string {
        $final = array_merge(
            [
                'loading'  => 'lazy',
                'decoding' => 'async',
            ],
            $attr,
            $overrides
        );

        if (isset($attr['class']) && isset($overrides['class'])) {
            $final['class'] = trim($attr['class'] . ' ' . $overrides['class']);
        }

        $compiled = [];
        foreach ($final as $key => $value) {
            if ($value === null) {
                continue;
            }
            if ($key === 'class') {
                $value = trim((string) $value);
                if ($value === '') {
                    continue;
                }
            }
            $compiled[] = sprintf('%s="%s"', esc_attr($key), esc_attr((string) $value));
        }

        return implode(' ', $compiled);
    }
}

if (!function_exists('svic_category_label')) {
    function svic_category_label($term): string {
        if (!($term instanceof WP_Term)) {
            return '';
        }

        $label = trim((string) $term->name);
        $slug  = sanitize_title($term->slug ?: $label);

        if ($slug !== '') {
            $translation = trim(svic_translate('blog.categories.' . $slug));

            if ($translation !== '' && strcasecmp($translation, $slug) !== 0) {
                $label = $translation;
            } elseif ($label === '' || strcasecmp($label, $slug) === 0) {
                $label = ucwords(str_replace(['-', '_'], ' ', $slug));
            }
        }

        return $label !== '' ? $label : $term->name;
    }
}

if (!function_exists('svic_post_locale_meta')) {
    function svic_post_locale_meta(int $post_id, string $field): string {
        $post_id = (int) $post_id;
        if ($post_id <= 0) {
            return '';
        }

        $locale = function_exists('svic_current_locale') ? svic_current_locale() : get_locale();
        $locale = is_string($locale) ? strtolower($locale) : '';

        $meta_keys = [];
        if (strpos($locale, 'zh_cn') !== false || strpos($locale, 'zh-cn') !== false) {
            $meta_keys[] = '_svic_' . $field . '_zh_cn';
            $meta_keys[] = '_svic_' . $field . '_zh_tw';
        } elseif (strpos($locale, 'zh') !== false) {
            $meta_keys[] = '_svic_' . $field . '_zh_tw';
        } else {
            $meta_keys[] = '_svic_' . $field . '_en_us';
        }

        foreach ($meta_keys as $key) {
            $value = get_post_meta($post_id, $key, true);
            if (is_string($value)) {
                $trimmed = trim($value);
                if ($trimmed !== '') {
                    return $trimmed;
                }
            }
        }

        return '';
    }
}

if (!function_exists('svic_post_localized_content')) {
    function svic_post_localized_content(int $post_id): string {
        $localized = function_exists('svic_post_locale_meta') ? svic_post_locale_meta($post_id, 'content') : '';
        if ($localized === '') {
            return '';
        }

        return apply_filters('the_content', $localized);
    }
}

if (!function_exists('svic_post_title')) {
    function svic_post_title(int $post_id): string {
        $post = get_post($post_id);
        if (!$post instanceof WP_Post) {
            return '';
        }

        $locale = function_exists('svic_current_locale') ? svic_current_locale() : get_locale();

        $title = get_the_title($post_id);

        $meta_title = function_exists('svic_post_locale_meta') ? svic_post_locale_meta($post_id, 'title') : '';
        if ($meta_title !== '') {
            if (function_exists('svic_localize_brand_in_text')) {
                $meta_title = svic_localize_brand_in_text($meta_title, $locale);
            }

            return $meta_title;
        }

        $slug_candidates = [];
        $current_slug = sanitize_title($post->post_name ?: $post->post_title ?: '');
        if ($current_slug !== '') {
            $slug_candidates[] = $current_slug;
        }

        if (function_exists('pll_default_language') && function_exists('pll_get_post')) {
            $default_lang = pll_default_language('slug');
            if (is_string($default_lang) && $default_lang !== '') {
                $default_post_id = pll_get_post($post_id, $default_lang);
                if ($default_post_id && (int) $default_post_id !== $post_id) {
                    $default_post = get_post($default_post_id);
                    if ($default_post instanceof WP_Post) {
                        $default_slug = sanitize_title($default_post->post_name ?: $default_post->post_title ?: '');
                        if ($default_slug !== '') {
                            $slug_candidates[] = $default_slug;
                        }
                    }
                }
            }
        }

        $slug_candidates = array_values(array_unique(array_filter($slug_candidates)));

        foreach ($slug_candidates as $slug) {
            $translation_key = 'blog.posts.' . $slug . '.title';
            $translation = svic_translate($translation_key);
            if (!is_string($translation)) {
                continue;
            }
            $translation = trim($translation);
            if ($translation === '' || strcasecmp($translation, 'title') === 0 || $translation === $translation_key) {
                continue;
            }

            $title = $translation;
            break;
        }

        if (is_string($locale) && stripos($locale, 'zh') !== false) {
            if (strpos($title, '—') !== false) {
                $parts = preg_split('/\s*—\s*/u', $title);
                if (is_array($parts) && $parts) {
                    $candidate = trim((string) end($parts));
                    if ($candidate !== '') {
                        $title = $candidate;
                    }
                }
            }

            $title = svic_localize_brand_in_text($title, $locale);
        }

        return $title;
    }
}

if (!function_exists('svic_icon_translation_key')) {
    function svic_icon_translation_key(string $icon_filename): string {
        $filename = trim($icon_filename);
        if ($filename === '') {
            return 'icons.generic';
        }

        $basename = pathinfo($filename, PATHINFO_FILENAME);
        if ($basename === '') {
            $basename = $filename;
        }

        if (stripos($basename, 'icon-') === 0) {
            $basename = substr($basename, 5);
        }

        $slug = strtolower(str_replace('-', '_', $basename));
        if ($slug === '') {
            $slug = 'generic';
        }

        return 'icons.' . $slug;
    }
}

if (!function_exists('svic_icon_label')) {
    function svic_icon_label(string $icon_filename): string {
        $key = svic_icon_translation_key($icon_filename);
        $label = svic_translate($key);

        if (!is_string($label) || $label === '' || $label === $key) {
            $label = ucwords(str_replace('_', ' ', str_replace('icons.', '', $key)));
        }

        return $label;
    }
}

if (!function_exists('svic_text_domain')) {
    function svic_text_domain(): string {
        if (defined('SVIC_THEME_TEXT_DOMAIN')) {
            return constant('SVIC_THEME_TEXT_DOMAIN');
        }

        return 'svicloudtvbox-lumen';
    }
}

if (!function_exists('svic_get_product_by_slug')) {
    function svic_get_product_by_slug(string $slug) {
        if (!class_exists('WooCommerce')) {
            return null;
        }

        $post = get_page_by_path($slug, OBJECT, 'product');
        if (!$post instanceof WP_Post) {
            return null;
        }

        $product = wc_get_product($post->ID);
        return $product ?: null;
    }
}

if (!function_exists('svic_price_html')) {
    function svic_price_html($product): string {
        if (!$product) {
            return '';
        }
        $regular_price = $product->get_regular_price();
        $current_price = $product->get_price();
        $sale_price    = $product->get_sale_price();

        if ($current_price === '' && $regular_price !== '') {
            $current_price = $regular_price;
        }

        $regular_html = $regular_price !== '' ? wc_price($regular_price) : '';
        $current_html = $current_price !== '' ? wc_price($current_price) : '';

        if ($product->is_on_sale() && $sale_price !== '' && $regular_html && $current_html && $current_html !== $regular_html) {
            $sr_template = svic_translate('frontpage.pricing.sr_sale_announcement');
            if (!is_string($sr_template) || $sr_template === '') {
                $sr_template = __('Sale price %2$s, original price %1$s', svic_text_domain());
            }

            $sr_text = sprintf(
                $sr_template,
                wp_strip_all_tags($regular_html),
                wp_strip_all_tags($current_html)
            );

            return sprintf(
                '<span class="lumen-price lumen-price--sale"><span class="lumen-price__current">%1$s</span><span class="lumen-price__original">%2$s</span><span class="screen-reader-text">%3$s</span></span>',
                $current_html,
                $regular_html,
                esc_html($sr_text)
            );
        }

        if ($current_html) {
            return sprintf('<span class="lumen-price"><span class="lumen-price__current">%s</span></span>', $current_html);
        }

        return '';
    }
}

if (!function_exists('svic_product_schema_availability')) {
    function svic_product_schema_availability($product): string {
        if (!$product instanceof WC_Product) {
            return 'https://schema.org/OutOfStock';
        }

        if ($product->is_on_backorder(1)) {
            return 'https://schema.org/BackOrder';
        }

        return $product->is_in_stock()
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';
    }
}

if (!function_exists('svic_get_product_image_meta')) {
    function svic_get_product_image_meta($product, int $index = 0, string $size = 'full'): array {
        $fallback = function_exists('svic_get_theme_image_meta')
            ? svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp')
            : [
                'url' => esc_url_raw(svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')),
                'width' => null,
                'height' => null,
            ];

        if (!($product instanceof WC_Product)) {
            return $fallback;
        }

        $attachment_ids = [];
        $primary_id = (int) $product->get_image_id();
        if ($primary_id > 0) {
            $attachment_ids[] = $primary_id;
        }

        if (method_exists($product, 'get_gallery_image_ids')) {
            foreach ((array) $product->get_gallery_image_ids() as $gallery_id) {
                $gallery_id = (int) $gallery_id;
                if ($gallery_id > 0 && !in_array($gallery_id, $attachment_ids, true)) {
                    $attachment_ids[] = $gallery_id;
                }
            }
        }

        if (!$attachment_ids) {
            return $fallback;
        }

        $selected_id = $attachment_ids[$index] ?? $attachment_ids[0];
        $image_src = wp_get_attachment_image_src($selected_id, $size);
        if (!is_array($image_src) || empty($image_src[0])) {
            return $fallback;
        }

        return [
            'url' => esc_url_raw($image_src[0]),
            'width' => isset($image_src[1]) ? (int) $image_src[1] : null,
            'height' => isset($image_src[2]) ? (int) $image_src[2] : null,
        ];
    }
}

if (!function_exists('svic_product_primary_image')) {
    function svic_product_primary_image($product, string $size = 'medium'): string {
        if (!$product) {
            return '';
        }

        $image_id = $product->get_image_id();
        if (!$image_id && method_exists($product, 'get_gallery_image_ids')) {
            $gallery_ids = $product->get_gallery_image_ids();
            if (is_array($gallery_ids) && !empty($gallery_ids)) {
                $image_id = (int) reset($gallery_ids);
            }
        }

        if ($image_id) {
            return wp_get_attachment_image(
                $image_id,
                $size,
                false,
                [
                    'alt' => esc_attr($product->get_name()),
                    'loading' => 'lazy',
                    'decoding' => 'async',
                ]
            );
        }

        return '<img src="' . esc_url(svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')) . '" alt="' . esc_attr($product->get_name()) . '" loading="lazy" decoding="async" />';
    }
}

if (!function_exists('svic_add_to_cart_url')) {
    function svic_add_to_cart_url($product): string {
        if (!$product) {
            return '#';
        }

        return esc_url(svic_url_with_lang(add_query_arg('add-to-cart', $product->get_id(), wc_get_cart_url())));
    }
}

if (!function_exists('svic_cart_contents_count')) {
    function svic_cart_contents_count(): int
    {
        if (!class_exists('WooCommerce') || !function_exists('WC')) {
            return 0;
        }

        $cart = WC()->cart;
        if (!is_object($cart) || !method_exists($cart, 'get_cart_contents_count')) {
            return 0;
        }

        $count = (int) $cart->get_cart_contents_count();

        return $count > 0 ? $count : 0;
    }
}

if (!function_exists('svic_header_cart_count_markup')) {
    function svic_header_cart_count_markup(): string
    {
        $count = svic_cart_contents_count();
        $classes = ['lumen-cart-count'];

        if ($count === 0) {
            $classes[] = 'is-empty';
        }

        return sprintf(
            '<span class="%1$s" data-cart-count data-count="%2$s">%3$s</span>',
            esc_attr(implode(' ', $classes)),
            esc_attr((string) $count),
            esc_html(number_format_i18n($count))
        );
    }
}

if (!function_exists('svic_header_cart_link')) {
    function svic_header_cart_link(array $args = []): string
    {
        $defaults = [
            'class' => '',
            /* translators: Header cart CTA label. Non-breaking space keeps text on one line. */
            'label' => wp_kses_post(__('View&nbsp;Cart', svic_text_domain())),
        ];
        $args = wp_parse_args($args, $defaults);

        $count   = svic_cart_contents_count();
        $base_classes = [
            'lumen-pill',
            'lumen-pill--primary',
            'lumen-cart-link',
        ];

        $extra_classes = trim((string) $args['class']);
        if ($extra_classes !== '') {
            $base_classes[] = $extra_classes;
        }

        $classes = trim(implode(' ', $base_classes));

        if ($count > 0) {
            $classes .= ' has-items';
        }

        $cart_url = svic_url_with_lang(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart'));

        $count_display = number_format_i18n($count);
        if ($count === 1) {
            $sr_label = svic_translate('core.cart.count_label_single', ['count' => $count_display]);
        } elseif ($count > 1) {
            $sr_label = svic_translate('core.cart.count_label_plural', ['count' => $count_display]);
        } else {
            $sr_label = svic_translate('core.cart.count_label_empty');
        }

        $link_aria_label = wp_strip_all_tags($args['label']);
        $html  = '<a class="' . esc_attr($classes) . '" href="' . esc_url($cart_url) . '" data-cart-link aria-label="' . esc_attr($link_aria_label) . '">';
        $html .= '<span class="lumen-cart-link__icon" aria-hidden="true"></span>';
        $html .= '<span class="lumen-cart-link__label">' . $args['label'] . '</span>';
        $html .= svic_header_cart_count_markup();
        $html .= '<span class="screen-reader-text">' . esc_html($sr_label) . '</span>';
        $html .= '</a>';

        return $html;
    }
}

if (!function_exists('svic_bilingual_span')) {
    function svic_bilingual_span(string $en, string $zh, string $extra_class = ''): string {
        $extra_class = trim($extra_class);
        $suffix = $extra_class !== '' ? ' ' . esc_attr($extra_class) : '';
        $domain = svic_text_domain();

        $en_span = sprintf('<span class="hide-zh%s">%s</span>', $suffix, esc_html__($en, $domain));
        $zh_span = sprintf('<span class="hide-en%s" lang="zh">%s</span>', $suffix, esc_html__($zh, $domain));

        return $en_span . $zh_span;
    }
}

if (!function_exists('svic_render_product_card')) {
    function svic_render_product_card($product): void {
        if (!$product) {
            return;
        }

        $permalink   = svic_url_with_lang(get_permalink($product->get_id()));
        $gallery_ids = method_exists($product, 'get_gallery_image_ids') ? (array) $product->get_gallery_image_ids() : [];
        $slides      = [];
        $primary_id  = $product->get_image_id();

        if ($primary_id) {
            $slides[] = wp_get_attachment_image_url($primary_id, 'product-tile');
        }

        foreach ($gallery_ids as $gid) {
            $url = wp_get_attachment_image_url($gid, 'product-tile');
            if ($url) {
                $slides[] = $url;
            }
        }

        if (!$slides) {
            $slides[] = svic_theme_image_uri('/assets/images/svicloud-hero-product.webp');
        }

        $feature_tags = [];
        if (method_exists($product, 'get_slug')) {
            $slug = $product->get_slug();
            if ($slug === 'svicloud-10p-plus') {
                $feature_tags = ['4K HDR', 'Wi-Fi 6', 'Kids & Karaoke'];
            } elseif ($slug === 'svicloud-10s') {
                $feature_tags = ['4K HDR', 'Compact Footprint', 'Dual-Band Wi-Fi'];
            }
        }

        if (!$feature_tags) {
            $term_names = wp_get_post_terms($product->get_id(), 'product_tag', ['fields' => 'names']);
            if (!$term_names || is_wp_error($term_names)) {
                $term_names = wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'names']);
            }

            if ($term_names && !is_wp_error($term_names)) {
                $feature_tags = array_slice($term_names, 0, 3);
            }
        }
        ?>
        <article class="product-card">
          <div class="pcard-carousel">
            <?php foreach ($slides as $i => $src) : ?>
              <div class="pcard-slide<?php echo $i === 0 ? ' active' : ''; ?>">
                <a href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
                  <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($product->get_name()); ?> image <?php echo (int) ($i + 1); ?>" loading="lazy" />
                </a>
              </div>
            <?php endforeach; ?>
            <div class="pcard-nav">
              <button class="pcard-btn pcard-prev" aria-label="<?php esc_attr_e('Previous image', svic_text_domain()); ?>">‹</button>
              <button class="pcard-btn pcard-next" aria-label="<?php esc_attr_e('Next image', svic_text_domain()); ?>">›</button>
            </div>
            <div class="pcard-dots">
              <?php foreach ($slides as $i => $src) : ?>
                <button class="pcard-dot<?php echo $i === 0 ? ' active' : ''; ?>" data-i="<?php echo (int) $i; ?>" aria-label="<?php esc_attr_e('Go to image', svic_text_domain()); ?> <?php echo (int) ($i + 1); ?>"></button>
              <?php endforeach; ?>
            </div>
          </div>
          <h3 class="pcard-title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
          <div class="pcard-meta">
            <span class="pcard-price"><?php echo svic_price_html($product); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
          </div>
          <?php
            $summary = wp_strip_all_tags($product->get_short_description() ?: $product->get_description());
            if ($summary) {
                echo '<p class="product-blurb">' . esc_html(wp_trim_words($summary, 18)) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            $render_tags = array_filter(array_map('trim', (array) $feature_tags));
            if ($render_tags) {
                echo '<ul class="pcard-tags" role="list">';
                foreach ($render_tags as $tag) {
                    echo '<li>' . esc_html($tag) . '</li>';
                }
                echo '</ul>';
            }
          ?>
          <div class="pcard-actions">
            <a class="btn btn-primary btn-cta" href="<?php echo svic_add_to_cart_url($product); ?>"><?php esc_html_e('Add to Cart', svic_text_domain()); ?></a>
          </div>
        </article>
        <?php
    }
}

if (!function_exists('svic_get_google_customer_reviews_optin_payload')) {
    /**
     * Prepare the payload for Google Customer Reviews opt-in.
     *
     * @param \WC_Order|null $order
     *
     * @return array|null
     */
    function svic_get_google_customer_reviews_optin_payload($order): ?array
    {
        if (!($order instanceof \WC_Order)) {
            return null;
        }

        $enabled = apply_filters('svic_google_customer_reviews_enabled', true, $order);
        if (!$enabled) {
            return null;
        }

        $merchant_id = (int) apply_filters(
            'svic_google_customer_reviews_merchant_id',
            defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID') ? SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID : 0,
            $order
        );

        if ($merchant_id <= 0) {
            return null;
        }

        $order_number = $order->get_order_number();
        $order_id = preg_replace('/[^A-Za-z0-9\-]/', '', (string) $order_number);
        if ($order_id === '') {
            $order_id = (string) $order->get_id();
        }

        $email = sanitize_email((string) $order->get_billing_email());
        if ($email === '') {
            return null;
        }

        $country = $order->get_shipping_country();
        if (!$country) {
            $country = $order->get_billing_country();
        }
        $country = strtoupper(preg_replace('/[^A-Z]/', '', (string) $country));
        if ($country === '') {
            $country = 'US';
        }

        $created_timestamp = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : current_time('timestamp', true);
        $transit_days = (int) apply_filters('svic_google_customer_reviews_estimated_transit_days', 5, $order);
        if ($transit_days < 0) {
            $transit_days = 0;
        }
        $estimated_timestamp = $created_timestamp + ($transit_days * DAY_IN_SECONDS);
        $estimated_date = gmdate('Y-m-d', $estimated_timestamp);

        $meta_keys = (array) apply_filters('svic_google_customer_reviews_gtin_meta_keys', ['_wc_gcr_gtin', '_gtin', 'gtin', 'svic_gtin'], $order);
        $products = [];

        foreach ($order->get_items() as $item) {
            if (!($item instanceof \WC_Order_Item_Product)) {
                continue;
            }

            $product = $item->get_product();
            if (!($product instanceof \WC_Product)) {
                continue;
            }

            $gtin_value = '';
            foreach ($meta_keys as $meta_key) {
                if (!is_string($meta_key) || $meta_key === '') {
                    continue;
                }
                $raw = $product->get_meta($meta_key, true);
                if (is_string($raw) && $raw !== '') {
                    $gtin_value = preg_replace('/[^0-9A-Za-z]/', '', $raw);
                }

                if ($gtin_value !== '') {
                    break;
                }
            }

            if ($gtin_value === '') {
                continue;
            }

            $products[] = ['gtin' => $gtin_value];
        }

        $payload = [
            'merchant_id'             => $merchant_id,
            'order_id'                => $order_id,
            'email'                   => $email,
            'delivery_country'        => $country,
            'estimated_delivery_date' => $estimated_date,
        ];

        if ($products) {
            $payload['products'] = $products;
        }

        $payload = apply_filters('svic_google_customer_reviews_optin_payload', $payload, $order);

        if (!is_array($payload)) {
            return null;
        }

        foreach (['merchant_id', 'order_id', 'email', 'delivery_country', 'estimated_delivery_date'] as $required_key) {
            if (!isset($payload[$required_key]) || $payload[$required_key] === '') {
                return null;
            }
        }

        $payload['merchant_id'] = (int) $payload['merchant_id'];
        $payload['order_id'] = (string) $payload['order_id'];
        $payload['email'] = sanitize_email((string) $payload['email']);
        $payload['delivery_country'] = strtoupper(preg_replace('/[^A-Z]/', '', (string) $payload['delivery_country']));
        $payload['estimated_delivery_date'] = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $payload['estimated_delivery_date']) ? (string) $payload['estimated_delivery_date'] : $estimated_date;

        if ($payload['merchant_id'] <= 0 || $payload['email'] === '' || $payload['delivery_country'] === '') {
            return null;
        }

        if (isset($payload['products']) && is_array($payload['products'])) {
            $filtered_products = [];
            foreach ($payload['products'] as $product_entry) {
                if (!is_array($product_entry) || !isset($product_entry['gtin'])) {
                    continue;
                }
                $gtin_clean = preg_replace('/[^0-9A-Za-z]/', '', (string) $product_entry['gtin']);
                if ($gtin_clean === '') {
                    continue;
                }
                $filtered_products[] = ['gtin' => $gtin_clean];
            }

            if ($filtered_products) {
                $payload['products'] = $filtered_products;
            } else {
                unset($payload['products']);
            }
        }

        return $payload;
    }
}

if (!function_exists('svic_render_google_platform_loader')) {
    /**
     * Load Google's platform script once and queue callbacks until gapi is ready.
     *
     * @return void
     */
    function svic_render_google_platform_loader(): void
    {
        static $rendered = false;

        if ($rendered) {
            return;
        }

        $rendered = true;
        ?>
        <script>
          (function(w) {
            w.svicGooglePlatformCallbacks = w.svicGooglePlatformCallbacks || [];
            w.svicGooglePlatformReady = w.svicGooglePlatformReady || function() {
              w.svicGooglePlatformLoaded = true;
              var pending = w.svicGooglePlatformCallbacks.splice(0, w.svicGooglePlatformCallbacks.length);
              for (var i = 0; i < pending.length; i += 1) {
                try {
                  pending[i]();
                } catch (error) {
                  setTimeout(function() { throw error; }, 0);
                }
              }
            };
            w.svicRunGooglePlatformCallback = w.svicRunGooglePlatformCallback || function(callback) {
              if (typeof callback !== 'function') {
                return;
              }
              if (w.gapi && w.gapi.load) {
                callback();
                return;
              }
              w.svicGooglePlatformCallbacks.push(callback);
            };
          })(window);
        </script>
        <script src="https://apis.google.com/js/platform.js?onload=svicGooglePlatformReady" async defer></script>
        <?php
    }
}

if (!function_exists('svic_render_google_customer_reviews_optin')) {
    /**
     * Output the Google Customer Reviews opt-in script block.
     *
     * @param \WC_Order|null $order
     *
     * @return void
     */
    function svic_render_google_customer_reviews_optin($order): void
    {
        static $rendered = false;

        if ($rendered) {
            return;
        }

        $payload = svic_get_google_customer_reviews_optin_payload($order);
        if (!$payload) {
            return;
        }

        $json_payload = wp_json_encode($payload, JSON_UNESCAPED_SLASHES);
        if (!is_string($json_payload) || $json_payload === '') {
            return;
        }

        if ($order instanceof \WC_Order) {
            $order->update_meta_data('_svic_gcr_optin_rendered_at', gmdate('c'));
            $order->update_meta_data('_svic_gcr_optin_estimated_delivery_date', (string) $payload['estimated_delivery_date']);
            $order->save();
        }

        $rendered = true;
        svic_render_google_platform_loader();

        ?>
        <script>
          (function(w) {
            w.svicGoogleCustomerReviewsConfig = <?php echo $json_payload; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
            w.svicRunGooglePlatformCallback(function() {
              var config = w.svicGoogleCustomerReviewsConfig || null;
              if (!config || !w.gapi || !w.gapi.load) {
                return;
              }
              w.gapi.load('surveyoptin', function() {
                if (!w.gapi || !w.gapi.surveyoptin || !w.gapi.surveyoptin.render) {
                  return;
                }
                w.gapi.surveyoptin.render(config);
              });
            });
          })(window);
        </script>
        <?php
    }
}

if (!function_exists('svic_google_business_review_url')) {
    /**
     * Return the configured Google Business Profile write-review URL.
     *
     * @return string
     */
    function svic_google_business_review_url(): string
    {
        $url = defined('SVIC_GOOGLE_BUSINESS_REVIEW_URL') ? (string) SVIC_GOOGLE_BUSINESS_REVIEW_URL : '';
        $url = apply_filters('svic_google_business_review_url', trim($url));

        if (!is_string($url)) {
            return '';
        }

        $url = trim($url);
        if ($url === '') {
            return '';
        }

        $scheme = wp_parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return '';
        }

        return esc_url_raw($url);
    }
}

if (!function_exists('svic_render_google_customer_reviews_badge')) {
    /**
     * Output the official Google Customer Reviews rating badge.
     *
     * @return void
     */
    function svic_render_google_customer_reviews_badge(): void
    {
        static $rendered = false;

        if ($rendered || is_admin()) {
            return;
        }

        $enabled = defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_BADGE_ENABLED') && SVIC_GOOGLE_CUSTOMER_REVIEWS_BADGE_ENABLED;
        $enabled = (bool) apply_filters('svic_google_customer_reviews_badge_enabled', $enabled);
        if (!$enabled) {
            return;
        }

        $merchant_id = (int) apply_filters(
            'svic_google_customer_reviews_merchant_id',
            defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID') ? SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID : 0,
            null
        );

        if ($merchant_id <= 0) {
            return;
        }

        $position = (string) apply_filters('svic_google_customer_reviews_badge_position', 'BOTTOM_LEFT');
        $allowed_positions = ['BOTTOM_LEFT', 'BOTTOM_RIGHT', 'INLINE'];
        if (!in_array($position, $allowed_positions, true)) {
            $position = 'BOTTOM_LEFT';
        }

        $config = [
            'merchant_id' => $merchant_id,
            'position'    => $position,
        ];
        $json_config = wp_json_encode($config, JSON_UNESCAPED_SLASHES);
        if (!is_string($json_config) || $json_config === '') {
            return;
        }

        $rendered = true;
        svic_render_google_platform_loader();
        ?>
        <div id="svic-google-customer-reviews-badge" aria-hidden="true"></div>
        <script>
          (function(w, d) {
            var badgeConfig = <?php echo $json_config; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
            w.svicRunGooglePlatformCallback(function() {
              if (!w.gapi || !w.gapi.load) {
                return;
              }
              w.gapi.load('ratingbadge', function() {
                var container = d.getElementById('svic-google-customer-reviews-badge');
                if (!container || !w.gapi || !w.gapi.ratingbadge || !w.gapi.ratingbadge.render) {
                  return;
                }
                w.gapi.ratingbadge.render(container, badgeConfig);
              });
            });
          })(window, document);
        </script>
        <?php
    }
}

if (!function_exists('svic_render_google_customer_reviews_inline_badge')) {
    /**
     * Output an inline official Google Customer Reviews badge.
     *
     * @param string $element_id DOM id for the inline badge container.
     *
     * @return void
     */
    function svic_render_google_customer_reviews_inline_badge(string $element_id = 'svic-google-customer-reviews-inline-badge'): void
    {
        static $rendered_ids = [];

        $element_id = sanitize_key($element_id);
        if ($element_id === '' || isset($rendered_ids[$element_id])) {
            return;
        }

        $enabled = defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_BADGE_ENABLED') && SVIC_GOOGLE_CUSTOMER_REVIEWS_BADGE_ENABLED;
        $enabled = (bool) apply_filters('svic_google_customer_reviews_badge_enabled', $enabled);
        if (!$enabled) {
            return;
        }

        $merchant_id = (int) apply_filters(
            'svic_google_customer_reviews_merchant_id',
            defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID') ? SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID : 0,
            null
        );

        if ($merchant_id <= 0) {
            return;
        }

        $config = [
            'merchant_id' => $merchant_id,
            'position'    => 'INLINE',
        ];
        $json_config = wp_json_encode($config, JSON_UNESCAPED_SLASHES);
        if (!is_string($json_config) || $json_config === '') {
            return;
        }

        $rendered_ids[$element_id] = true;
        svic_render_google_platform_loader();
        ?>
        <div id="<?php echo esc_attr($element_id); ?>" class="svic-google-customer-reviews-inline-badge" aria-label="<?php echo esc_attr__('Google store rating badge', 'svicloudtvbox-lumen'); ?>"></div>
        <script>
          (function(w, d) {
            var badgeConfig = <?php echo $json_config; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
            var elementId = <?php echo wp_json_encode($element_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
            w.svicRunGooglePlatformCallback(function() {
              if (!w.gapi || !w.gapi.load) {
                return;
              }
              w.gapi.load('ratingbadge', function() {
                var container = d.getElementById(elementId);
                if (!container || !w.gapi || !w.gapi.ratingbadge || !w.gapi.ratingbadge.render) {
                  return;
                }
                w.gapi.ratingbadge.render(container, badgeConfig);
              });
            });
          })(window, document);
        </script>
        <?php
    }
}

if (!function_exists('svic_extract_intro_media_blocks')) {
    /**
     * Extract the first few media-heavy blocks so the template can restyle them.
     *
     * @param string      $content      Rendered post content.
     * @param string|null $featured_url URL of the featured image for duplicate detection.
     *
     * @return array{blocks: array<int, string>, content: string}
     */
    function svic_extract_intro_media_blocks(string $content, ?string $featured_url = null): array
    {
        $remaining           = ltrim($content);
        $blocks              = [];
        $consumed_intro_html = false;
        $normalized_featured = svic_normalize_media_src($featured_url);
        $seen_sources        = [];

        if ($remaining === '') {
            return [
                'blocks'  => [],
                'content' => '',
            ];
        }

        $pattern  = '/^\s*(<(?:figure\b[^>]*>.*?<\\/figure>|p\b[^>]*>.*?<\\/p>))/isu';
        $attempts = 0;

        while (count($blocks) < 3 && $attempts < 6) {
            $attempts++;

            if (!preg_match($pattern, $remaining, $match)) {
                break;
            }

            $matched_block = $match[1];
            if (strpos($matched_block, '<img') === false) {
                break;
            }

            $slice = substr($remaining, strlen($match[0]));
            $remaining = $slice === false ? '' : $slice;
            $consumed_intro_html = true;

            $image_src      = svic_extract_first_image_src($matched_block);
            $normalized_src = svic_normalize_media_src($image_src);

            if ($normalized_src !== '' && $normalized_featured !== '' && $normalized_src === $normalized_featured) {
                continue;
            }

            if ($normalized_src !== '' && isset($seen_sources[$normalized_src])) {
                continue;
            }

            $blocks[] = $matched_block;

            if ($normalized_src !== '') {
                $seen_sources[$normalized_src] = true;
            }
        }

        $body = ltrim($remaining);
        if (!$consumed_intro_html) {
            $body = $content;
        }

        return [
            'blocks'  => $blocks,
            'content' => $body,
        ];
    }
}

if (!function_exists('svic_extract_first_image_src')) {
    function svic_extract_first_image_src(string $html): string
    {
        if ($html === '') {
            return '';
        }

        if (!preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $match)) {
            return '';
        }

        return trim((string) $match[1]);
    }
}

if (!function_exists('svic_normalize_media_src')) {
    function svic_normalize_media_src(?string $url): string
    {
        if (!is_string($url) || $url === '') {
            return '';
        }

        $trimmed = trim($url);
        if ($trimmed === '') {
            return '';
        }

        $parts = wp_parse_url($trimmed);
        if ($parts === false) {
            return '';
        }

        $host = isset($parts['host']) ? strtolower((string) $parts['host']) : '';
        $path = isset($parts['path']) ? strtolower((string) $parts['path']) : '';

        if ($path !== '') {
            $path = preg_replace('/-\d+x\d+(?=\.[a-z0-9]+$)/i', '', $path);
            $path = preg_replace('#/+#', '/', $path);
        }

        return ($host !== '' ? $host : '') . $path;
    }
}

if (!function_exists('svic_generate_default_image_alt')) {
    function svic_generate_default_image_alt($attachment, ?string $fallback = null): string
    {
        $candidates = [];

        if ($attachment instanceof WP_Post) {
            $candidates[] = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
            $candidates[] = $attachment->post_excerpt ?? '';
            $candidates[] = $attachment->post_title ?? '';

            if (!empty($attachment->post_parent)) {
                $parent_title = get_the_title((int) $attachment->post_parent);
                if (is_string($parent_title) && $parent_title !== '') {
                    $candidates[] = sprintf(
                        /* translators: %s is the parent post title. */
                        esc_html__('Illustration for %s', 'svicloudtvbox-lumen'),
                        wp_strip_all_tags($parent_title)
                    );
                }
            }
        }

        if (is_string($fallback) && $fallback !== '') {
            $candidates[] = $fallback;
        }

        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }
            $trimmed = trim(wp_strip_all_tags($candidate));
            if ($trimmed !== '') {
                return $trimmed;
            }
        }

        return esc_html__('SVICLOUD TV Box illustration', 'svicloudtvbox-lumen');
    }
}

if (!function_exists('svic_support_chat_number')) {
    function svic_support_chat_number(): string
    {
        if (!defined('SVIC_SUPPORT_CHAT_WHATSAPP_NUMBER')) {
            return '';
        }

        $digits = preg_replace('/\D+/', '', (string) SVIC_SUPPORT_CHAT_WHATSAPP_NUMBER);

        return is_string($digits) ? $digits : '';
    }
}

if (!function_exists('svic_support_chat_is_enabled')) {
    function svic_support_chat_is_enabled(): bool
    {
        if (!defined('SVIC_SUPPORT_CHAT_ENABLED') || !SVIC_SUPPORT_CHAT_ENABLED) {
            return false;
        }

        return svic_support_chat_number() !== '';
    }
}

if (!function_exists('svic_support_chat_context')) {
    function svic_support_chat_context(): string
    {
        if (function_exists('is_front_page') && is_front_page()) {
            return 'home';
        }

        if ((function_exists('is_page_template') && is_page_template('page-compare.php')) || (function_exists('is_page') && is_page('compare'))) {
            return 'compare';
        }

        if (function_exists('is_product') && is_product()) {
            return 'product';
        }

        if (function_exists('is_cart') && is_cart()) {
            return 'cart';
        }

        return '';
    }
}

if (!function_exists('svic_support_chat_should_render')) {
    function svic_support_chat_should_render(): bool
    {
        if (is_admin() || !svic_support_chat_is_enabled()) {
            return false;
        }

        if (function_exists('is_checkout') && is_checkout()) {
            return false;
        }

        if (function_exists('is_account_page') && is_account_page()) {
            return false;
        }

        return svic_support_chat_context() !== '';
    }
}

if (!function_exists('svic_support_chat_product_name')) {
    function svic_support_chat_product_name(): string
    {
        $product_name = '';
        $product_id   = function_exists('get_queried_object_id') ? (int) get_queried_object_id() : 0;

        if ($product_id > 0) {
            $product_name = get_the_title($product_id);
        }

        if (!is_string($product_name)) {
            $product_name = '';
        }

        $product_name = trim(wp_strip_all_tags($product_name));

        if ($product_name === '') {
            return '';
        }

        return svic_localize_brand_in_text($product_name);
    }
}

if (!function_exists('svic_support_chat_message')) {
    function svic_support_chat_message(?string $context = null): string
    {
        $context = $context ?: svic_support_chat_context();

        if ($context === 'product') {
            $product_name = svic_support_chat_product_name();
            if ($product_name !== '') {
                return svic_translate('support_chat.messages.product', [
                    'product' => $product_name,
                ]);
            }

            return svic_translate('support_chat.messages.product_generic');
        }

        $supported_contexts = ['home', 'compare', 'cart'];
        if (in_array($context, $supported_contexts, true)) {
            return svic_translate('support_chat.messages.' . $context);
        }

        return svic_translate('support_chat.messages.default');
    }
}

if (!function_exists('svic_support_chat_url')) {
    function svic_support_chat_url(?string $context = null): string
    {
        $number = svic_support_chat_number();
        if ($number === '') {
            return '';
        }

        $url     = 'https://wa.me/' . $number;
        $message = trim(svic_support_chat_message($context));

        if ($message !== '') {
            $url .= '?text=' . rawurlencode($message);
        }

        return $url;
    }
}

if (!function_exists('svic_get_support_chat_payload')) {
    function svic_get_support_chat_payload(): array
    {
        if (!svic_support_chat_should_render()) {
            return [];
        }

        $context = svic_support_chat_context();
        $url     = svic_support_chat_url($context);

        if ($context === '' || $url === '') {
            return [];
        }

        return [
            'context'    => $context,
            'url'        => $url,
            'channel'    => svic_translate('support_chat.channel'),
            'label'      => svic_translate('support_chat.label'),
            'title'      => svic_translate('support_chat.title'),
            'aria_label' => svic_translate('support_chat.aria_label'),
        ];
    }
}

if (!function_exists('svic_render_support_chat')) {
    function svic_render_support_chat(): void
    {
        $payload = svic_get_support_chat_payload();
        if ($payload === []) {
            return;
        }
        ?>
        <a
            class="svic-support-chat"
            href="<?php echo esc_url($payload['url']); ?>"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="<?php echo esc_attr($payload['aria_label']); ?>"
            title="<?php echo esc_attr($payload['title']); ?>"
            data-svic-event="svic_support_chat_click"
            data-svic-location="floating_support_chat"
            data-svic-label="<?php echo esc_attr($payload['context']); ?>"
            data-svic-model="whatsapp"
        >
            <span class="svic-support-chat__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                    <path d="M12 3.5C7.30558 3.5 3.5 7.03902 3.5 11.4054C3.5 13.4053 4.30174 15.2337 5.6268 16.6164L5 20.5L8.99635 19.2215C9.94103 19.554 10.9506 19.7311 12 19.7311C16.6944 19.7311 20.5 16.1921 20.5 11.8257C20.5 7.45929 16.6944 3.5 12 3.5Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                    <path d="M9.30354 8.55273C9.49759 8.17882 9.69409 8 9.94845 8C10.0641 8 10.1797 8.00389 10.2918 8.00973C10.4737 8.01945 10.5868 8.05058 10.7119 8.3501C10.8615 8.70605 11.2141 9.54572 11.2566 9.63091C11.2991 9.71609 11.3271 9.81587 11.2701 9.91467C11.2131 10.0135 11.1841 10.0709 11.1002 10.1706C11.0162 10.2704 10.9243 10.3936 10.8443 10.4837C10.7604 10.5786 10.6734 10.6813 10.7683 10.8369C10.8632 10.9926 11.1916 11.5107 11.6621 11.9301C12.2673 12.4705 12.7785 12.6388 12.9458 12.7087C13.1131 12.7787 13.2109 12.767 13.3077 12.6721C13.4044 12.5773 13.7202 12.2402 13.8342 12.0807C13.9482 11.9213 14.0622 11.9485 14.2014 12.0086C14.3407 12.0688 15.0864 12.4237 15.2421 12.5015C15.3977 12.5793 15.5005 12.6172 15.5355 12.6721C15.5704 12.727 15.5704 12.9833 15.4727 13.2522C15.3749 13.5211 14.9006 13.7647 14.6896 13.8386C14.4787 13.9125 14.2126 13.9446 13.3233 13.576C12.2402 13.1261 11.5376 12.5482 10.9171 11.8922C10.4711 11.4208 10.0758 10.8069 9.95232 10.5806C9.82889 10.3544 9.37608 9.62605 9.37608 8.87245C9.37608 8.74806 9.34708 8.65802 9.30354 8.55273Z" fill="currentColor"/>
                </svg>
            </span>
            <span class="svic-support-chat__text">
                <span class="svic-support-chat__channel"><?php echo esc_html($payload['channel']); ?></span>
                <span class="svic-support-chat__label"><?php echo esc_html($payload['label']); ?></span>
            </span>
        </a>
        <?php
    }
}

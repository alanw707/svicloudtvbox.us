<?php
/**
 * Locale resolver for SVICLOUD theme.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class SVIC_Locale_Resolver
{
    public const COOKIE_NAME = 'svic_lang';
    private const COOKIE_TTL = 30 * DAY_IN_SECONDS;
    private const DEFAULT_LOCALE = 'zh_TW';
    private const PRIMARY_LOCALE = 'en_US';
    private static ?string $pathLocale = null;
    private static string $sanitizedRequestPath = '/';
    private static ?string $originalRequestPath = null;
    private static bool $pathHandled = false;

    public static function bootstrap(): void
    {
        add_action('init', [self::class, 'handle_language_path'], 0);
        add_filter('determine_locale', [self::class, 'filter_determine_locale'], 20);
        add_filter('locale', [self::class, 'filter_locale'], 20);
        add_action('set_current_user', [self::class, 'maybe_persist_cookie']);
        add_action('init', [self::class, 'maybe_persist_cookie']);
    }

    public static function filter_determine_locale(string $locale): string
    {
        $resolver = self::resolve_locale();
        if ($resolver) {
            return $resolver;
        }

        return $locale;
    }

    public static function filter_locale(string $locale): string
    {
        $resolved = self::resolve_locale();
        if ($resolved) {
            return $resolved;
        }

        return $locale;
    }

    public static function resolve_locale(): ?string
    {
        if (!self::$pathHandled) {
            self::handle_language_path();
        }

        $requested = self::locale_from_query();
        if ($requested) {
            return SVIC_Translator::normalizeLocaleCode($requested);
        }

        if (self::$pathLocale) {
            return SVIC_Translator::normalizeLocaleCode(self::$pathLocale);
        }

        $cookieLocale = self::locale_from_cookie();
        if ($cookieLocale) {
            return $cookieLocale;
        }

        $browserLocale = self::locale_from_browser();
        if ($browserLocale) {
            return $browserLocale;
        }

        return SVIC_Translator::normalizeLocaleCode(self::DEFAULT_LOCALE);
    }

    public static function maybe_persist_cookie(): void
    {
        if (headers_sent()) {
            return;
        }

        $queryLocale = self::locale_from_query();
        if ($queryLocale) {
            self::set_cookie($queryLocale);
            return;
        }

        if (self::$pathLocale) {
            self::set_cookie(self::$pathLocale);
        }
    }

    public static function currentRequestPath(): string
    {
        return self::$sanitizedRequestPath ?: '/';
    }

    public static function originalRequestPath(): string
    {
        return self::$originalRequestPath ?: self::currentRequestPath();
    }

    public static function pathLocale(): ?string
    {
        return self::$pathLocale;
    }

    public static function handle_language_path(): void
    {
        if (self::$pathHandled) {
            return;
        }

        self::$pathHandled = true;
        self::$pathLocale = null;

        if (is_admin()) {
            return;
        }

        if (defined('WP_CLI') && WP_CLI) {
            return;
        }

        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (!is_string($requestUri) || $requestUri === '') {
            self::$sanitizedRequestPath = '/';
            self::$originalRequestPath = '/';
            return;
        }

        $parsed = wp_parse_url($requestUri);
        if (!is_array($parsed)) {
            self::$sanitizedRequestPath = '/';
            self::$originalRequestPath = '/';
            return;
        }

        $path = $parsed['path'] ?? '/';
        if (!is_string($path) || $path === '') {
            $path = '/';
        }

        $homePath = wp_parse_url(home_url('/'), PHP_URL_PATH) ?? '/';
        $homePath = rtrim($homePath, '/');

        $relativePath = $path;
        if ($homePath !== '' && $homePath !== '/') {
            if ($relativePath === $homePath) {
                $relativePath = '/';
            } elseif (strpos($relativePath, $homePath . '/') === 0) {
                $relativePath = substr($relativePath, strlen($homePath));
                if ($relativePath === false || $relativePath === '') {
                    $relativePath = '/';
                } elseif ($relativePath[0] !== '/') {
                    $relativePath = '/' . $relativePath;
                }
            }
        }

        if ($relativePath === '') {
            $relativePath = '/';
        }

        $relativePath = self::normalizePath($relativePath);
        self::$originalRequestPath = $relativePath;
        self::$sanitizedRequestPath = $relativePath;

        $restrictedPrefixes = ['/wp-admin', '/wp-login.php', '/wp-json', '/xmlrpc.php'];
        foreach ($restrictedPrefixes as $prefix) {
            if (strpos($relativePath, $prefix) === 0) {
                return;
            }
        }

        $isLanguageRoute = false;
        $sanitized = $relativePath;

        if ($relativePath === '/zh' || $relativePath === '/zh/') {
            $isLanguageRoute = true;
            $sanitized = '/';
        } elseif (strpos($relativePath, '/zh/') === 0) {
            $isLanguageRoute = true;
            $sanitized = substr($relativePath, 3);
            if ($sanitized === false || $sanitized === '') {
                $sanitized = '/';
            } elseif ($sanitized[0] !== '/') {
                $sanitized = '/' . $sanitized;
            }
        }

        if (!$isLanguageRoute) {
            self::$sanitizedRequestPath = $relativePath;
            self::$pathLocale = SVIC_Translator::normalizeLocaleCode(self::PRIMARY_LOCALE);
            return;
        }

        $sanitized = self::normalizePath($sanitized);
        self::$sanitizedRequestPath = $sanitized;
        self::$pathLocale = 'zh_TW';

        $updatedPath = $sanitized;
        if ($homePath !== '' && $homePath !== '/') {
            $updatedPath = rtrim($homePath, '/') . ($sanitized === '/' ? '/' : $sanitized);
        }

        $query = isset($parsed['query']) && $parsed['query'] !== '' ? '?' . $parsed['query'] : '';

        $_SERVER['REQUEST_URI'] = $updatedPath . $query;
        if (isset($_SERVER['PATH_INFO'])) {
            $_SERVER['PATH_INFO'] = $updatedPath;
        }
    }

    private static function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $path = preg_replace('#/+#', '/', $path);
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/') . '/';
        }

        return $path === '' ? '/' : $path;
    }

    private static function locale_from_query(): ?string
    {
        if (!isset($_GET['lang'])) {
            return null;
        }

        $lang = sanitize_text_field(wp_unslash($_GET['lang']));
        if ($lang === '') {
            return null;
        }

        return SVIC_Translator::normalizeLocaleCode($lang);
    }

    private static function locale_from_cookie(): ?string
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return null;
        }

        $lang = sanitize_text_field(wp_unslash($_COOKIE[self::COOKIE_NAME]));
        if ($lang === '') {
            return null;
        }

        return SVIC_Translator::normalizeLocaleCode($lang);
    }

    private static function locale_from_browser(): ?string
    {
        $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($accept === '') {
            return null;
        }

        $locales = explode(',', $accept);
        foreach ($locales as $locale) {
            $locale = trim($locale);
            if ($locale === '') {
                continue;
            }

            $locale = SVIC_Translator::normalizeLocaleCode($locale);
            if ($locale) {
                return $locale;
            }
        }

        return null;
    }

    private static function set_cookie(string $locale): void
    {
        setcookie(self::COOKIE_NAME, $locale, [
            'expires'  => time() + self::COOKIE_TTL,
            'path'     => '/',
            'secure'   => is_ssl(),
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::COOKIE_NAME] = $locale;
    }
}

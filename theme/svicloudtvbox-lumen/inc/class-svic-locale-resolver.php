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

    public static function bootstrap(): void
    {
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
        $requested = self::locale_from_query();
        if ($requested) {
            return SVIC_Translator::normalizeLocaleCode($requested);
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
        }
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

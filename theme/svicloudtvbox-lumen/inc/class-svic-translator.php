<?php
/**
 * Centralized translation loader for the SVICLOUD Lumen theme.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class SVIC_Translator
{
    private const DEFAULT_LOCALE = 'en_US';
    private const SUPPORTED_LOCALES = ['en_US', 'zh_TW', 'zh_CN'];
    private const CACHE_GROUP    = 'svic_i18n';

    private static ?self $instance = null;

    /**
     * In-request cache of locale registries.
     *
     * @var array<string,array>
     */
    /**
     * @var array<string,array{version:int,data:array}>
     */
    private array $registryCache = [];

    private function __construct() {}

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Return the supported locale codes for the theme.
     *
     * @return string[]
     */
    public static function supportedLocales(): array
    {
        $locales = apply_filters('svic_supported_locales', self::SUPPORTED_LOCALES);

        if (!is_array($locales) || !$locales) {
            return self::SUPPORTED_LOCALES;
        }

        $normalized = [];
        foreach ($locales as $candidate) {
            if (!is_string($candidate) && !is_numeric($candidate)) {
                continue;
            }

            $code = self::normalizeLocale((string) $candidate);
            if ($code === '') {
                continue;
            }

            if (!in_array($code, $normalized, true)) {
                $normalized[] = $code;
            }
        }

        return $normalized ?: self::SUPPORTED_LOCALES;
    }

    /**
     * Normalize an incoming locale identifier to one of the supported locales.
     */
    public static function normalizeLocaleCode(?string $locale): string
    {
        if (!is_string($locale) || $locale === '') {
            return self::DEFAULT_LOCALE;
        }

        $normalized = self::normalizeLocale($locale);
        $supported  = self::supportedLocales();

        if (in_array($normalized, $supported, true)) {
            return $normalized;
        }

        $language = strtolower(explode('_', $normalized)[0] ?? $normalized);
        foreach ($supported as $candidate) {
            $parts           = explode('_', $candidate);
            $candidateLocale = strtolower($parts[0] ?? $candidate);
            if ($candidateLocale === $language) {
                return $candidate;
            }
        }

        return self::DEFAULT_LOCALE;
    }

    public function translate(string $key, array $replacements = [], ?string $locale = null): string
    {
        $locale = $locale ? self::normalizeLocaleCode($locale) : $this->currentLocale();

        $value = $this->resolveKey($locale, $key);
        if ($value === null && $locale !== self::DEFAULT_LOCALE) {
            $value = $this->resolveKey(self::DEFAULT_LOCALE, $key);
        }

        if ($value === null) {
            $value = $this->fallbackForMissingKey($key);
        }

        if (!is_string($value)) {
            if (is_scalar($value)) {
                $value = (string) $value;
            } else {
                $value = $this->fallbackForMissingKey($key);
            }
        }

        if ($replacements) {
            $value = $this->applyTokens($value, $replacements);
        }

        return $value;
    }

    public function registry(string $locale): array
    {
        $locale = self::normalizeLocaleCode($locale);
        return $this->loadLocale($locale);
    }

    private function resolveKey(string $locale, string $key)
    {
        $registry = $this->loadLocale($locale);

        if (array_key_exists($key, $registry)) {
            return $registry[$key];
        }

        $segments = explode('.', $key);
        $cursor   = $registry;
        foreach ($segments as $segment) {
            if (!is_array($cursor) || !array_key_exists($segment, $cursor)) {
                return null;
            }
            $cursor = $cursor[$segment];
        }

        return $cursor;
    }

    private function loadLocale(string $locale): array
    {
        $file = $this->findLocaleFile($locale);
        $data = [];

        $version = 0;
        if ($file && file_exists($file)) {
            $version = (int) filemtime($file);
        }

        $cacheKey = 'registry_' . $locale;
        if ($version > 0) {
            $cacheKey .= '_' . $version;
        }

        if (isset($this->registryCache[$locale]) && is_array($this->registryCache[$locale])) {
            $entry = $this->registryCache[$locale];
            if (($entry['version'] ?? null) === $version && isset($entry['data']) && is_array($entry['data'])) {
                return $entry['data'];
            }
        }

        $cached = wp_cache_get($cacheKey, self::CACHE_GROUP);
        if (is_array($cached)) {
            $this->registryCache[$locale] = [
                'version' => $version,
                'data'    => $cached,
            ];
            return $cached;
        }

        if ($file && file_exists($file)) {
            $loaded = include $file;
            if (is_array($loaded)) {
                $data = $loaded;
            }
        }

        /** 
         * Allow plugins or child themes to mutate the registry for a locale.
         */
        $data = apply_filters('svic_locale_registry', $data, $locale);

        wp_cache_set($cacheKey, $data, self::CACHE_GROUP);
        $this->registryCache[$locale] = [
            'version' => $version,
            'data'    => $data,
        ];

        return $data;
    }

    private function findLocaleFile(string $locale): ?string
    {
        $baseDir = trailingslashit(get_template_directory()) . 'lang/';
        $path    = $baseDir . $locale . '.php';

        if (file_exists($path)) {
            return $path;
        }

        // Try generic language code (e.g., `zh`).
        $languageOnly = strtok($locale, '_-');
        if ($languageOnly && $languageOnly !== $locale) {
            $fallbackPath = $baseDir . $languageOnly . '.php';
            if (file_exists($fallbackPath)) {
                return $fallbackPath;
            }
        }

        return null;
    }

    private function applyTokens(string $value, array $replacements): string
    {
        $tokens = [];
        foreach ($replacements as $token => $replacement) {
            $tokens['{{' . $token . '}}'] = (string) $replacement;
        }

        return $tokens ? strtr($value, $tokens) : $value;
    }

    private function fallbackForMissingKey(string $key): string
    {
        $segments = explode('.', $key);
        return end($segments) ?: $key;
    }

    private function currentLocale(): string
    {
        $locale = apply_filters('svic_current_locale', get_locale());

        if (!is_string($locale) || $locale === '') {
            $locale = self::DEFAULT_LOCALE;
        }

        return self::normalizeLocaleCode($locale);
    }

    private static function normalizeLocale(string $locale): string
    {
        $locale = trim($locale);
        if ($locale === '') {
            return self::DEFAULT_LOCALE;
        }

        $parts  = preg_split('/[;,]/', $locale);
        $locale = is_array($parts) && isset($parts[0]) && $parts[0] !== '' ? trim((string) $parts[0]) : $locale;
        $locale = str_replace('-', '_', $locale);
        $locale = trim($locale);

        if ($locale === '') {
            return self::DEFAULT_LOCALE;
        }

        if (strpos($locale, '_') !== false) {
            [$language, $region] = explode('_', $locale, 2);
            $language = strtolower(trim((string) $language));
            $region   = strtoupper(trim((string) $region));

            if ($language === '') {
                $defaultParts = explode('_', self::DEFAULT_LOCALE);
                $language     = strtolower($defaultParts[0] ?? 'en');
            }

            if ($language === '') {
                $language = 'en';
            }

            if ($region === '') {
                return $language;
            }

            return $language . '_' . $region;
        }

        return strtolower($locale);
    }
}

<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Validator;

/**
 * Validates and sanitizes URLs for carousel items
 */
class UrlValidator
{
    private const ALLOWED_SCHEMES = ['http', 'https', ''];

    /** Schemes interdits (open redirect, XSS) */
    private const DANGEROUS_SCHEMES_PATTERN = '/^(javascript|data|vbscript|file|about|ftp|ws|wss):/i';

    /** Caractères de contrôle Unicode */
    private const CONTROL_CHARS_PATTERN = '/[\x00-\x1f\x7f-\x9f]/u';

    /**
     * Sanitize and validate a URL
     *
     * @param string $url The URL to validate
     * @return string Sanitized URL or '#' if invalid
     */
    public static function sanitize(string $url): string
    {
        if (empty(trim($url))) {
            return '#';
        }

        if (!self::isSafe($url)) {
            return '#';
        }

        return htmlspecialchars($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Check if a URL is safe
     *
     * @param string $url The URL to check
     * @return bool True if safe, false otherwise
     */
    public static function isSafe(string $url): bool
    {
        if (empty(trim($url))) {
            return false;
        }

        // Rejeter les URLs protocol-relative (//evil.com)
        if (str_starts_with($url, '//')) {
            return false;
        }

        if (preg_match(self::DANGEROUS_SCHEMES_PATTERN, $url)) {
            return false;
        }

        if (preg_match(self::CONTROL_CHARS_PATTERN, $url)) {
            return false;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme !== null && $scheme !== '' && !in_array(strtolower($scheme), self::ALLOWED_SCHEMES, true)) {
            return false;
        }

        // URLs absolues : validation supplémentaire
        if (str_contains($url, '://') && filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return true;
    }
}


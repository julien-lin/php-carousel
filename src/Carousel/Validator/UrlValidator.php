<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Validator;

/**
 * Validates and sanitizes URLs for carousel items
 */
class UrlValidator
{
    private const ALLOWED_SCHEMES = ['http', 'https', ''];
    
    /**
     * Sanitize and validate a URL
     * 
     * @param string $url The URL to validate
     * @return string Sanitized URL or '#' if invalid
     */
    public static function sanitize(string $url): string
    {
        if (empty($url)) {
            return '#';
        }
        
        // Check for dangerous schemes
        if (preg_match('/^(javascript|data|vbscript|file|about):/i', $url)) {
            return '#';
        }
        
        // Validate scheme if present
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme !== null && !in_array(strtolower($scheme), self::ALLOWED_SCHEMES, true)) {
            return '#';
        }
        
        // Escape the URL for HTML output
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
        if (empty($url)) {
            return false;
        }
        
        // Check for dangerous schemes
        if (preg_match('/^(javascript|data|vbscript|file|about):/i', $url)) {
            return false;
        }
        
        // Validate scheme if present
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme !== null && !in_array(strtolower($scheme), self::ALLOWED_SCHEMES, true)) {
            return false;
        }
        
        return true;
    }
}


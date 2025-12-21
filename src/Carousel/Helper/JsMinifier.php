<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Helper;

/**
 * Minifies JavaScript code
 */
class JsMinifier
{
    /**
     * Minify JavaScript code (optimized for better compression)
     * 
     * @param string $js JavaScript code to minify
     * @return string Minified JavaScript
     */
    public static function minify(string $js): string
    {
        // Preserve strings (quoted values) before processing
        $strings = [];
        $stringIndex = 0;
        $js = preg_replace_callback(
            '/(["\'])(?:(?=(\\\\?))\2.)*?\1/',
            function ($matches) use (&$strings, &$stringIndex) {
                $placeholder = '___STRING_' . $stringIndex . '___';
                $strings[$stringIndex] = $matches[0];
                $stringIndex++;
                return $placeholder;
            },
            $js
        );
        
        // Remove single-line comments (but preserve URLs)
        $js = preg_replace('/(?<!:)\/\/.*$/m', '', $js);
        
        // Remove multi-line comments (but preserve regex patterns)
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        
        // Remove whitespace around operators (but preserve in strings)
        $js = preg_replace('/\s*([=+\-*\/%<>!&|?:,;{}()\[\]])\s*/', '$1', $js);
        
        // Remove multiple spaces
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove spaces at start of lines
        $js = preg_replace('/^\s+/m', '', $js);
        
        // Remove spaces at end of lines
        $js = preg_replace('/\s+$/m', '', $js);
        
        // Remove empty lines
        $js = preg_replace('/\n\s*\n/', '', $js);
        
        // Remove all newlines and remaining whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove trailing semicolons before closing braces (but not in for loops)
        $js = preg_replace('/;\s*}/', '}', $js);
        
        // Remove spaces after keywords (if, for, while, etc.)
        $js = preg_replace('/\b(if|for|while|switch|function|return|var|let|const|new|typeof|instanceof|in|of)\s+/', '$1 ', $js);
        
        // Remove spaces before and after operators (but preserve in expressions)
        $js = preg_replace('/\s*([=+\-*\/%<>!&|?:,;])\s*/', '$1', $js);
        
        // Restore strings
        foreach ($strings as $index => $string) {
            $js = str_replace('___STRING_' . $index . '___', $string, $js);
        }
        
        return trim($js);
    }
}


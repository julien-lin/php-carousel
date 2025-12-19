<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Helper;

/**
 * Minifies JavaScript code
 */
class JsMinifier
{
    /**
     * Minify JavaScript code (basic minification)
     * 
     * @param string $js JavaScript code to minify
     * @return string Minified JavaScript
     */
    public static function minify(string $js): string
    {
        // Remove single-line comments (but preserve URLs and strings)
        $js = preg_replace('/(?<!:)\/\/.*$/m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        
        // Remove whitespace around operators
        $js = preg_replace('/\s*([=+\-*\/%<>!&|?:,;{}()\[\]])\s*/', '$1', $js);
        
        // Remove multiple spaces
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove spaces at start of lines
        $js = preg_replace('/^\s+/m', '', $js);
        
        // Remove spaces at end of lines
        $js = preg_replace('/\s+$/m', '', $js);
        
        // Remove empty lines
        $js = preg_replace('/\n\s*\n/', "\n", $js);
        
        // Remove trailing semicolons before closing braces
        $js = preg_replace('/;\s*}/', '}', $js);
        
        return trim($js);
    }
}


<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Helper;

/**
 * Minifies CSS code
 */
class CssMinifier
{
    /**
     * Minify CSS code
     * 
     * @param string $css CSS code to minify
     * @return string Minified CSS
     */
    public static function minify(string $css): string
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        
        // Remove spaces around operators
        $css = preg_replace('/\s*([{}:;,])\s*/', '$1', $css);
        
        // Remove trailing semicolons before closing braces
        $css = preg_replace('/;}/', '}', $css);
        
        // Remove spaces after colons
        $css = str_replace(': ', ':', $css);
        
        // Remove spaces before opening braces
        $css = str_replace(' {', '{', $css);
        
        // Remove spaces after opening braces
        $css = str_replace('{ ', '{', $css);
        
        // Remove spaces before closing braces
        $css = str_replace(' }', '}', $css);
        
        // Remove spaces after closing braces
        $css = str_replace('} ', '}', $css);
        
        // Remove spaces after semicolons
        $css = str_replace('; ', ';', $css);
        
        // Remove spaces after commas
        $css = str_replace(', ', ',', $css);
        
        return trim($css);
    }
}


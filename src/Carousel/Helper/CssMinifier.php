<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Helper;

/**
 * Minifies CSS code
 */
class CssMinifier
{
    /**
     * Minify CSS code (optimized for better compression)
     * 
     * @param string $css CSS code to minify
     * @return string Minified CSS
     */
    public static function minify(string $css): string
    {
        // Remove comments (including those with special characters)
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Preserve strings (quoted values) before processing
        $strings = [];
        $stringIndex = 0;
        $css = preg_replace_callback(
            '/(["\'])(?:(?=(\\\\?))\2.)*?\1/',
            function ($matches) use (&$strings, &$stringIndex) {
                $placeholder = '___STRING_' . $stringIndex . '___';
                $strings[$stringIndex] = $matches[0];
                $stringIndex++;
                return $placeholder;
            },
            $css
        );
        
        // Remove whitespace (but preserve in calc(), attr(), etc.)
        $css = preg_replace('/\s+/', ' ', $css);
        
        // Remove spaces around operators (but preserve in calc())
        $css = preg_replace_callback(
            '/calc\([^)]+\)/',
            function ($matches) {
                return str_replace(' ', '', $matches[0]);
            },
            $css
        );
        
        // Remove spaces around operators (but preserve colons)
        $css = preg_replace('/\s*([{};,])\s*/', '$1', $css);
        
        // Remove spaces around colons (but preserve in URLs and calc)
        $css = preg_replace('/\s*:\s*(?![^()]*\))/', ':', $css);
        
        // Remove trailing semicolons before closing braces
        $css = preg_replace('/;}/', '}', $css);
        
        // Remove spaces before opening braces
        $css = preg_replace('/\s*{/', '{', $css);
        
        // Remove spaces after opening braces
        $css = preg_replace('/{\s+/', '{', $css);
        
        // Remove spaces before closing braces
        $css = preg_replace('/\s*}/', '}', $css);
        
        // Remove spaces after closing braces
        $css = preg_replace('/}\s+/', '}', $css);
        
        // Remove spaces after semicolons
        $css = preg_replace('/;\s+/', ';', $css);
        
        // Remove spaces after commas (but preserve in rgba, etc.)
        $css = preg_replace('/,\s+/', ',', $css);
        
        // Remove leading zeros in decimal numbers (0.5 -> .5, but not 0px)
        $css = preg_replace('/([^0-9])0+\.([0-9]+)/', '$1.$2', $css);
        
        // Remove units from zero values (0px -> 0, 0em -> 0) but preserve in calc()
        // Match: space or colon before 0, unit, then space or semicolon or closing brace
        $css = preg_replace('/([: ])0(px|em|rem|pt|pc|in|cm|mm|ex|ch|vw|vh|vmin|vmax|%|deg|rad|grad|ms|s|Hz|kHz)([; }])/i', '$10$3', $css);
        
        // Shorten color values (#ffffff -> #fff, #000000 -> #000)
        $css = preg_replace('/#([0-9a-f])\1([0-9a-f])\2([0-9a-f])\3/i', '#$1$2$3', $css);
        
        // Restore strings
        foreach ($strings as $index => $string) {
            $css = str_replace('___STRING_' . $index . '___', $string, $css);
        }
        
        return trim($css);
    }
}


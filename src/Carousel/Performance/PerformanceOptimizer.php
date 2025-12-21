<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Performance;

use JulienLinard\Carousel\Carousel;

/**
 * Performance optimization utilities
 */
class PerformanceOptimizer
{
    /**
     * Extract critical CSS (above-the-fold styles)
     * 
     * @param string $css Full CSS
     * @param array $criticalSelectors Selectors to include in critical CSS
     * @return string Critical CSS
     */
    public static function extractCriticalCss(string $css, array $criticalSelectors = []): string
    {
        if (empty($criticalSelectors)) {
            // Default critical selectors for carousel
            $criticalSelectors = [
                '#carousel-',
                '.carousel-container',
                '.carousel-wrapper',
                '.carousel-track',
                '.carousel-slide',
                '.carousel-arrow',
                '.carousel-loading',
            ];
        }
        
        $criticalCss = '';
        $lines = explode("\n", $css);
        $inCriticalBlock = false;
        $braceCount = 0;
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Check if line contains critical selector
            $isCritical = false;
            foreach ($criticalSelectors as $selector) {
                if (strpos($trimmedLine, $selector) !== false) {
                    $isCritical = true;
                    break;
                }
            }
            
            if ($isCritical || $inCriticalBlock) {
                $criticalCss .= $line . "\n";
                
                // Track braces to know when block ends
                $braceCount += substr_count($line, '{') - substr_count($line, '}');
                $inCriticalBlock = $braceCount > 0;
            }
        }
        
        return trim($criticalCss);
    }

    /**
     * Preload critical images
     * 
     * @param Carousel $carousel Carousel instance
     * @return string HTML link tags for preloading
     */
    public static function generatePreloadLinks(Carousel $carousel): string
    {
        $items = $carousel->getItems();
        $preloadCount = min(3, count($items)); // Preload first 3 images
        $links = '';
        
        for ($i = 0; $i < $preloadCount; $i++) {
            $item = $items[$i] ?? null;
            if (!$item) {
                continue;
            }
            
            // Handle both array and CarouselItem object
            if ($item instanceof \JulienLinard\Carousel\CarouselItem) {
                $imageUrl = $item->image ?? '';
            } else {
                $imageUrl = $item['image'] ?? $item['src'] ?? null;
            }
            
            if (empty($imageUrl)) {
                continue;
            }
            
            if ($imageUrl) {
                $links .= '<link rel="preload" as="image" href="' . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') . '">' . "\n";
            }
        }
        
        return $links;
    }

    /**
     * Generate resource hints (DNS prefetch, preconnect)
     * 
     * @param array $domains Domains to prefetch/preconnect
     * @return string HTML link tags for resource hints
     */
    public static function generateResourceHints(array $domains): string
    {
        $hints = '';
        
        foreach ($domains as $domain) {
            // DNS prefetch for external domains
            if (strpos($domain, 'http') === 0) {
                $parsed = parse_url($domain);
                $domain = $parsed['host'] ?? $domain;
            }
            
            $hints .= '<link rel="dns-prefetch" href="//' . htmlspecialchars($domain, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        }
        
        return $hints;
    }

    /**
     * Optimize image sources for performance
     * 
     * @param string $imageUrl Image URL
     * @param array $options Optimization options (width, height, quality, format)
     * @return string Optimized image URL or srcset
     */
    public static function optimizeImageUrl(string $imageUrl, array $options = []): string
    {
        // If image service supports optimization (e.g., Cloudinary, Imgix)
        // This is a placeholder for future implementation
        // For now, return original URL
        
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;
        $quality = $options['quality'] ?? 85;
        $format = $options['format'] ?? 'auto';
        
        // Example: If using Cloudinary
        // return str_replace('/upload/', '/upload/w_' . $width . ',h_' . $height . ',q_' . $quality . ',f_' . $format . '/', $imageUrl);
        
        return $imageUrl;
    }

    /**
     * Calculate estimated bundle size
     * 
     * @param string $html HTML output
     * @param string $css CSS output
     * @param string $js JavaScript output
     * @return array Size information (html, css, js, total in bytes)
     */
    public static function calculateBundleSize(string $html, string $css, string $js): array
    {
        return [
            'html' => strlen($html),
            'css' => strlen($css),
            'js' => strlen($js),
            'total' => strlen($html) + strlen($css) + strlen($js),
        ];
    }

    /**
     * Get performance recommendations
     * 
     * @param array $bundleSize Bundle size information
     * @return array Recommendations
     */
    public static function getPerformanceRecommendations(array $bundleSize): array
    {
        $recommendations = [];
        
        // CSS recommendations
        if ($bundleSize['css'] > 50000) { // 50KB
            $recommendations[] = [
                'type' => 'css',
                'message' => 'CSS bundle is large. Consider using critical CSS extraction.',
                'severity' => 'warning',
            ];
        }
        
        // JS recommendations
        if ($bundleSize['js'] > 100000) { // 100KB
            $recommendations[] = [
                'type' => 'js',
                'message' => 'JavaScript bundle is large. Consider code splitting or lazy loading.',
                'severity' => 'warning',
            ];
        }
        
        // Total recommendations
        if ($bundleSize['total'] > 200000) { // 200KB
            $recommendations[] = [
                'type' => 'total',
                'message' => 'Total bundle size is large. Consider optimizing assets.',
                'severity' => 'info',
            ];
        }
        
        return $recommendations;
    }
}


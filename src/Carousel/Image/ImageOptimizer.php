<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Image;

/**
 * Helper class to generate responsive image source sets
 */
class ImageOptimizer
{
    /**
     * Generate ImageSourceSet from a base image URL
     * 
     * Assumes naming convention: base-image-{width}w.{ext}
     * 
     * @param string $baseUrl Base image URL (e.g., "image.jpg")
     * @param array $widths Array of widths (e.g., [400, 800, 1200])
     * @param array $formats Formats to generate (e.g., ['webp', 'jpg'])
     * @param string|null $alt Alt text
     * @return ImageSourceSet
     */
    public static function generateFromBase(
        string $baseUrl,
        array $widths = [400, 800, 1200],
        array $formats = ['webp', 'jpg'],
        ?string $alt = null
    ): ImageSourceSet {
        $pathInfo = pathinfo($baseUrl);
        $extension = $pathInfo['extension'] ?? 'jpg';
        $baseName = $pathInfo['filename'] ?? 'image';
        $directory = $pathInfo['dirname'] ?? '.';
        
        if ($directory !== '.') {
            $directory .= '/';
        } else {
            $directory = '';
        }
        
        $sourceSet = new ImageSourceSet($baseUrl, $alt);
        
        // Generate srcset for each format
        foreach ($formats as $format) {
            $srcsetParts = [];
            foreach ($widths as $width) {
                $srcsetParts[] = "{$directory}{$baseName}-{$width}w.{$format} {$width}w";
            }
            $srcset = implode(', ', $srcsetParts);
            
            $mimeType = ImageSourceSet::getMimeType($format);
            $sourceSet->addSource($srcset, null, $mimeType);
        }
        
        return $sourceSet;
    }

    /**
     * Generate ImageSourceSet with media queries
     * 
     * @param string $baseUrl Base image URL
     * @param array $breakpoints Array of [width => mediaQuery] (e.g., [400 => '(max-width: 400px)', 800 => '(max-width: 800px)'])
     * @param array $formats Formats to generate
     * @param string|null $alt Alt text
     * @return ImageSourceSet
     */
    public static function generateWithBreakpoints(
        string $baseUrl,
        array $breakpoints = [
            400 => '(max-width: 400px)',
            800 => '(max-width: 800px)',
            1200 => null, // Default/desktop
        ],
        array $formats = ['webp', 'jpg'],
        ?string $alt = null
    ): ImageSourceSet {
        $pathInfo = pathinfo($baseUrl);
        $extension = $pathInfo['extension'] ?? 'jpg';
        $baseName = $pathInfo['filename'] ?? 'image';
        $directory = $pathInfo['dirname'] ?? '.';
        
        if ($directory !== '.') {
            $directory .= '/';
        } else {
            $directory = '';
        }
        
        $sourceSet = new ImageSourceSet($baseUrl, $alt);
        
        // Generate sources for each format and breakpoint
        foreach ($formats as $format) {
            foreach ($breakpoints as $width => $media) {
                $srcset = "{$directory}{$baseName}-{$width}w.{$format} {$width}w";
                $mimeType = ImageSourceSet::getMimeType($format);
                $sourceSet->addSource($srcset, $media, $mimeType);
            }
        }
        
        return $sourceSet;
    }

    /**
     * Create simple ImageSourceSet from array configuration
     * 
     * @param array $config Configuration array:
     *   - 'fallback': string (required)
     *   - 'alt': string (optional)
     *   - 'sources': array of ['srcset' => string, 'media' => string|null, 'type' => string|null]
     * @return ImageSourceSet
     */
    public static function fromArray(array $config): ImageSourceSet
    {
        if (!isset($config['fallback'])) {
            throw new \InvalidArgumentException('fallback is required');
        }
        
        $sourceSet = new ImageSourceSet($config['fallback'], $config['alt'] ?? null);
        
        if (isset($config['sources']) && is_array($config['sources'])) {
            foreach ($config['sources'] as $source) {
                $sourceSet->addSource(
                    $source['srcset'] ?? '',
                    $source['media'] ?? null,
                    $source['type'] ?? null
                );
            }
        }
        
        return $sourceSet;
    }
}


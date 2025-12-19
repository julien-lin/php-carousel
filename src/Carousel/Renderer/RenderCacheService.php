<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

/**
 * Service to manage rendering cache
 * Centralizes the static cache previously in CarouselRenderer
 */
class RenderCacheService
{
    private static array $renderedCarousels = [];

    /**
     * Check if a carousel has already been rendered
     * 
     * @param string $id Carousel ID
     * @param string $type Render type ('html', 'css', 'js', 'api')
     * @return bool True if already rendered
     */
    public static function isRendered(string $id, string $type = 'html'): bool
    {
        $key = self::getCacheKey($id, $type);
        return isset(self::$renderedCarousels[$key]);
    }

    /**
     * Mark a carousel as rendered
     * 
     * @param string $id Carousel ID
     * @param string $type Render type ('html', 'css', 'js', 'api')
     * @return void
     */
    public static function markAsRendered(string $id, string $type = 'html'): void
    {
        $key = self::getCacheKey($id, $type);
        self::$renderedCarousels[$key] = true;
    }

    /**
     * Clear the entire cache
     * 
     * @return void
     */
    public static function clear(): void
    {
        self::$renderedCarousels = [];
    }

    /**
     * Get cache key for a carousel ID and type
     * 
     * @param string $id Carousel ID
     * @param string $type Render type
     * @return string Cache key
     */
    private static function getCacheKey(string $id, string $type): string
    {
        if ($type === 'html') {
            return $id;
        }
        if ($type === 'api') {
            return '_api';
        }
        return $id . '_' . $type;
    }

    /**
     * Get all cached keys (for debugging)
     * 
     * @return array Array of cache keys
     */
    public static function getCachedKeys(): array
    {
        return array_keys(self::$renderedCarousels);
    }

    /**
     * Check if API has been rendered
     * 
     * @return bool True if API is already rendered
     */
    public static function isApiRendered(): bool
    {
        return self::isRendered('', 'api');
    }

    /**
     * Mark API as rendered
     * 
     * @return void
     */
    public static function markApiAsRendered(): void
    {
        self::markAsRendered('', 'api');
    }
}


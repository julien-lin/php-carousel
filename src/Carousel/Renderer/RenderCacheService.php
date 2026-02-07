<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Performance\RenderCacheInterface;

/**
 * Service to manage rendering cache
 * Centralizes the static cache previously in CarouselRenderer.
 * Optionally delegates to a persistent cache (RenderCacheInterface) when set.
 */
class RenderCacheService
{
    private static array $renderedCarousels = [];

    private static ?RenderCacheInterface $persistentCache = null;

    private static int $defaultTtl = 3600;

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
     * Set optional persistent cache (file, Redis, PSR-6). When set, CSS/HTML/JS can be stored and retrieved across requests.
     */
    public static function setPersistentCache(?RenderCacheInterface $cache, int $defaultTtl = 3600): void
    {
        self::$persistentCache = $cache;
        self::$defaultTtl = $defaultTtl;
    }

    /**
     * Get cached content from persistent cache (if configured).
     *
     * @return string|null Cached content or null
     */
    public static function getCachedContent(string $id, string $type = 'html'): ?string
    {
        if (self::$persistentCache === null) {
            return null;
        }
        $key = 'carousel_' . self::getCacheKey($id, $type);
        return self::$persistentCache->get($key);
    }

    /**
     * Store content in persistent cache (if configured).
     */
    public static function setCachedContent(string $id, string $type, string $content, ?int $ttl = null): void
    {
        if (self::$persistentCache === null) {
            return;
        }
        $key = 'carousel_' . self::getCacheKey($id, $type);
        self::$persistentCache->set($key, $content, $ttl ?? self::$defaultTtl);
    }

    /**
     * Clear the entire in-memory cache (does not clear persistent cache).
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


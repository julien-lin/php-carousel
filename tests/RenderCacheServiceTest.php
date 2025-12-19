<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Renderer\RenderCacheService;

class RenderCacheServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear cache before each test
        RenderCacheService::clear();
    }

    /**
     * Test isRendered returns false by default
     */
    public function testIsRenderedReturnsFalseByDefault(): void
    {
        $this->assertFalse(RenderCacheService::isRendered('test-id'));
    }

    /**
     * Test markAsRendered then isRendered
     */
    public function testMarkAsRenderedThenIsRendered(): void
    {
        $this->assertFalse(RenderCacheService::isRendered('test-id'));
        
        RenderCacheService::markAsRendered('test-id');
        
        $this->assertTrue(RenderCacheService::isRendered('test-id'));
    }

    /**
     * Test clear() resets cache
     */
    public function testClearResetsCache(): void
    {
        RenderCacheService::markAsRendered('test-id');
        $this->assertTrue(RenderCacheService::isRendered('test-id'));
        
        RenderCacheService::clear();
        
        $this->assertFalse(RenderCacheService::isRendered('test-id'));
    }

    /**
     * Test different types (html, css, js)
     */
    public function testDifferentTypes(): void
    {
        RenderCacheService::markAsRendered('test-id', 'html');
        RenderCacheService::markAsRendered('test-id', 'css');
        RenderCacheService::markAsRendered('test-id', 'js');
        
        $this->assertTrue(RenderCacheService::isRendered('test-id', 'html'));
        $this->assertTrue(RenderCacheService::isRendered('test-id', 'css'));
        $this->assertTrue(RenderCacheService::isRendered('test-id', 'js'));
    }

    /**
     * Test HTML type is default
     */
    public function testHtmlTypeIsDefault(): void
    {
        RenderCacheService::markAsRendered('test-id');
        
        $this->assertTrue(RenderCacheService::isRendered('test-id', 'html'));
        $this->assertTrue(RenderCacheService::isRendered('test-id'));
    }

    /**
     * Test different IDs are independent
     */
    public function testDifferentIdsAreIndependent(): void
    {
        RenderCacheService::markAsRendered('id1');
        RenderCacheService::markAsRendered('id2');
        
        $this->assertTrue(RenderCacheService::isRendered('id1'));
        $this->assertTrue(RenderCacheService::isRendered('id2'));
        $this->assertFalse(RenderCacheService::isRendered('id3'));
    }

    /**
     * Test API rendering cache
     */
    public function testApiRenderingCache(): void
    {
        $this->assertFalse(RenderCacheService::isApiRendered());
        
        RenderCacheService::markApiAsRendered();
        
        $this->assertTrue(RenderCacheService::isApiRendered());
    }

    /**
     * Test API cache is independent from carousel cache
     */
    public function testApiCacheIsIndependent(): void
    {
        RenderCacheService::markAsRendered('test-id');
        $this->assertFalse(RenderCacheService::isApiRendered());
        
        RenderCacheService::markApiAsRendered();
        $this->assertTrue(RenderCacheService::isApiRendered());
        $this->assertTrue(RenderCacheService::isRendered('test-id'));
    }

    /**
     * Test getCachedKeys returns all keys
     */
    public function testGetCachedKeysReturnsAllKeys(): void
    {
        $this->assertEmpty(RenderCacheService::getCachedKeys());
        
        RenderCacheService::markAsRendered('id1', 'html');
        RenderCacheService::markAsRendered('id1', 'css');
        RenderCacheService::markAsRendered('id2', 'html');
        RenderCacheService::markApiAsRendered();
        
        $keys = RenderCacheService::getCachedKeys();
        $this->assertCount(4, $keys);
        $this->assertContains('id1', $keys);
        $this->assertContains('id1_css', $keys);
        $this->assertContains('id2', $keys);
        $this->assertContains('_api', $keys);
    }

    /**
     * Test cache persists across multiple calls
     */
    public function testCachePersistsAcrossMultipleCalls(): void
    {
        RenderCacheService::markAsRendered('test-id');
        
        $this->assertTrue(RenderCacheService::isRendered('test-id'));
        $this->assertTrue(RenderCacheService::isRendered('test-id'));
        $this->assertTrue(RenderCacheService::isRendered('test-id'));
    }
}


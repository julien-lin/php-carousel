<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselRenderer;
use JulienLinard\Carousel\Renderer\JsRenderer;
use JulienLinard\Carousel\Renderer\RenderCacheService;

/**
 * Tests to verify JsRenderer output matches CarouselRenderer output
 */
class JsRendererMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear cache before each test
        RenderCacheService::clear();
    }

    /**
     * Test JS output is identical for image carousel
     * Note: We compare only the carousel-specific script, not the API (which is included once globally)
     */
    public function testJsOutputIdenticalForImageCarousel(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        // Extract only the carousel-specific script (not the API)
        $legacyJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $legacyJs);
        
        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        // Extract only the carousel-specific script (not the API)
        $newJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $newJs);
        
        $this->assertEquals($legacyJs, $newJs);
    }

    /**
     * Test JS output is identical for card carousel
     */
    public function testJsOutputIdenticalForCardCarousel(): void
    {
        $carousel = Carousel::card('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Card 1', 'content' => 'Content 1', 'image' => 'card1.jpg'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        $legacyJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $legacyJs);
        
        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        $newJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $newJs);
        
        $this->assertEquals($legacyJs, $newJs);
    }

    /**
     * Test JS output is identical with options
     */
    public function testJsOutputIdenticalWithOptions(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions([
            'autoplay' => false,
            'autoplayInterval' => 3000,
            'loop' => false,
            'transition' => 'fade',
            'keyboardNavigation' => false,
            'touchSwipe' => false,
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        $legacyJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $legacyJs);
        
        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        $newJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $newJs);
        
        $this->assertEquals($legacyJs, $newJs);
    }

    /**
     * Test JS output is identical with minification
     */
    public function testJsOutputIdenticalWithMinification(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions(['minify' => true]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        
        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        
        // Extract only carousel-specific script (not API) from both
        preg_match('/<script id="carousel-script-[^"]+">(.*?)<\/script>/s', $legacyJs, $legacyMatches);
        preg_match('/<script id="carousel-script-[^"]+">(.*?)<\/script>/s', $newJs, $newMatches);
        
        $this->assertNotEmpty($legacyMatches[1] ?? null, 'Legacy JS should contain carousel script');
        $this->assertNotEmpty($newMatches[1] ?? null, 'New JS should contain carousel script');
        
        // Compare minified carousel scripts (they should be identical)
        $this->assertEquals(trim($legacyMatches[1] ?? ''), trim($newMatches[1] ?? ''));
    }

    /**
     * Test JS output contains CarouselAPI
     */
    public function testJsOutputContainsCarouselApi(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $newRenderer = new JsRenderer();
        $js = $newRenderer->render($carousel);
        
        $this->assertStringContainsString('window.CarouselAPI', $js);
        $this->assertStringContainsString('CarouselInstance', $js);
    }

    /**
     * Test JS cache works correctly
     */
    public function testJsCacheWorksCorrectly(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $newRenderer = new JsRenderer();
        $firstJs = $newRenderer->render($carousel);
        
        // Second render should return empty string (cached)
        $secondJs = $newRenderer->render($carousel);
        
        $this->assertNotEmpty($firstJs);
        $this->assertEmpty($secondJs);
    }

    /**
     * Test JS API is included only once globally
     */
    public function testJsApiIncludedOnlyOnceGlobally(): void
    {
        $carousel1 = Carousel::image('test1-' . uniqid(), ['image1.jpg']);
        $carousel2 = Carousel::image('test2-' . uniqid(), ['image2.jpg']);
        
        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $js1 = $newRenderer->render($carousel1);
        $js2 = $newRenderer->render($carousel2);
        
        // First render should include API
        $this->assertStringContainsString('<script id="carousel-api">', $js1);
        $this->assertStringContainsString('window.CarouselAPI', $js1);
        
        // Second render should NOT include API (already included)
        $this->assertStringNotContainsString('<script id="carousel-api">', $js2);
        $this->assertStringContainsString('carousel-script-', $js2); // But should still have carousel script
    }

    /**
     * Test JS output with different locales
     */
    public function testJsOutputWithDifferentLocales(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions(['locale' => 'fr']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        $legacyJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $legacyJs);
        
        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        $newJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $newJs);
        
        $this->assertEquals($legacyJs, $newJs);
        // Verify French translation is present
        $this->assertStringContainsString('sur', $newJs); // "sur" is in "Slide {current} sur {total}"
    }

    /**
     * Test JS output contains all required functions
     */
    public function testJsOutputContainsAllRequiredFunctions(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $newRenderer = new JsRenderer();
        $js = $newRenderer->render($carousel);
        
        $this->assertStringContainsString('function goToSlide', $js);
        $this->assertStringContainsString('function nextSlide', $js);
        $this->assertStringContainsString('function prevSlide', $js);
        $this->assertStringContainsString('function destroy', $js);
        $this->assertStringContainsString('function updateCarousel', $js);
        $this->assertStringContainsString('function resetAutoplay', $js);
    }
}


<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselRenderer;
use JulienLinard\Carousel\Renderer\CssRenderer;
use JulienLinard\Carousel\Renderer\RenderCacheService;

/**
 * Tests to verify CssRenderer output matches CarouselRenderer output
 */
class CssRendererMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear cache before each test
        RenderCacheService::clear();
    }

    /**
     * Test CSS output is identical for image carousel
     */
    public function testCssOutputIdenticalForImageCarousel(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $legacyRenderer->renderCss();
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical for card carousel
     */
    public function testCssOutputIdenticalForCardCarousel(): void
    {
        $carousel = Carousel::card('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Card 1', 'content' => 'Content 1', 'image' => 'card1.jpg'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $legacyRenderer->renderCss();
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical for testimonial carousel
     */
    public function testCssOutputIdenticalForTestimonialCarousel(): void
    {
        $carousel = Carousel::testimonial('test-' . uniqid(), [
            ['id' => '1', 'title' => 'John Doe', 'content' => 'Great product!'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $legacyRenderer->renderCss();
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical for gallery carousel
     */
    public function testCssOutputIdenticalForGalleryCarousel(): void
    {
        $carousel = Carousel::gallery('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Gallery 1', 'image' => 'gallery1.jpg'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $legacyRenderer->renderCss();
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical with options
     */
    public function testCssOutputIdenticalWithOptions(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions([
            'gap' => 20,
            'transitionDuration' => 1000,
            'height' => '500px',
            'itemsPerSlide' => 2,
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $legacyRenderer->renderCss();
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical with minification
     */
    public function testCssOutputIdenticalWithMinification(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions(['minify' => true]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $legacyRenderer->renderCss();
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical for infinite carousel
     */
    public function testCssOutputIdenticalForInfiniteCarousel(): void
    {
        $carousel = Carousel::infiniteCarousel('test-' . uniqid(), ['image1.jpg', 'image2.jpg']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $legacyRenderer->renderCss();
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS cache works correctly
     */
    public function testCssCacheWorksCorrectly(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $newRenderer = new CssRenderer();
        $firstCss = $newRenderer->render($carousel);
        
        // Second render should return empty string (cached)
        $secondCss = $newRenderer->render($carousel);
        
        $this->assertNotEmpty($firstCss);
        $this->assertEmpty($secondCss);
    }
}


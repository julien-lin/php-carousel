<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselRenderer;
use JulienLinard\Carousel\Renderer\HtmlRenderer;

/**
 * Tests to verify HtmlRenderer output matches CarouselRenderer output
 */
class HtmlRendererMigrationTest extends TestCase
{
    /**
     * Test HTML output is identical for image carousel
     */
    public function testHtmlOutputIdenticalForImageCarousel(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg', 'image2.jpg']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyHtml = $legacyRenderer->renderHtml();
        
        $newRenderer = new HtmlRenderer();
        $newHtml = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyHtml, $newHtml);
    }

    /**
     * Test HTML output is identical for card carousel
     */
    public function testHtmlOutputIdenticalForCardCarousel(): void
    {
        $carousel = Carousel::card('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Card 1', 'content' => 'Content 1', 'image' => 'card1.jpg'],
            ['id' => '2', 'title' => 'Card 2', 'content' => 'Content 2', 'image' => 'card2.jpg'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyHtml = $legacyRenderer->renderHtml();
        
        $newRenderer = new HtmlRenderer();
        $newHtml = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyHtml, $newHtml);
    }

    /**
     * Test HTML output is identical for testimonial carousel
     */
    public function testHtmlOutputIdenticalForTestimonialCarousel(): void
    {
        $carousel = Carousel::testimonial('test-' . uniqid(), [
            ['id' => '1', 'title' => 'John Doe', 'content' => 'Great product!', 'image' => 'avatar1.jpg'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyHtml = $legacyRenderer->renderHtml();
        
        $newRenderer = new HtmlRenderer();
        $newHtml = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyHtml, $newHtml);
    }

    /**
     * Test HTML output is identical for gallery carousel
     */
    public function testHtmlOutputIdenticalForGalleryCarousel(): void
    {
        $carousel = Carousel::gallery('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Gallery 1', 'image' => 'gallery1.jpg'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyHtml = $legacyRenderer->renderHtml();
        
        $newRenderer = new HtmlRenderer();
        $newHtml = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyHtml, $newHtml);
    }

    /**
     * Test HTML output is identical with options
     */
    public function testHtmlOutputIdenticalWithOptions(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions([
            'showArrows' => false,
            'showDots' => false,
            'showThumbnails' => true,
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyHtml = $legacyRenderer->renderHtml();
        
        $newRenderer = new HtmlRenderer();
        $newHtml = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyHtml, $newHtml);
    }

    /**
     * Test HTML output is identical with ImageSourceSet
     */
    public function testHtmlOutputIdenticalWithImageSourceSet(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $item = $carousel->getItems()[0];
        
        $sourceSet = new \JulienLinard\Carousel\Image\ImageSourceSet('fallback.jpg', 'Test image');
        $sourceSet->addSource('image-400w.webp 400w', '(max-width: 400px)', 'image/webp');
        $item->setImageSourceSet($sourceSet);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyHtml = $legacyRenderer->renderHtml();
        
        $newRenderer = new HtmlRenderer();
        $newHtml = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyHtml, $newHtml);
    }

    /**
     * Test HTML output handles empty carousel exception
     */
    public function testHtmlOutputHandlesEmptyCarouselException(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE);
        
        $newRenderer = new HtmlRenderer();
        
        $this->expectException(\JulienLinard\Carousel\Exception\EmptyCarouselException::class);
        $newRenderer->render($carousel);
    }

    /**
     * Test HTML output is identical for infinite carousel
     */
    public function testHtmlOutputIdenticalForInfiniteCarousel(): void
    {
        $carousel = Carousel::infiniteCarousel('test-' . uniqid(), ['image1.jpg', 'image2.jpg', 'image3.jpg']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyHtml = $legacyRenderer->renderHtml();
        
        $newRenderer = new HtmlRenderer();
        $newHtml = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyHtml, $newHtml);
    }

    /**
     * Test HTML output with different locales
     */
    public function testHtmlOutputWithDifferentLocales(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions(['locale' => 'fr']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyHtml = $legacyRenderer->renderHtml();
        
        $newRenderer = new HtmlRenderer();
        $newHtml = $newRenderer->render($carousel);
        
        $this->assertEquals($legacyHtml, $newHtml);
        $this->assertStringContainsString('Chargement du carousel', $newHtml);
    }
}


<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\RenderCacheService;
use JulienLinard\Carousel\SSR\SSRRenderer;
use PHPUnit\Framework\TestCase;

class SSRTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RenderCacheService::clear();
    }

    public function testRenderStaticReturnsHtmlAndCss(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg', 'image2.jpg']);
        
        $static = $carousel->renderStatic();
        
        $this->assertStringContainsString('<style', $static);
        $this->assertStringContainsString('<div class="carousel-container"', $static);
        $this->assertStringNotContainsString('<script', $static);
    }

    public function testRenderStaticContainsAllNecessaryElements(): void
    {
        $carousel = Carousel::card('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Card 1', 'image' => 'card1.jpg'],
        ]);
        
        $static = $carousel->renderStatic();
        
        $this->assertStringContainsString('carousel-container', $static);
        $this->assertStringContainsString('carousel-track', $static);
        $this->assertStringContainsString('carousel-slide', $static);
    }

    public function testHydrateAddsJavaScript(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $static = $carousel->renderStatic();
        $hydrated = $carousel->hydrate($static);
        
        $this->assertStringContainsString('<script', $hydrated);
        $this->assertStringContainsString('window.CarouselAPI', $hydrated);
    }

    public function testHydratePreservesStaticContent(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $static = $carousel->renderStatic();
        $hydrated = $carousel->hydrate($static);
        
        // Static content should still be present
        $this->assertStringContainsString('<style', $hydrated);
        $this->assertStringContainsString('<div class="carousel-container"', $hydrated);
    }

    public function testSSRRendererRenderStatic(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $ssrRenderer = new SSRRenderer();
        
        $static = $ssrRenderer->renderStatic($carousel);
        
        $this->assertStringContainsString('<style', $static);
        $this->assertStringContainsString('<div class="carousel-container"', $static);
        $this->assertStringNotContainsString('<script', $static);
    }

    public function testSSRRendererHydrateWithCarousel(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $ssrRenderer = new SSRRenderer();
        
        $static = $ssrRenderer->renderStatic($carousel);
        $hydrated = $ssrRenderer->hydrateWithCarousel($static, $carousel);
        
        $this->assertStringContainsString('<script', $hydrated);
        $this->assertStringContainsString('window.CarouselAPI', $hydrated);
    }

    public function testStaticRenderingWorksWithAllTypes(): void
    {
        $types = [
            Carousel::TYPE_IMAGE,
            Carousel::TYPE_CARD,
            Carousel::TYPE_TESTIMONIAL,
            Carousel::TYPE_GALLERY,
        ];
        
        foreach ($types as $type) {
            $carousel = new Carousel('test-' . uniqid(), $type, [
                'items' => [['image' => 'test.jpg']],
            ]);
            $carousel->addItem(['image' => 'test.jpg']);
            
            $static = $carousel->renderStatic();
            
            $this->assertStringContainsString('<div class="carousel-container"', $static, "Failed for type: {$type}");
            $this->assertStringNotContainsString('<script', $static, "Failed for type: {$type}");
        }
    }

    public function testStaticRenderingCanBeCached(): void
    {
        $id1 = 'test-' . uniqid();
        $id2 = 'test-' . uniqid();
        
        $carousel1 = Carousel::image($id1, ['image1.jpg']);
        $carousel2 = Carousel::image($id2, ['image1.jpg']);
        
        $static1 = $carousel1->renderStatic();
        $static2 = $carousel2->renderStatic();
        
        // Both should contain CSS and HTML (structure should be similar)
        $this->assertStringContainsString('<style', $static1);
        $this->assertStringContainsString('<div class="carousel-container"', $static1);
        $this->assertStringContainsString('<style', $static2);
        $this->assertStringContainsString('<div class="carousel-container"', $static2);
        
        // Both should NOT contain JavaScript
        $this->assertStringNotContainsString('<script', $static1);
        $this->assertStringNotContainsString('<script', $static2);
    }

    public function testHydrationIsIdempotent(): void
    {
        $id1 = 'test-' . uniqid();
        $id2 = 'test-' . uniqid();
        
        $carousel1 = Carousel::image($id1, ['image1.jpg']);
        $carousel2 = Carousel::image($id2, ['image1.jpg']);
        
        $static1 = $carousel1->renderStatic();
        $static2 = $carousel2->renderStatic();
        
        $hydrated1 = $carousel1->hydrate($static1);
        $hydrated2 = $carousel2->hydrate($static2);
        
        // Both should contain JavaScript
        $this->assertStringContainsString('<script', $hydrated1);
        $this->assertStringContainsString('<script', $hydrated2);
        
        // Both should preserve static content
        $this->assertStringContainsString('<style', $hydrated1);
        $this->assertStringContainsString('<div class="carousel-container"', $hydrated1);
        $this->assertStringContainsString('<style', $hydrated2);
        $this->assertStringContainsString('<div class="carousel-container"', $hydrated2);
    }
}


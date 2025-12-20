<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\RenderCacheService;
use PHPUnit\Framework\TestCase;

class VirtualizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RenderCacheService::clear();
    }

    public function testVirtualizationDisabledByDefault(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $js = $carousel->renderJs();
        
        // Virtualization should be disabled by default
        $this->assertStringContainsString('const virtualizationEnabled = false', $js);
    }

    public function testVirtualizationCanBeEnabled(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'virtualization' => true,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $js = $carousel->renderJs();
        
        $this->assertStringContainsString('const virtualizationEnabled = true', $js);
    }

    public function testVirtualizationAutoEnabledAboveThreshold(): void
    {
        // Create carousel with 60 items (above default threshold of 50)
        $items = [];
        for ($i = 0; $i < 60; $i++) {
            $items[] = ['image' => "image{$i}.jpg"];
        }
        
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'items' => $items,
        ]);
        foreach ($items as $item) {
            $carousel->addItem($item);
        }
        
        $js = $carousel->renderJs();
        
        // Should contain virtualization logic (even if virtualization=false, threshold triggers it)
        $this->assertStringContainsString('const shouldVirtualize = virtualizationEnabled || slides.length >= virtualizationThreshold', $js);
        $this->assertStringContainsString('const virtualizationThreshold = 50', $js);
    }

    public function testVirtualizationBufferIsConfigurable(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'virtualization' => true,
            'virtualizationBuffer' => 5,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $js = $carousel->renderJs();
        
        $this->assertStringContainsString('const virtualizationBuffer = 5', $js);
    }

    public function testVirtualizationThresholdIsConfigurable(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'virtualizationThreshold' => 30,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $js = $carousel->renderJs();
        
        $this->assertStringContainsString('const virtualizationThreshold = 30', $js);
    }

    public function testVirtualizationLogicInUpdateCarousel(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'virtualization' => true,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $js = $carousel->renderJs();
        
        // Should contain virtualization logic in updateCarousel
        $this->assertStringContainsString('if (shouldVirtualize)', $js);
        $this->assertStringContainsString('const distance = Math.abs(index - currentIndex)', $js);
        $this->assertStringContainsString('slide.style.display = \'none\'', $js);
        $this->assertStringContainsString('slide.setAttribute(\'data-virtualized\', \'true\')', $js);
    }

    public function testVirtualizationShowsSlidesWithinBuffer(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'virtualization' => true,
            'virtualizationBuffer' => 2,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $js = $carousel->renderJs();
        
        // Should show slides within buffer distance
        $this->assertStringContainsString('if (distance > virtualizationBuffer)', $js);
        $this->assertStringContainsString('slide.style.display = \'\'', $js);
        $this->assertStringContainsString('slide.removeAttribute(\'data-virtualized\')', $js);
    }

    public function testVirtualizationDisabledShowsAllSlides(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'virtualization' => false,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $js = $carousel->renderJs();
        
        // When disabled, should ensure all slides are visible
        $this->assertStringContainsString('} else {', $js);
        $this->assertStringContainsString('// Ensure all slides are visible when virtualization is disabled', $js);
    }

    public function testVirtualizationWorksWithAllCarouselTypes(): void
    {
        $types = [
            Carousel::TYPE_IMAGE,
            Carousel::TYPE_CARD,
            Carousel::TYPE_TESTIMONIAL,
            Carousel::TYPE_GALLERY,
        ];
        
        foreach ($types as $type) {
            $carousel = new Carousel('test-' . uniqid(), $type, [
                'virtualization' => true,
                'items' => [['image' => 'test.jpg']],
            ]);
            $carousel->addItem(['image' => 'test.jpg']);
            
            $js = $carousel->renderJs();
            
            $this->assertStringContainsString('const virtualizationEnabled = true', $js, "Failed for type: {$type}");
        }
    }
}


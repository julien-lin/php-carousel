<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Analytics\FileAnalytics;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\JsRenderer;
use JulienLinard\Carousel\Renderer\RenderCacheService;
use PHPUnit\Framework\TestCase;

class AnalyticsIntegrationTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        RenderCacheService::clear();
        $this->tempDir = sys_get_temp_dir() . '/php-carousel-analytics-' . uniqid();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        RenderCacheService::clear();
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->tempDir);
        }
    }

    public function testCarouselWithAnalyticsEnabled(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        $carousel = new Carousel('test-analytics', Carousel::TYPE_IMAGE, [
            'analytics' => true,
            'analyticsProvider' => $analytics,
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $this->assertNotNull($carousel->getAnalyticsProvider());
        $this->assertInstanceOf(FileAnalytics::class, $carousel->getAnalyticsProvider());
    }

    public function testJsRendererIncludesAnalyticsCode(): void
    {
        $carousel = new Carousel('test-js-analytics', Carousel::TYPE_IMAGE, [
            'analytics' => true,
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $renderer = new JsRenderer();
        $js = $renderer->render($carousel);
        
        $this->assertStringContainsString('analyticsEnabled = true', $js);
        $this->assertStringContainsString('function trackAnalytics', $js);
        $this->assertStringContainsString('trackAnalytics(\'impression\'', $js);
    }

    public function testJsRendererDoesNotIncludeAnalyticsWhenDisabled(): void
    {
        $carousel = new Carousel('test-js-no-analytics', Carousel::TYPE_IMAGE, [
            'analytics' => false,
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $renderer = new JsRenderer();
        $js = $renderer->render($carousel);
        
        $this->assertStringContainsString('analyticsEnabled = false', $js);
    }

    public function testJsRendererTracksInteractions(): void
    {
        $carousel = new Carousel('test-interactions', Carousel::TYPE_IMAGE, [
            'analytics' => true,
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $renderer = new JsRenderer();
        $js = $renderer->render($carousel);
        
        // Check for interaction tracking
        $this->assertStringContainsString('interaction_type: \'arrow_click\'', $js);
        $this->assertStringContainsString('interaction_type: \'dot_click\'', $js);
        $this->assertStringContainsString('interaction_type: \'swipe\'', $js);
        // Note: keyboard navigation calls prevSlide/nextSlide which don't directly track keyboard events
        // The tracking is done via arrow clicks, so we just verify trackAnalytics exists
        $this->assertStringContainsString('function trackAnalytics', $js);
    }

    public function testSetAnalyticsProvider(): void
    {
        $carousel = new Carousel('test-set-provider');
        $analytics = new FileAnalytics($this->tempDir);
        
        $carousel->setAnalyticsProvider($analytics);
        
        $this->assertSame($analytics, $carousel->getAnalyticsProvider());
    }
}


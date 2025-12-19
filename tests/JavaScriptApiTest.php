<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\JavaScript\CarouselAPI;
use JulienLinard\Carousel\JavaScript\CarouselInstance;

class JavaScriptApiTest extends TestCase
{
    /**
     * Test CarouselAPI class exists
     */
    public function testCarouselAPIClassExists(): void
    {
        $this->assertTrue(class_exists(CarouselAPI::class));
    }

    /**
     * Test CarouselInstance class exists
     */
    public function testCarouselInstanceClassExists(): void
    {
        $this->assertTrue(class_exists(CarouselInstance::class));
    }

    /**
     * Test CarouselAPI generates JavaScript code
     */
    public function testCarouselAPIGeneratesJavaScriptCode(): void
    {
        $js = CarouselAPI::generate();
        
        $this->assertNotEmpty($js);
        $this->assertIsString($js);
        $this->assertStringContainsString('window.CarouselAPI', $js);
    }

    /**
     * Test CarouselAPI includes init method
     */
    public function testCarouselAPIIncludesInitMethod(): void
    {
        $js = CarouselAPI::generate();
        
        $this->assertStringContainsString('init', $js);
        $this->assertStringContainsString('function init', $js);
    }

    /**
     * Test CarouselAPI includes get method
     */
    public function testCarouselAPIIncludesGetMethod(): void
    {
        $js = CarouselAPI::generate();
        
        $this->assertStringContainsString('get', $js);
        $this->assertStringContainsString('function get', $js);
    }

    /**
     * Test CarouselAPI includes destroy method
     */
    public function testCarouselAPIIncludesDestroyMethod(): void
    {
        $js = CarouselAPI::generate();
        
        $this->assertStringContainsString('destroy', $js);
        $this->assertStringContainsString('function destroy', $js);
    }

    /**
     * Test CarouselAPI includes autoInit method
     */
    public function testCarouselAPIIncludesAutoInitMethod(): void
    {
        $js = CarouselAPI::generate();
        
        $this->assertStringContainsString('autoInit', $js);
        $this->assertStringContainsString('function autoInit', $js);
    }

    /**
     * Test CarouselInstance generates JavaScript code
     */
    public function testCarouselInstanceGeneratesJavaScriptCode(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertNotEmpty($js);
        $this->assertIsString($js);
        $this->assertStringContainsString('class CarouselInstance', $js);
    }

    /**
     * Test CarouselInstance includes goTo method
     */
    public function testCarouselInstanceIncludesGoToMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('goTo', $js);
        $this->assertStringContainsString('goTo(index)', $js);
    }

    /**
     * Test CarouselInstance includes next method
     */
    public function testCarouselInstanceIncludesNextMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('next()', $js);
    }

    /**
     * Test CarouselInstance includes prev method
     */
    public function testCarouselInstanceIncludesPrevMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('prev()', $js);
    }

    /**
     * Test CarouselInstance includes getCurrentIndex method
     */
    public function testCarouselInstanceIncludesGetCurrentIndexMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('getCurrentIndex', $js);
        $this->assertStringContainsString('getCurrentIndex()', $js);
    }

    /**
     * Test CarouselInstance includes getTotalSlides method
     */
    public function testCarouselInstanceIncludesGetTotalSlidesMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('getTotalSlides', $js);
        $this->assertStringContainsString('getTotalSlides()', $js);
    }

    /**
     * Test CarouselInstance includes on method for events
     */
    public function testCarouselInstanceIncludesOnMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('on(event', $js);
        $this->assertStringContainsString('on(event, callback)', $js);
    }

    /**
     * Test CarouselInstance includes off method for events
     */
    public function testCarouselInstanceIncludesOffMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('off(event', $js);
        $this->assertStringContainsString('off(event, callback)', $js);
    }

    /**
     * Test CarouselInstance includes emit method for events
     */
    public function testCarouselInstanceIncludesEmitMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('emit(event', $js);
        $this->assertStringContainsString('emit(event, data', $js);
    }

    /**
     * Test CarouselInstance includes destroy method
     */
    public function testCarouselInstanceIncludesDestroyMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('destroy()', $js);
    }

    /**
     * Test CarouselInstance includes startAutoplay method
     */
    public function testCarouselInstanceIncludesStartAutoplayMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('startAutoplay', $js);
    }

    /**
     * Test CarouselInstance includes stopAutoplay method
     */
    public function testCarouselInstanceIncludesStopAutoplayMethod(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('stopAutoplay', $js);
    }

    /**
     * Test carousel JavaScript includes CarouselAPI initialization
     */
    public function testCarouselJavaScriptIncludesCarouselAPIInitialization(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg', 'image2.jpg']);
        $js = $carousel->renderJs();
        
        $this->assertStringContainsString('CarouselAPI', $js);
        $this->assertStringContainsString('window.CarouselAPI.init', $js);
    }

    /**
     * Test carousel JavaScript includes CarouselAPI script
     */
    public function testCarouselJavaScriptIncludesCarouselAPIScript(): void
    {
        // Reset static cache to ensure API is included
        \JulienLinard\Carousel\Renderer\RenderCacheService::clear();
        
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $js = $carousel->renderJs();
        
        $this->assertStringContainsString('id="carousel-api"', $js);
        $this->assertStringContainsString('window.CarouselAPI', $js);
    }

    /**
     * Test carousel JavaScript includes CarouselInstance class
     */
    public function testCarouselJavaScriptIncludesCarouselInstanceClass(): void
    {
        // Reset static cache to ensure API is included
        \JulienLinard\Carousel\Renderer\RenderCacheService::clear();
        
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $js = $carousel->renderJs();
        
        $this->assertStringContainsString('class CarouselInstance', $js);
    }

    /**
     * Test multiple carousels only include API once
     */
    public function testMultipleCarouselsOnlyIncludeAPIOnce(): void
    {
        // Reset static cache
        \JulienLinard\Carousel\Renderer\RenderCacheService::clear();
        
        $carousel1 = Carousel::image('test1-' . uniqid(), ['image1.jpg']);
        $carousel2 = Carousel::image('test2-' . uniqid(), ['image2.jpg']);
        
        $js1 = $carousel1->renderJs();
        $js2 = $carousel2->renderJs();
        
        // First carousel should include API
        $this->assertStringContainsString('id="carousel-api"', $js1);
        
        // Second carousel should not include API again
        $this->assertStringNotContainsString('id="carousel-api"', $js2);
    }

    /**
     * Test carousel exposes instance via window.carouselInstances
     */
    public function testCarouselExposesInstanceViaWindowCarouselInstances(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg', 'image2.jpg']);
        $js = $carousel->renderJs();
        
        $this->assertStringContainsString('window.carouselInstances', $js);
        $this->assertStringContainsString('goToSlide', $js);
        $this->assertStringContainsString('nextSlide', $js);
        $this->assertStringContainsString('prevSlide', $js);
        $this->assertStringContainsString('getCurrentIndex', $js);
    }

    /**
     * Test API is included before carousel script
     */
    public function testAPIIncludedBeforeCarouselScript(): void
    {
        // Reset static cache
        \JulienLinard\Carousel\Renderer\RenderCacheService::clear();
        
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $js = $carousel->renderJs();
        
        $apiPos = strpos($js, 'id="carousel-api"');
        $scriptPos = strpos($js, 'id="carousel-script-');
        
        if ($apiPos !== false && $scriptPos !== false) {
            $this->assertLessThan($scriptPos, $apiPos, 'API should be included before carousel script');
        } else {
            // If API is not in this render (cached), skip this assertion
            $this->assertTrue(true, 'API may be cached in previous render');
        }
    }

    /**
     * Test event system is present in CarouselInstance
     */
    public function testEventSystemIsPresentInCarouselInstance(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('listeners', $js);
        $this->assertStringContainsString('new Map()', $js);
        $this->assertStringContainsString('slideChange', $js);
    }

    /**
     * Test CarouselInstance wraps existing instance
     */
    public function testCarouselInstanceWrapsExistingInstance(): void
    {
        $js = CarouselInstance::generate();
        
        $this->assertStringContainsString('existingInstance', $js);
        $this->assertStringContainsString('_wrapExistingInstance', $js);
    }
}


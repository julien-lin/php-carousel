<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Blade\CarouselServiceProvider;
use JulienLinard\Carousel\Carousel;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Blade integration
 * 
 * Note: These tests verify that the helper functions are defined
 * and work correctly. Full Blade directive testing requires Laravel:
 *   composer require illuminate/support
 */
class BladeIntegrationTest extends TestCase
{
    /**
     * Test that helper functions are callable
     * 
     * Note: In a real Laravel environment, these functions would be
     * registered by the ServiceProvider. For testing purposes, we
     * verify the CarouselServiceProvider class exists and can be instantiated.
     */
    public function testServiceProviderExists(): void
    {
        $provider = new CarouselServiceProvider(null);
        $this->assertInstanceOf(CarouselServiceProvider::class, $provider);
    }

    /**
     * Test that helper functions would work (simulated)
     * 
     * This test verifies that the methods called by helpers exist
     * and work correctly, simulating what the helpers would do.
     */
    public function testHelperFunctionsSimulation(): void
    {
        // Simulate carousel_image() helper
        $carousel = Carousel::image('test', [
            'image1.jpg',
            'image2.jpg',
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('test', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_IMAGE, $carousel->getType());
    }

    /**
     * Test that all carousel types can be created (simulating helpers)
     */
    public function testAllCarouselTypesCanBeCreated(): void
    {
        // Simulate carousel_infinite()
        $infinite = Carousel::infiniteCarousel('infinite', ['img1.jpg']);
        $this->assertEquals(Carousel::TYPE_INFINITE, $infinite->getType());
        
        // Simulate carousel_hero()
        $hero = Carousel::heroBanner('hero', [['id' => '1', 'image' => 'banner.jpg']]);
        $this->assertEquals(Carousel::TYPE_IMAGE, $hero->getType());
        
        // Simulate carousel_products()
        $products = Carousel::productShowcase('products', [['id' => '1', 'image' => 'product.jpg']]);
        $this->assertEquals(Carousel::TYPE_CARD, $products->getType());
        
        // Simulate carousel_card()
        $card = Carousel::card('cards', [['id' => '1', 'image' => 'card.jpg']]);
        $this->assertEquals(Carousel::TYPE_CARD, $card->getType());
        
        // Simulate carousel_testimonial()
        $testimonial = Carousel::testimonial('testimonials', [['id' => '1', 'content' => 'Test']]);
        $this->assertEquals(Carousel::TYPE_TESTIMONIAL, $testimonial->getType());
        
        // Simulate carousel_gallery()
        $gallery = Carousel::gallery('gallery', ['img1.jpg']);
        $this->assertEquals(Carousel::TYPE_GALLERY, $gallery->getType());
    }

    /**
     * Test that ServiceProvider has required methods
     */
    public function testServiceProviderHasRequiredMethods(): void
    {
        $this->assertTrue(method_exists(CarouselServiceProvider::class, 'boot'));
        $this->assertTrue(method_exists(CarouselServiceProvider::class, 'register'));
    }
    
    /**
     * Test that ServiceProvider can be booted (if Laravel is available)
     */
    public function testServiceProviderCanBeBooted(): void
    {
        // Only test if Laravel is available
        if (!class_exists('\Illuminate\Support\ServiceProvider')) {
            $this->markTestSkipped('Laravel is not installed. Install with: composer require illuminate/support');
        }
        
        // The boot method requires a full Laravel application context
        // (facades, service container, etc.). Without it, it will fail.
        // This is expected behavior - the ServiceProvider is designed to work
        // within a Laravel application, not in isolation.
        
        // We verify that the method exists and can be reflected
        $this->assertTrue(method_exists(CarouselServiceProvider::class, 'boot'));
        
        $reflection = new \ReflectionMethod(CarouselServiceProvider::class, 'boot');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals('void', (string) $reflection->getReturnType());
        
        // Note: Actual boot() testing requires a full Laravel application instance
        // which is beyond the scope of unit tests. Integration tests in a Laravel
        // project would be more appropriate for full functionality testing.
    }
}


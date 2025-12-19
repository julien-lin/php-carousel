<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Twig\CarouselExtension;
use JulienLinard\Carousel\Twig\CarouselRuntime;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Twig integration
 * 
 * Note: These tests verify the extension classes without requiring Twig to be installed.
 * Full integration testing requires Twig to be installed via composer:
 *   composer require twig/twig
 */
class TwigIntegrationTest extends TestCase
{
    /**
     * Test that CarouselExtension can be instantiated
     */
    public function testExtensionCanBeInstantiated(): void
    {
        $extension = new CarouselExtension();
        $this->assertInstanceOf(CarouselExtension::class, $extension);
    }

    /**
     * Test that Twig functions are defined
     */
    public function testTwigFunctionsAreDefined(): void
    {
        $extension = new CarouselExtension();
        $functions = $extension->getFunctions();
        
        $this->assertIsArray($functions);
        $this->assertGreaterThan(0, count($functions));
        
        // Verify function names if Twig is available
        if (class_exists('\Twig\TwigFunction')) {
            $functionNames = array_map(fn($fn) => $fn->getName(), $functions);
            
            $this->assertContains('carousel', $functionNames);
            $this->assertContains('carousel_image', $functionNames);
            $this->assertContains('carousel_card', $functionNames);
            $this->assertContains('carousel_infinite', $functionNames);
            $this->assertContains('carousel_hero', $functionNames);
            $this->assertContains('carousel_products', $functionNames);
            $this->assertContains('carousel_testimonial', $functionNames);
            $this->assertContains('carousel_gallery', $functionNames);
        }
    }

    /**
     * Test CarouselRuntime::createImageCarousel
     */
    public function testCreateImageCarousel(): void
    {
        $carousel = CarouselRuntime::createImageCarousel('test', [
            'image1.jpg',
            'image2.jpg',
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('test', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_IMAGE, $carousel->getType());
        $this->assertCount(2, $carousel->getItems());
    }

    /**
     * Test CarouselRuntime::createInfiniteCarousel
     */
    public function testCreateInfiniteCarousel(): void
    {
        $carousel = CarouselRuntime::createInfiniteCarousel('infinite', [
            'img1.jpg',
            'img2.jpg',
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('infinite', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_INFINITE, $carousel->getType());
    }

    /**
     * Test CarouselRuntime::createHeroBanner
     */
    public function testCreateHeroBanner(): void
    {
        $carousel = CarouselRuntime::createHeroBanner('hero', [
            [
                'id' => 'banner1',
                'title' => 'Banner 1',
                'image' => 'banner1.jpg',
            ],
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('hero', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_IMAGE, $carousel->getType());
    }

    /**
     * Test CarouselRuntime::createProductShowcase
     */
    public function testCreateProductShowcase(): void
    {
        $carousel = CarouselRuntime::createProductShowcase('products', [
            [
                'id' => '1',
                'title' => 'Product 1',
                'image' => 'product1.jpg',
            ],
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('products', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_CARD, $carousel->getType());
    }

    /**
     * Test CarouselRuntime::createCardCarousel
     */
    public function testCreateCardCarousel(): void
    {
        $carousel = CarouselRuntime::createCardCarousel('cards', [
            [
                'id' => '1',
                'title' => 'Card 1',
                'content' => 'Content 1',
                'image' => 'card1.jpg',
            ],
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('cards', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_CARD, $carousel->getType());
    }

    /**
     * Test CarouselRuntime::createTestimonialCarousel
     */
    public function testCreateTestimonialCarousel(): void
    {
        $carousel = CarouselRuntime::createTestimonialCarousel('testimonials', [
            [
                'id' => '1',
                'title' => 'John Doe',
                'content' => 'Great product!',
                'image' => 'avatar1.jpg',
            ],
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('testimonials', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_TESTIMONIAL, $carousel->getType());
    }

    /**
     * Test CarouselRuntime::createGalleryCarousel
     */
    public function testCreateGalleryCarousel(): void
    {
        $carousel = CarouselRuntime::createGalleryCarousel('gallery', [
            'img1.jpg',
            'img2.jpg',
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('gallery', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_GALLERY, $carousel->getType());
    }

    /**
     * Test CarouselRuntime::createCarousel (generic)
     */
    public function testCreateCarousel(): void
    {
        $carousel = CarouselRuntime::createCarousel('generic', Carousel::TYPE_IMAGE, [
            [
                'id' => '1',
                'image' => 'img1.jpg',
            ],
        ]);
        
        $this->assertInstanceOf(Carousel::class, $carousel);
        $this->assertEquals('generic', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_IMAGE, $carousel->getType());
    }
}


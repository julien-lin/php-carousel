<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\RenderContext;
use JulienLinard\Carousel\Translator\ArrayTranslator;

class RenderContextTest extends TestCase
{
    /**
     * Test all getters return correct values
     */
    public function testAllGettersReturnCorrectValues(): void
    {
        $carousel = Carousel::image('test-id', ['image1.jpg']);
        $translator = new ArrayTranslator([], 'en');
        
        $context = new RenderContext($carousel, $translator);
        
        $this->assertSame($carousel, $context->getCarousel());
        $this->assertSame($translator, $context->getTranslator());
        $this->assertEquals('test-id', $context->getId());
        $this->assertEquals(Carousel::TYPE_IMAGE, $context->getType());
        $this->assertIsArray($context->getItems());
        $this->assertIsArray($context->getOptions());
    }

    /**
     * Test getOption with default value
     */
    public function testGetOptionWithDefaultValue(): void
    {
        $carousel = Carousel::image('test-id', ['image1.jpg']);
        $translator = new ArrayTranslator([], 'en');
        
        $context = new RenderContext($carousel, $translator);
        
        $this->assertEquals('default-value', $context->getOption('non-existent', 'default-value'));
    }

    /**
     * Test getOption without default returns null
     */
    public function testGetOptionWithoutDefaultReturnsNull(): void
    {
        $carousel = Carousel::image('test-id', ['image1.jpg']);
        $translator = new ArrayTranslator([], 'en');
        
        $context = new RenderContext($carousel, $translator);
        
        $this->assertNull($context->getOption('non-existent'));
    }

    /**
     * Test getOption returns actual option value
     */
    public function testGetOptionReturnsActualOptionValue(): void
    {
        $carousel = Carousel::image('test-id', ['image1.jpg']);
        $carousel->setOptions(['autoplay' => false, 'gap' => 20]);
        $translator = new ArrayTranslator([], 'en');
        
        $context = new RenderContext($carousel, $translator);
        
        $this->assertFalse($context->getOption('autoplay'));
        $this->assertEquals(20, $context->getOption('gap'));
    }

    /**
     * Test getItems returns carousel items
     */
    public function testGetItemsReturnsCarouselItems(): void
    {
        $carousel = Carousel::image('test-id', ['image1.jpg', 'image2.jpg']);
        $translator = new ArrayTranslator([], 'en');
        
        $context = new RenderContext($carousel, $translator);
        
        $items = $context->getItems();
        $this->assertCount(2, $items);
    }

    /**
     * Test getOptions returns all options
     */
    public function testGetOptionsReturnsAllOptions(): void
    {
        $carousel = Carousel::image('test-id', ['image1.jpg']);
        $carousel->setOptions(['autoplay' => false, 'gap' => 20, 'loop' => true]);
        $translator = new ArrayTranslator([], 'en');
        
        $context = new RenderContext($carousel, $translator);
        
        $options = $context->getOptions();
        $this->assertIsArray($options);
        $this->assertFalse($options['autoplay']);
        $this->assertEquals(20, $options['gap']);
        $this->assertTrue($options['loop']);
    }

    /**
     * Test getType returns correct type
     */
    public function testGetTypeReturnsCorrectType(): void
    {
        $types = [
            Carousel::TYPE_IMAGE,
            Carousel::TYPE_CARD,
            Carousel::TYPE_TESTIMONIAL,
            Carousel::TYPE_GALLERY,
            Carousel::TYPE_INFINITE,
        ];
        
        foreach ($types as $type) {
            $carousel = new Carousel('test-id', $type);
            $translator = new ArrayTranslator([], 'en');
            $context = new RenderContext($carousel, $translator);
            
            $this->assertEquals($type, $context->getType());
        }
    }

    /**
     * Test getId returns correct ID
     */
    public function testGetIdReturnsCorrectId(): void
    {
        $carousel = Carousel::image('my-carousel-id', ['image1.jpg']);
        $translator = new ArrayTranslator([], 'en');
        
        $context = new RenderContext($carousel, $translator);
        
        $this->assertEquals('my-carousel-id', $context->getId());
    }
}


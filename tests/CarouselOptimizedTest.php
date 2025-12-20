<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;
use JulienLinard\Carousel\Exception\EmptyCarouselException;

/**
 * Optimized Carousel Tests with Data Providers
 * 
 * This file demonstrates best practices for testing the Carousel class:
 * - Use of data providers to reduce code duplication
 * - Comprehensive coverage of carousel factory methods
 * - Well-organized test structure
 * - Clear naming and documentation
 */
class CarouselOptimizedTest extends TestCase
{
    /**
     * Data provider for carousel factory methods
     * 
     * @return array<string, array<int, mixed>>
     */
    public static function carouselFactoryMethodsProvider(): array
    {
        return [
            'image carousel' => [
                'method' => 'image',
                'params' => ['test-image', ['image1.jpg', 'image2.jpg']],
                'expectedId' => 'test-image',
                'expectedType' => Carousel::TYPE_IMAGE,
                'expectedCount' => 2,
            ],
            'card carousel' => [
                'method' => 'card',
                'params' => ['test-card', [
                    ['id' => '1', 'title' => 'Card 1', 'image' => 'card1.jpg'],
                ]],
                'expectedId' => 'test-card',
                'expectedType' => Carousel::TYPE_CARD,
                'expectedCount' => 1,
            ],
            'infinite carousel' => [
                'method' => 'infiniteCarousel',
                'params' => ['test-infinite', ['img1.jpg', 'img2.jpg']],
                'expectedId' => 'test-infinite',
                'expectedType' => Carousel::TYPE_INFINITE,
                'expectedCount' => 2,
            ],
        ];
    }

    /**
     * Test carousel factory methods create correct types
     * 
     * @param string $method
     * @param array $params
     * @param string $expectedId
     * @param string $expectedType
     * @param int $expectedCount
     */
    #[DataProvider('carouselFactoryMethodsProvider')]
    public function testCarouselFactoryMethods(
        string $method,
        array $params,
        string $expectedId,
        string $expectedType,
        int $expectedCount
    ): void {
        $carousel = Carousel::$method(...$params);

        $this->assertEquals($expectedId, $carousel->getId());
        $this->assertEquals($expectedType, $carousel->getType());
        $this->assertCount($expectedCount, $carousel->getItems());
    }

    /**
     * Data provider for option validation tests
     * 
     * @return array<string, array<int, mixed>>
     */
    public static function optionValidationProvider(): array
    {
        return [
            'valid autoplayInterval' => [
                'options' => ['autoplayInterval' => 2000],
                'shouldPass' => true,
            ],
            'autoplayInterval too low' => [
                'options' => ['autoplayInterval' => 500],
                'shouldPass' => false,
                'exceptionMessage' => 'autoplayInterval must be between 1000 and 60000',
            ],
            'autoplayInterval too high' => [
                'options' => ['autoplayInterval' => 70000],
                'shouldPass' => false,
                'exceptionMessage' => 'autoplayInterval must be between 1000 and 60000',
            ],
        ];
    }

    /**
     * Test option validation
     * 
     * @param array $options
     * @param bool $shouldPass
     * @param string|null $exceptionMessage
     */
    #[DataProvider('optionValidationProvider')]
    public function testOptionValidation(
        array $options,
        bool $shouldPass,
        ?string $exceptionMessage = null
    ): void {
        if (!$shouldPass && $exceptionMessage) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage($exceptionMessage);
        }

        new Carousel('test', Carousel::TYPE_IMAGE, $options);

        if ($shouldPass) {
            $this->assertTrue(true); // Explicit assertion for success case
        }
    }

    /**
     * Test adding items to carousel
     */
    public function testAddItemAsCarouselItem(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);
        $item = new CarouselItem('item1', 'Title', 'Content');

        $carousel->addItem($item);

        $this->assertCount(1, $carousel->getItems());
        $this->assertSame($item, $carousel->getItems()[0]);
    }

    /**
     * Test adding items from array
     */
    public function testAddItemFromArray(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);

        $carousel->addItem([
            'id' => 'item1',
            'title' => 'Title 1',
        ]);

        $this->assertCount(1, $carousel->getItems());
        $this->assertInstanceOf(CarouselItem::class, $carousel->getItems()[0]);
    }

    /**
     * Test setting options on carousel
     */
    public function testSetOptions(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);

        $carousel->setOptions([
            'autoplay' => false,
            'gap' => 20,
        ]);

        $this->assertFalse($carousel->getOption('autoplay'));
        $this->assertEquals(20, $carousel->getOption('gap'));
    }

    /**
     * Test rendering empty carousel throws exception
     */
    public function testRenderEmptyCarouselThrowsException(): void
    {
        $this->expectException(EmptyCarouselException::class);

        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);
        $carousel->render();
    }

    /**
     * Test carousel rendering includes expected markup
     */
    public function testRenderCarouselHasExpectedMarkup(): void
    {
        $uniqueId = 'test-render-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg']);
        $output = $carousel->render();

        $this->assertStringContainsString('carousel-container', $output);
        $this->assertStringContainsString('carousel-' . $uniqueId, $output);
        $this->assertStringContainsString('<style', $output);
        $this->assertStringContainsString('<script', $output);
    }

    /**
     * Data provider for carousel item conversion
     * 
     * @return array<string, array<int, mixed>>
     */
    public static function carouselItemConversionProvider(): array
    {
        return [
            'complete item' => [
                'data' => [
                    'id' => 'test',
                    'title' => 'Test Title',
                    'content' => 'Test Content',
                    'image' => 'test.jpg',
                    'link' => '/test',
                ],
                'expectedId' => 'test',
                'expectedTitle' => 'Test Title',
                'expectedContent' => 'Test Content',
            ],
            'minimal item' => [
                'data' => [
                    'id' => 'minimal',
                    'title' => 'Minimal',
                ],
                'expectedId' => 'minimal',
                'expectedTitle' => 'Minimal',
                'expectedContent' => '',
            ],
        ];
    }

    /**
     * Test carousel item from array conversion
     * 
     * @param array $data
     * @param string $expectedId
     * @param string $expectedTitle
     * @param string $expectedContent
     */
    #[DataProvider('carouselItemConversionProvider')]
    public function testCarouselItemFromArray(
        array $data,
        string $expectedId,
        string $expectedTitle,
        string $expectedContent
    ): void {
        $item = CarouselItem::fromArray($data);

        $this->assertEquals($expectedId, $item->id);
        $this->assertEquals($expectedTitle, $item->title);
        $this->assertEquals($expectedContent, $item->content);
    }

    /**
     * Test carousel item to array conversion
     */
    public function testCarouselItemToArray(): void
    {
        $item = new CarouselItem('test', 'Title', 'Content', 'image.jpg', '/link');
        $array = $item->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('test', $array['id']);
        $this->assertEquals('Title', $array['title']);
        $this->assertEquals('Content', $array['content']);
    }

    /**
     * Data provider for preset carousel types
     * 
     * @return array<string, array<int, mixed>>
     */
    public static function presetCarouselTypesProvider(): array
    {
        return [
            'hero banner' => [
                'method' => 'heroBanner',
                'id' => 'hero',
                'data' => [
                    ['id' => 'banner1', 'title' => 'Banner 1', 'image' => 'banner1.jpg'],
                ],
                'expectedType' => Carousel::TYPE_IMAGE,
                'optionChecks' => [
                    'height' => '600px',
                    'transition' => 'fade',
                    'showDots' => true,
                ],
            ],
            'product showcase' => [
                'method' => 'productShowcase',
                'id' => 'products',
                'data' => [
                    ['id' => '1', 'title' => 'Product 1', 'image' => 'product1.jpg'],
                ],
                'expectedType' => Carousel::TYPE_CARD,
                'optionChecks' => [
                    'itemsPerSlide' => 4,
                    'autoplay' => false,
                    'gap' => 20,
                ],
            ],
            'testimonial slider' => [
                'method' => 'testimonialSlider',
                'id' => 'testimonials',
                'data' => [
                    ['id' => '1', 'title' => 'John Doe', 'content' => 'Great product!'],
                ],
                'expectedType' => Carousel::TYPE_TESTIMONIAL,
                'optionChecks' => [
                    'transition' => 'fade',
                    'showDots' => true,
                    'showArrows' => false,
                    'autoplayInterval' => 6000,
                ],
            ],
        ];
    }

    /**
     * Test preset carousel types have correct configuration
     * 
     * @param string $method
     * @param string $id
     * @param array $data
     * @param string $expectedType
     * @param array $optionChecks
     */
    #[DataProvider('presetCarouselTypesProvider')]
    public function testPresetCarouselTypes(
        string $method,
        string $id,
        array $data,
        string $expectedType,
        array $optionChecks
    ): void {
        $carousel = Carousel::$method($id, $data);

        $this->assertEquals($id, $carousel->getId());
        $this->assertEquals($expectedType, $carousel->getType());

        foreach ($optionChecks as $optionKey => $expectedValue) {
            $this->assertEquals(
                $expectedValue,
                $carousel->getOption($optionKey),
                "Option $optionKey should be $expectedValue for $method"
            );
        }
    }
}

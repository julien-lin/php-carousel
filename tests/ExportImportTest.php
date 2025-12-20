<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;
use PHPUnit\Framework\TestCase;

class ExportImportTest extends TestCase
{
    public function testExportConfigReturnsValidArray(): void
    {
        $carousel = new Carousel('test-carousel', Carousel::TYPE_IMAGE, [
            'autoplay' => false,
            'autoplayInterval' => 3000,
        ]);
        $carousel->addItem(['id' => '1', 'image' => 'image1.jpg']);
        $carousel->addItem(['id' => '2', 'image' => 'image2.jpg']);
        
        $config = $carousel->exportConfig();
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('id', $config);
        $this->assertArrayHasKey('type', $config);
        $this->assertArrayHasKey('items', $config);
        $this->assertArrayHasKey('options', $config);
        
        $this->assertEquals('test-carousel', $config['id']);
        $this->assertEquals(Carousel::TYPE_IMAGE, $config['type']);
        $this->assertCount(2, $config['items']);
        $this->assertIsArray($config['options']);
    }

    public function testExportConfigIncludesAllOptions(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_CARD, [
            'autoplay' => true,
            'autoplayInterval' => 5000,
            'loop' => false,
            'itemsPerSlide' => 3,
            'theme' => 'dark',
        ]);
        
        $config = $carousel->exportConfig();
        
        $this->assertArrayHasKey('options', $config);
        $this->assertEquals(true, $config['options']['autoplay']);
        $this->assertEquals(5000, $config['options']['autoplayInterval']);
        $this->assertEquals(false, $config['options']['loop']);
        $this->assertEquals(3, $config['options']['itemsPerSlide']);
        $this->assertEquals('dark', $config['options']['theme']);
    }

    public function testExportConfigConvertsItemsToArray(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);
        $carousel->addItem(new CarouselItem('1', 'Title', 'Content', 'image.jpg', '/link'));
        $carousel->addItem(['id' => '2', 'image' => 'image2.jpg']);
        
        $config = $carousel->exportConfig();
        
        $this->assertIsArray($config['items']);
        $this->assertCount(2, $config['items']);
        
        // First item (CarouselItem instance)
        $this->assertIsArray($config['items'][0]);
        $this->assertEquals('1', $config['items'][0]['id']);
        $this->assertEquals('Title', $config['items'][0]['title']);
        $this->assertEquals('Content', $config['items'][0]['content']);
        $this->assertEquals('image.jpg', $config['items'][0]['image']);
        $this->assertEquals('/link', $config['items'][0]['link']);
        
        // Second item (already array)
        $this->assertIsArray($config['items'][1]);
        $this->assertEquals('2', $config['items'][1]['id']);
    }

    public function testFromConfigCreatesValidCarousel(): void
    {
        $config = [
            'id' => 'imported-carousel',
            'type' => Carousel::TYPE_CARD,
            'items' => [
                ['id' => '1', 'title' => 'Card 1', 'image' => 'card1.jpg'],
                ['id' => '2', 'title' => 'Card 2', 'image' => 'card2.jpg'],
            ],
            'options' => [
                'autoplay' => false,
                'itemsPerSlide' => 2,
            ],
        ];
        
        $carousel = Carousel::fromConfig($config);
        
        $this->assertEquals('imported-carousel', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_CARD, $carousel->getType());
        $this->assertCount(2, $carousel->getItems());
        $this->assertEquals(false, $carousel->getOption('autoplay'));
        $this->assertEquals(2, $carousel->getOption('itemsPerSlide'));
    }

    public function testFromConfigThrowsExceptionForMissingId(): void
    {
        $config = [
            'type' => Carousel::TYPE_IMAGE,
            'items' => [],
            'options' => [],
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration must contain a valid "id" field');
        
        Carousel::fromConfig($config);
    }

    public function testFromConfigThrowsExceptionForMissingType(): void
    {
        $config = [
            'id' => 'test',
            'items' => [],
            'options' => [],
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration must contain a valid "type" field');
        
        Carousel::fromConfig($config);
    }

    public function testFromConfigThrowsExceptionForInvalidType(): void
    {
        $config = [
            'id' => 'test',
            'type' => 'invalid-type',
            'items' => [],
            'options' => [],
        ];
        
        $this->expectException(\JulienLinard\Carousel\Exception\InvalidCarouselTypeException::class);
        
        Carousel::fromConfig($config);
    }

    public function testExportAndImportRoundTrip(): void
    {
        $original = new Carousel('original', Carousel::TYPE_TESTIMONIAL, [
            'autoplay' => true,
            'autoplayInterval' => 7000,
            'transition' => 'fade',
            'theme' => 'light',
        ]);
        $original->addItem(['id' => '1', 'title' => 'John', 'content' => 'Great product!']);
        $original->addItem(['id' => '2', 'title' => 'Jane', 'content' => 'Highly recommended!']);
        
        // Export
        $config = $original->exportConfig();
        
        // Import
        $imported = Carousel::fromConfig($config);
        
        // Verify they match
        $this->assertEquals($original->getId(), $imported->getId());
        $this->assertEquals($original->getType(), $imported->getType());
        $this->assertCount(count($original->getItems()), $imported->getItems());
        $this->assertEquals($original->getOption('autoplay'), $imported->getOption('autoplay'));
        $this->assertEquals($original->getOption('autoplayInterval'), $imported->getOption('autoplayInterval'));
        $this->assertEquals($original->getOption('transition'), $imported->getOption('transition'));
        $this->assertEquals($original->getOption('theme'), $imported->getOption('theme'));
    }

    public function testExportAndImportWithAllCarouselTypes(): void
    {
        $types = [
            Carousel::TYPE_IMAGE,
            Carousel::TYPE_CARD,
            Carousel::TYPE_TESTIMONIAL,
            Carousel::TYPE_GALLERY,
            Carousel::TYPE_SIMPLE,
            Carousel::TYPE_INFINITE,
        ];
        
        foreach ($types as $type) {
            $original = new Carousel('test-' . $type, $type, [
                'autoplay' => false,
            ]);
            $original->addItem(['id' => '1', 'image' => 'test.jpg']);
            
            $config = $original->exportConfig();
            $imported = Carousel::fromConfig($config);
            
            $this->assertEquals($type, $imported->getType(), "Failed for type: {$type}");
            $this->assertEquals($original->getId(), $imported->getId(), "Failed for type: {$type}");
        }
    }

    public function testExportAndImportWithCustomOptions(): void
    {
        $original = new Carousel('custom', Carousel::TYPE_IMAGE, [
            'customTransition' => [
                'duration' => 600,
                'timingFunction' => 'ease',
                'properties' => ['transform'],
            ],
            'animations' => [
                'slideIn' => 'slideIn 0.5s ease-out',
            ],
            'themeColors' => [
                'light' => ['background' => '#ffffff'],
                'dark' => ['background' => '#000000'],
            ],
        ]);
        
        $config = $original->exportConfig();
        $imported = Carousel::fromConfig($config);
        
        $this->assertEquals($original->getOption('customTransition'), $imported->getOption('customTransition'));
        $this->assertEquals($original->getOption('animations'), $imported->getOption('animations'));
        $this->assertEquals($original->getOption('themeColors'), $imported->getOption('themeColors'));
    }

    public function testFromConfigHandlesEmptyItems(): void
    {
        $config = [
            'id' => 'empty',
            'type' => Carousel::TYPE_IMAGE,
            'options' => [],
        ];
        
        $carousel = Carousel::fromConfig($config);
        
        $this->assertEquals('empty', $carousel->getId());
        $this->assertCount(0, $carousel->getItems());
    }

    public function testExportConfigCanBeJsonEncoded(): void
    {
        $carousel = new Carousel('json-test', Carousel::TYPE_CARD, [
            'autoplay' => true,
        ]);
        $carousel->addItem(['id' => '1', 'title' => 'Test']);
        
        $config = $carousel->exportConfig();
        $json = json_encode($config);
        
        $this->assertNotFalse($json);
        $this->assertIsString($json);
        
        // Verify it can be decoded back
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        $this->assertEquals('json-test', $decoded['id']);
    }
}


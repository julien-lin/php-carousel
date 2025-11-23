<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;
use JulienLinard\Carousel\Exception\EmptyCarouselException;

class CarouselTest extends TestCase
{
    public function testCreateImageCarousel(): void
    {
        $carousel = Carousel::image('test', [
            'image1.jpg',
            'image2.jpg',
        ]);

        $this->assertEquals('test', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_IMAGE, $carousel->getType());
        $this->assertCount(2, $carousel->getItems());
    }

    public function testCreateCardCarousel(): void
    {
        $carousel = Carousel::card('products', [
            [
                'id' => '1',
                'title' => 'Product 1',
                'image' => 'product1.jpg',
            ],
        ]);

        $this->assertEquals('products', $carousel->getId());
        $this->assertEquals(Carousel::TYPE_CARD, $carousel->getType());
    }

    public function testAddItem(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);
        $item = new CarouselItem('item1', 'Title', 'Content');

        $carousel->addItem($item);

        $this->assertCount(1, $carousel->getItems());
        $this->assertEquals($item, $carousel->getItems()[0]);
    }

    public function testAddItemsFromArray(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);
        
        $carousel->addItem([
            'id' => 'item1',
            'title' => 'Title 1',
        ]);

        $this->assertCount(1, $carousel->getItems());
        $this->assertInstanceOf(CarouselItem::class, $carousel->getItems()[0]);
    }

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

    public function testRenderEmptyCarouselThrowsException(): void
    {
        $this->expectException(EmptyCarouselException::class);
        
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);
        $carousel->render();
    }

    public function testRenderCarousel(): void
    {
        $carousel = Carousel::image('test', ['image1.jpg']);
        $output = $carousel->render();

        $this->assertStringContainsString('carousel-container', $output);
        $this->assertStringContainsString('carousel-test', $output);
        $this->assertStringContainsString('<style', $output);
        $this->assertStringContainsString('<script', $output);
    }

    public function testCarouselItemFromArray(): void
    {
        $data = [
            'id' => 'test',
            'title' => 'Test Title',
            'content' => 'Test Content',
            'image' => 'test.jpg',
            'link' => '/test',
        ];

        $item = CarouselItem::fromArray($data);

        $this->assertEquals('test', $item->id);
        $this->assertEquals('Test Title', $item->title);
        $this->assertEquals('Test Content', $item->content);
    }

    public function testCarouselItemToArray(): void
    {
        $item = new CarouselItem('test', 'Title', 'Content', 'image.jpg', '/link');
        $array = $item->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('test', $array['id']);
        $this->assertEquals('Title', $array['title']);
    }
}


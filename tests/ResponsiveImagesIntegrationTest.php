<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;
use JulienLinard\Carousel\Image\ImageSourceSet;
use JulienLinard\Carousel\Image\ImageOptimizer;

class ResponsiveImagesIntegrationTest extends TestCase
{
    /**
     * Test carousel item can have image source set
     */
    public function testCarouselItemCanHaveImageSourceSet(): void
    {
        $item = new CarouselItem('1', 'Test', '', 'fallback.jpg');
        $sourceSet = new ImageSourceSet('fallback.jpg', 'Test image');
        $sourceSet->addSource('image-400w.webp 400w', null, 'image/webp');
        
        $item->setImageSourceSet($sourceSet);
        
        $this->assertTrue($item->hasImageSourceSet());
        $this->assertInstanceOf(ImageSourceSet::class, $item->getImageSourceSet());
    }

    /**
     * Test carousel renders picture element when source set is provided
     */
    public function testCarouselRendersPictureElementWhenSourceSetProvided(): void
    {
        $item = new CarouselItem('1', 'Test', '', 'fallback.jpg');
        $sourceSet = new ImageSourceSet('fallback.jpg', 'Test image');
        $sourceSet->addSource('image-400w.webp 400w', null, 'image/webp');
        $item->setImageSourceSet($sourceSet);
        
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE);
        $carousel->addItem($item);
        
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('<picture>', $html);
        $this->assertStringContainsString('<source', $html);
        $this->assertStringContainsString('srcset="image-400w.webp 400w"', $html);
    }

    /**
     * Test carousel falls back to regular image when no source set
     */
    public function testCarouselFallsBackToRegularImageWhenNoSourceSet(): void
    {
        $item = new CarouselItem('1', 'Test', '', 'image.jpg');
        
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE);
        $carousel->addItem($item);
        
        $html = $carousel->renderHtml();
        
        $this->assertStringNotContainsString('<picture>', $html);
        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('src="image.jpg"', $html);
    }

    /**
     * Test card carousel uses source set
     */
    public function testCardCarouselUsesSourceSet(): void
    {
        $item = new CarouselItem('1', 'Product', 'Description', 'fallback.jpg');
        $sourceSet = new ImageSourceSet('fallback.jpg');
        $sourceSet->addSource('product-400w.webp 400w', null, 'image/webp');
        $item->setImageSourceSet($sourceSet);
        
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_CARD);
        $carousel->addItem($item);
        
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('<picture>', $html);
        $this->assertStringContainsString('carousel-card-image', $html);
    }

    /**
     * Test gallery carousel uses source set
     */
    public function testGalleryCarouselUsesSourceSet(): void
    {
        $item = new CarouselItem('1', 'Gallery', '', 'fallback.jpg');
        $sourceSet = new ImageSourceSet('fallback.jpg');
        $sourceSet->addSource('gallery-400w.webp 400w', null, 'image/webp');
        $item->setImageSourceSet($sourceSet);
        
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_GALLERY);
        $carousel->addItem($item);
        
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('<picture>', $html);
        $this->assertStringContainsString('carousel-gallery-image-wrapper', $html);
    }

    /**
     * Test ImageOptimizer can generate source set from base URL
     */
    public function testImageOptimizerCanGenerateSourceSetFromBaseUrl(): void
    {
        $sourceSet = ImageOptimizer::generateFromBase('product.jpg', [400, 800, 1200], ['webp', 'jpg']);
        
        $this->assertTrue($sourceSet->hasSources());
        $sources = $sourceSet->getSources();
        $this->assertGreaterThan(0, count($sources));
        
        // Check that webp sources are included
        $hasWebp = false;
        foreach ($sources as $source) {
            if ($source['type'] === 'image/webp') {
                $hasWebp = true;
                break;
            }
        }
        $this->assertTrue($hasWebp);
    }

    /**
     * Test multiple formats are supported
     */
    public function testMultipleFormatsAreSupported(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $sourceSet->addSource('image-400w.webp 400w', null, 'image/webp');
        $sourceSet->addSource('image-400w.avif 400w', null, 'image/avif');
        
        $html = $sourceSet->render();
        
        $this->assertStringContainsString('image/webp', $html);
        $this->assertStringContainsString('image/avif', $html);
    }

    /**
     * Test media queries work correctly
     */
    public function testMediaQueriesWorkCorrectly(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $sourceSet->addSource('image-400w.webp 400w', '(max-width: 400px)', 'image/webp');
        $sourceSet->addSource('image-800w.webp 800w', '(max-width: 800px)', 'image/webp');
        $sourceSet->addSource('image-1200w.webp 1200w', null, 'image/webp');
        
        $html = $sourceSet->render();
        
        $this->assertStringContainsString('media="(max-width: 400px)"', $html);
        $this->assertStringContainsString('media="(max-width: 800px)"', $html);
        // Last source should not have media (desktop)
        $this->assertStringContainsString('srcset="image-1200w.webp 1200w"', $html);
    }
}


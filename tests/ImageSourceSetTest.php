<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Image\ImageSourceSet;
use JulienLinard\Carousel\Image\ImageOptimizer;

class ImageSourceSetTest extends TestCase
{
    /**
     * Test ImageSourceSet can be instantiated
     */
    public function testImageSourceSetCanBeInstantiated(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $this->assertInstanceOf(ImageSourceSet::class, $sourceSet);
    }

    /**
     * Test fallback image is set correctly
     */
    public function testFallbackImageIsSetCorrectly(): void
    {
        $sourceSet = new ImageSourceSet('fallback.jpg');
        $this->assertEquals('fallback.jpg', $sourceSet->getFallback());
    }

    /**
     * Test alt text can be set
     */
    public function testAltTextCanBeSet(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg', 'Alt text');
        $html = $sourceSet->render();
        $this->assertStringContainsString('alt="Alt text"', $html);
    }

    /**
     * Test source can be added
     */
    public function testSourceCanBeAdded(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $sourceSet->addSource('image-400w.webp 400w, image-800w.webp 800w', null, 'image/webp');
        
        $sources = $sourceSet->getSources();
        $this->assertCount(1, $sources);
        $this->assertEquals('image-400w.webp 400w, image-800w.webp 800w', $sources[0]['srcset']);
    }

    /**
     * Test multiple sources can be added
     */
    public function testMultipleSourcesCanBeAdded(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $sourceSet->addSource('image-400w.webp 400w', '(max-width: 400px)', 'image/webp');
        $sourceSet->addSource('image-800w.webp 800w', '(max-width: 800px)', 'image/webp');
        
        $sources = $sourceSet->getSources();
        $this->assertCount(2, $sources);
    }

    /**
     * Test render generates picture element
     */
    public function testRenderGeneratesPictureElement(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $sourceSet->addSource('image-400w.webp 400w', null, 'image/webp');
        
        $html = $sourceSet->render();
        $this->assertStringStartsWith('<picture>', $html);
        $this->assertStringEndsWith('</picture>', $html);
    }

    /**
     * Test render includes source elements
     */
    public function testRenderIncludesSourceElements(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $sourceSet->addSource('image-400w.webp 400w', null, 'image/webp');
        
        $html = $sourceSet->render();
        $this->assertStringContainsString('<source', $html);
        $this->assertStringContainsString('srcset="image-400w.webp 400w"', $html);
        $this->assertStringContainsString('type="image/webp"', $html);
    }

    /**
     * Test render includes fallback img element
     */
    public function testRenderIncludesFallbackImgElement(): void
    {
        $sourceSet = new ImageSourceSet('fallback.jpg', 'Alt text');
        $html = $sourceSet->render();
        
        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('src="fallback.jpg"', $html);
        $this->assertStringContainsString('alt="Alt text"', $html);
    }

    /**
     * Test render includes lazy loading attribute
     */
    public function testRenderIncludesLazyLoadingAttribute(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $html = $sourceSet->render(true);
        
        $this->assertStringContainsString('loading="lazy"', $html);
    }

    /**
     * Test render excludes lazy loading when disabled
     */
    public function testRenderExcludesLazyLoadingWhenDisabled(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $html = $sourceSet->render(false);
        
        $this->assertStringNotContainsString('loading="lazy"', $html);
    }

    /**
     * Test render includes media query
     */
    public function testRenderIncludesMediaQuery(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $sourceSet->addSource('image-400w.webp 400w', '(max-width: 400px)', 'image/webp');
        
        $html = $sourceSet->render();
        $this->assertStringContainsString('media="(max-width: 400px)"', $html);
    }

    /**
     * Test hasSources returns false when no sources
     */
    public function testHasSourcesReturnsFalseWhenNoSources(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $this->assertFalse($sourceSet->hasSources());
    }

    /**
     * Test hasSources returns true when sources exist
     */
    public function testHasSourcesReturnsTrueWhenSourcesExist(): void
    {
        $sourceSet = new ImageSourceSet('image.jpg');
        $sourceSet->addSource('image-400w.webp 400w', null, 'image/webp');
        $this->assertTrue($sourceSet->hasSources());
    }

    /**
     * Test getMimeType returns correct MIME types
     */
    public function testGetMimeTypeReturnsCorrectMimeTypes(): void
    {
        $this->assertEquals('image/webp', ImageSourceSet::getMimeType('webp'));
        $this->assertEquals('image/avif', ImageSourceSet::getMimeType('avif'));
        $this->assertEquals('image/jpeg', ImageSourceSet::getMimeType('jpg'));
        $this->assertEquals('image/jpeg', ImageSourceSet::getMimeType('jpeg'));
        $this->assertEquals('image/png', ImageSourceSet::getMimeType('png'));
        $this->assertEquals('image/gif', ImageSourceSet::getMimeType('gif'));
        $this->assertEquals('image/svg+xml', ImageSourceSet::getMimeType('svg'));
    }

    /**
     * Test getMimeType defaults to jpeg for unknown formats
     */
    public function testGetMimeTypeDefaultsToJpegForUnknownFormats(): void
    {
        $this->assertEquals('image/jpeg', ImageSourceSet::getMimeType('unknown'));
    }

    /**
     * Test ImageOptimizer generateFromBase creates correct source set
     */
    public function testImageOptimizerGenerateFromBaseCreatesCorrectSourceSet(): void
    {
        $sourceSet = ImageOptimizer::generateFromBase('image.jpg', [400, 800], ['webp', 'jpg']);
        
        $this->assertInstanceOf(ImageSourceSet::class, $sourceSet);
        $this->assertEquals('image.jpg', $sourceSet->getFallback());
        $this->assertTrue($sourceSet->hasSources());
        
        $sources = $sourceSet->getSources();
        $this->assertGreaterThan(0, count($sources));
    }

    /**
     * Test ImageOptimizer generateWithBreakpoints creates correct source set
     */
    public function testImageOptimizerGenerateWithBreakpointsCreatesCorrectSourceSet(): void
    {
        $sourceSet = ImageOptimizer::generateWithBreakpoints(
            'image.jpg',
            [
                400 => '(max-width: 400px)',
                800 => '(max-width: 800px)',
            ],
            ['webp']
        );
        
        $this->assertInstanceOf(ImageSourceSet::class, $sourceSet);
        $sources = $sourceSet->getSources();
        $this->assertGreaterThan(0, count($sources));
        
        // Check media queries are included
        $hasMediaQuery = false;
        foreach ($sources as $source) {
            if ($source['media'] !== null) {
                $hasMediaQuery = true;
                break;
            }
        }
        $this->assertTrue($hasMediaQuery);
    }

    /**
     * Test ImageOptimizer fromArray creates correct source set
     */
    public function testImageOptimizerFromArrayCreatesCorrectSourceSet(): void
    {
        $config = [
            'fallback' => 'image.jpg',
            'alt' => 'Test image',
            'sources' => [
                [
                    'srcset' => 'image-400w.webp 400w',
                    'type' => 'image/webp',
                ],
                [
                    'srcset' => 'image-800w.webp 800w',
                    'media' => '(max-width: 800px)',
                    'type' => 'image/webp',
                ],
            ],
        ];
        
        $sourceSet = ImageOptimizer::fromArray($config);
        
        $this->assertInstanceOf(ImageSourceSet::class, $sourceSet);
        $this->assertEquals('image.jpg', $sourceSet->getFallback());
        $sources = $sourceSet->getSources();
        $this->assertCount(2, $sources);
    }

    /**
     * Test ImageOptimizer fromArray throws exception when fallback missing
     */
    public function testImageOptimizerFromArrayThrowsExceptionWhenFallbackMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('fallback is required');
        
        ImageOptimizer::fromArray([]);
    }

    /**
     * Test render escapes special characters in URLs
     */
    public function testRenderEscapesSpecialCharactersInUrls(): void
    {
        $sourceSet = new ImageSourceSet('image with spaces.jpg', 'Alt with "quotes"');
        $sourceSet->addSource('image-400w.webp 400w', null, 'image/webp');
        
        $html = $sourceSet->render();
        
        // Should escape quotes in alt
        $this->assertStringContainsString('alt="Alt with &quot;quotes&quot;"', $html);
    }
}


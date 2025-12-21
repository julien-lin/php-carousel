<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselRenderer;
use JulienLinard\Carousel\Renderer\CssRenderer;
use JulienLinard\Carousel\Renderer\RenderCacheService;

/**
 * Tests to verify CssRenderer output matches CarouselRenderer output
 */
class CssRendererMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear cache before each test
        RenderCacheService::clear();
    }

    /**
     * Normalize CSS by removing accessibility enhancements (focus-visible styles)
     * that were added after the legacy renderer
     */
    private function normalizeCss(string $css): string
    {
        // Remove focus-visible styles (added in Phase 4.3)
        $css = preg_replace('/\/\* Enhanced focus styles[^*]*\*\/.*?}/s', '', $css);
        $css = preg_replace('/\.carousel-arrow:focus-visible[^}]*}/s', '', $css);
        $css = preg_replace('/\.carousel-arrow:focus:not\(:focus-visible\)[^}]*}/s', '', $css);
        $css = preg_replace('/\.carousel-dot:focus-visible[^}]*}/s', '', $css);
        $css = preg_replace('/\.carousel-dot:focus:not\(:focus-visible\)[^}]*}/s', '', $css);
        $css = preg_replace('/#carousel-[^:]*:focus-visible[^}]*}/s', '', $css);
        $css = preg_replace('/#carousel-[^:]*:focus:not\(:focus-visible\)[^}]*}/s', '', $css);
        
        // Normalize aria-selected selector (remove it from combined selectors)
        // Match: .carousel-dot.active, .carousel-dot[aria-selected="true"] { ... }
        $css = preg_replace('/\.carousel-dot\.active,\s*\.carousel-dot\[aria-selected="true"\]\s*\{/s', '.carousel-dot.active {', $css);
        // Match: #carousel-id .carousel-dot.active, #carousel-id .carousel-dot[aria-selected="true"] { ... }
        $css = preg_replace('/(#carousel-[^\s]+)\s+\.carousel-dot\.active,\s*\1\s+\.carousel-dot\[aria-selected="true"\]\s*\{/s', '$1 .carousel-dot.active {', $css);
        // Remove standalone aria-selected selectors
        $css = preg_replace('/,\s*\.carousel-dot\[aria-selected="true"\]/s', '', $css);
        $css = preg_replace('/\.carousel-dot\[aria-selected="true"\]\s*\{[^}]*\}/s', '', $css);
        $css = preg_replace('/(#carousel-[^\s]+)\s+\.carousel-dot\[aria-selected="true"\]\s*\{[^}]*\}/s', '', $css);
        
        // Remove empty lines (multiple newlines)
        $css = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $css);
        
        // Normalize spacing differences (minified CSS may have different spacing)
        // This is a minor difference that doesn't affect functionality
        $css = preg_replace('/\s*\{/s', '{', $css);
        $css = preg_replace('/\}\s*/s', '}', $css);
        $css = preg_replace('/:\s+/s', ':', $css);
        $css = preg_replace('/;\s*/s', ';', $css);
        
        return $css;
    }

    /**
     * Test CSS output is identical for image carousel
     */
    public function testCssOutputIdenticalForImageCarousel(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $this->normalizeCss($legacyRenderer->renderCss());
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $this->normalizeCss($newRenderer->render($carousel));
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical for card carousel
     */
    public function testCssOutputIdenticalForCardCarousel(): void
    {
        $carousel = Carousel::card('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Card 1', 'content' => 'Content 1', 'image' => 'card1.jpg'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $this->normalizeCss($legacyRenderer->renderCss());
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $this->normalizeCss($newRenderer->render($carousel));
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical for testimonial carousel
     */
    public function testCssOutputIdenticalForTestimonialCarousel(): void
    {
        $carousel = Carousel::testimonial('test-' . uniqid(), [
            ['id' => '1', 'title' => 'John Doe', 'content' => 'Great product!'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $this->normalizeCss($legacyRenderer->renderCss());
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $this->normalizeCss($newRenderer->render($carousel));
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical for gallery carousel
     */
    public function testCssOutputIdenticalForGalleryCarousel(): void
    {
        $carousel = Carousel::gallery('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Gallery 1', 'image' => 'gallery1.jpg'],
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $this->normalizeCss($legacyRenderer->renderCss());
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $this->normalizeCss($newRenderer->render($carousel));
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical with options
     */
    public function testCssOutputIdenticalWithOptions(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions([
            'gap' => 20,
            'transitionDuration' => 1000,
            'height' => '500px',
            'itemsPerSlide' => 2,
        ]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $this->normalizeCss($legacyRenderer->renderCss());
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $this->normalizeCss($newRenderer->render($carousel));
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical with minification
     */
    public function testCssOutputIdenticalWithMinification(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions(['minify' => true]);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $this->normalizeCss($legacyRenderer->renderCss());
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $this->normalizeCss($newRenderer->render($carousel));
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS output is identical for infinite carousel
     */
    public function testCssOutputIdenticalForInfiniteCarousel(): void
    {
        $carousel = Carousel::infiniteCarousel('test-' . uniqid(), ['image1.jpg', 'image2.jpg']);
        
        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyCss = $this->normalizeCss($legacyRenderer->renderCss());
        
        RenderCacheService::clear();
        $newRenderer = new CssRenderer();
        $newCss = $this->normalizeCss($newRenderer->render($carousel));
        
        $this->assertEquals($legacyCss, $newCss);
    }

    /**
     * Test CSS cache works correctly
     */
    public function testCssCacheWorksCorrectly(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        
        $newRenderer = new CssRenderer();
        $firstCss = $newRenderer->render($carousel);
        
        // Second render should return empty string (cached)
        $secondCss = $newRenderer->render($carousel);
        
        $this->assertNotEmpty($firstCss);
        $this->assertEmpty($secondCss);
    }
}


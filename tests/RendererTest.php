<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\HtmlRenderer;
use JulienLinard\Carousel\Renderer\CssRenderer;
use JulienLinard\Carousel\Renderer\JsRenderer;
use JulienLinard\Carousel\Renderer\CompositeRenderer;
use JulienLinard\Carousel\Renderer\RendererInterface;

class RendererTest extends TestCase
{
    /**
     * Test HtmlRenderer implements RendererInterface
     */
    public function testHtmlRendererImplementsInterface(): void
    {
        $renderer = new HtmlRenderer();
        $this->assertInstanceOf(RendererInterface::class, $renderer);
    }

    /**
     * Test CssRenderer implements RendererInterface
     */
    public function testCssRendererImplementsInterface(): void
    {
        $renderer = new CssRenderer();
        $this->assertInstanceOf(RendererInterface::class, $renderer);
    }

    /**
     * Test JsRenderer implements RendererInterface
     */
    public function testJsRendererImplementsInterface(): void
    {
        $renderer = new JsRenderer();
        $this->assertInstanceOf(RendererInterface::class, $renderer);
    }

    /**
     * Test CompositeRenderer implements RendererInterface
     */
    public function testCompositeRendererImplementsInterface(): void
    {
        $renderer = new CompositeRenderer();
        $this->assertInstanceOf(RendererInterface::class, $renderer);
    }

    /**
     * Test HtmlRenderer renders HTML only
     */
    public function testHtmlRendererRendersHtmlOnly(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg', 'image2.jpg']);
        $renderer = new HtmlRenderer();
        $output = $renderer->render($carousel);
        
        $this->assertStringContainsString('<div class="carousel-container"', $output);
        $this->assertStringContainsString('carousel-slide', $output);
        $this->assertStringNotContainsString('<style', $output);
        $this->assertStringNotContainsString('<script', $output);
    }

    /**
     * Test CssRenderer renders CSS only
     */
    public function testCssRendererRendersCssOnly(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $renderer = new CssRenderer();
        $output = $renderer->render($carousel);
        
        $this->assertStringContainsString('<style', $output);
        // CSS can contain selectors like .carousel-container, but should not contain HTML tags
        $this->assertStringNotContainsString('<div class="carousel-container"', $output);
        $this->assertStringNotContainsString('<script', $output);
        // CSS can contain .carousel-slide selector, but should not contain HTML div with carousel-slide class
        $this->assertStringNotContainsString('<div class="carousel-slide"', $output);
        // Should not contain HTML img tags
        $this->assertStringNotContainsString('<img', $output);
    }

    /**
     * Test JsRenderer renders JavaScript only
     */
    public function testJsRendererRendersJsOnly(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $renderer = new JsRenderer();
        $output = $renderer->render($carousel);
        
        $this->assertStringContainsString('<script', $output);
        $this->assertStringContainsString('carousel', $output);
        $this->assertStringNotContainsString('<div class="carousel-container"', $output);
        $this->assertStringNotContainsString('<style', $output);
    }

    /**
     * Test CompositeRenderer combines all renderers
     */
    public function testCompositeRendererCombinesAll(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $renderer = new CompositeRenderer();
        $output = $renderer->render($carousel);
        
        $this->assertStringContainsString('<style', $output);
        $this->assertStringContainsString('<div class="carousel-container"', $output);
        $this->assertStringContainsString('<script', $output);
    }

    /**
     * Test CompositeRenderer order (CSS, HTML, JS)
     */
    public function testCompositeRendererOrder(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $renderer = new CompositeRenderer();
        $output = $renderer->render($carousel);
        
        $stylePos = strpos($output, '<style');
        $divPos = strpos($output, '<div class="carousel-container"');
        $scriptPos = strpos($output, '<script');
        
        $this->assertNotFalse($stylePos);
        $this->assertNotFalse($divPos);
        $this->assertNotFalse($scriptPos);
        $this->assertLessThan($divPos, $stylePos, 'CSS should come before HTML');
        $this->assertLessThan($scriptPos, $divPos, 'HTML should come before JS');
    }

    /**
     * Test CompositeRenderer getters
     */
    public function testCompositeRendererGetters(): void
    {
        $renderer = new CompositeRenderer();
        
        $this->assertInstanceOf(HtmlRenderer::class, $renderer->getHtmlRenderer());
        $this->assertInstanceOf(CssRenderer::class, $renderer->getCssRenderer());
        $this->assertInstanceOf(JsRenderer::class, $renderer->getJsRenderer());
    }

    /**
     * Test HtmlRenderer with different carousel types
     */
    public function testHtmlRendererWithDifferentTypes(): void
    {
        $types = [
            Carousel::TYPE_IMAGE,
            Carousel::TYPE_CARD,
            Carousel::TYPE_TESTIMONIAL,
            Carousel::TYPE_GALLERY,
            Carousel::TYPE_INFINITE,
        ];
        
        foreach ($types as $type) {
            $carousel = new Carousel('test-' . uniqid(), $type);
            $carousel->addItem(['id' => '1', 'title' => 'Test', 'image' => 'test.jpg']);
            
            $renderer = new HtmlRenderer();
            $output = $renderer->render($carousel);
            
            $this->assertStringContainsString('data-carousel-type="' . $type . '"', $output);
        }
    }

    /**
     * Test CssRenderer with different carousel types
     */
    public function testCssRendererWithDifferentTypes(): void
    {
        $carousel = Carousel::card('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Card 1', 'content' => 'Content 1']
        ]);
        
        $renderer = new CssRenderer();
        $output = $renderer->render($carousel);
        
        $this->assertStringContainsString('.carousel-card', $output);
    }

    /**
     * Test JsRenderer includes CarouselAPI
     */
    public function testJsRendererIncludesCarouselAPI(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $renderer = new JsRenderer();
        $output = $renderer->render($carousel);
        
        $this->assertStringContainsString('CarouselAPI', $output);
    }

    /**
     * Test renderers handle empty carousel
     */
    public function testRenderersHandleEmptyCarousel(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE);
        
        $htmlRenderer = new HtmlRenderer();
        $this->expectException(\JulienLinard\Carousel\Exception\EmptyCarouselException::class);
        $htmlRenderer->render($carousel);
    }

    /**
     * Test renderers are independent
     */
    public function testRenderersAreIndependent(): void
    {
        $carousel1 = Carousel::image('test1-' . uniqid(), ['image1.jpg']);
        $carousel2 = Carousel::image('test2-' . uniqid(), ['image2.jpg']);
        
        $htmlRenderer = new HtmlRenderer();
        $cssRenderer = new CssRenderer();
        $jsRenderer = new JsRenderer();
        
        $html1 = $htmlRenderer->render($carousel1);
        $html2 = $htmlRenderer->render($carousel2);
        
        $this->assertNotEquals($html1, $html2);
        $this->assertStringContainsString('test1-', $html1);
        $this->assertStringContainsString('test2-', $html2);
    }

    /**
     * Test CompositeRenderer output matches legacy renderer
     */
    public function testCompositeRendererMatchesLegacy(): void
    {
        // Use unique IDs to avoid cache issues
        $carousel1 = Carousel::image('test1-' . uniqid(), ['image1.jpg']);
        $carousel2 = Carousel::image('test2-' . uniqid(), ['image1.jpg']);
        
        $compositeRenderer = new CompositeRenderer();
        $compositeOutput = $compositeRenderer->render($carousel1);
        
        $legacyOutput = $carousel2->render();
        
        // Both should contain the same elements
        $this->assertStringContainsString('<style', $compositeOutput);
        $this->assertStringContainsString('<style', $legacyOutput);
        $this->assertStringContainsString('<div class="carousel-container"', $compositeOutput);
        $this->assertStringContainsString('<div class="carousel-container"', $legacyOutput);
        $this->assertStringContainsString('<script', $compositeOutput);
        $this->assertStringContainsString('<script', $legacyOutput);
    }
}


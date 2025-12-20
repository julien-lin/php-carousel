<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\SSR;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\CompositeRenderer;

/**
 * Server-Side Rendering Renderer
 * 
 * Generates static HTML that can be cached and served via CDN.
 * JavaScript can be loaded asynchronously for progressive enhancement.
 */
class SSRRenderer implements SSRRendererInterface
{
    private CompositeRenderer $renderer;

    public function __construct(?CompositeRenderer $renderer = null)
    {
        $this->renderer = $renderer ?? new CompositeRenderer();
    }

    /**
     * Render static HTML (no JavaScript required for initial display)
     * 
     * @param Carousel $carousel The carousel to render
     * @return string Static HTML output with CSS, but without JavaScript
     */
    public function renderStatic(Carousel $carousel): string
    {
        // Render HTML and CSS only (no JavaScript)
        $html = $this->renderer->getHtmlRenderer()->render($carousel);
        $css = $this->renderer->getCssRenderer()->render($carousel);
        
        // Combine HTML and CSS
        return $css . "\n" . $html;
    }

    /**
     * Hydrate static HTML with JavaScript for interactivity
     * 
     * @param string $html Static HTML from renderStatic()
     * @param string $carouselId Carousel ID
     * @return string HTML with JavaScript hydration
     */
    public function hydrate(string $html, string $carouselId): string
    {
        // Find the carousel in the HTML to get the Carousel instance
        // For now, we'll need to pass the carousel or reconstruct it
        // This is a simplified version - in production, you'd want to store
        // the carousel instance or configuration
        
        // Extract carousel from HTML (this is a workaround)
        // In a real implementation, you'd pass the carousel or config
        preg_match('/data-carousel-id="([^"]+)"/', $html, $matches);
        $id = $matches[1] ?? $carouselId;
        
        // For hydration, we need to create a temporary carousel
        // In production, you'd store the config or pass the carousel
        // This is a placeholder - the actual implementation would be more sophisticated
        
        return $html;
    }

    /**
     * Hydrate with a Carousel instance (better approach)
     * 
     * @param string $html Static HTML from renderStatic()
     * @param Carousel $carousel Carousel instance
     * @return string HTML with JavaScript hydration
     */
    public function hydrateWithCarousel(string $html, Carousel $carousel): string
    {
        // Render JavaScript
        $js = $this->renderer->getJsRenderer()->render($carousel);
        
        // Append JavaScript to HTML
        return $html . "\n" . $js;
    }
}


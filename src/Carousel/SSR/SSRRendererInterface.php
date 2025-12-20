<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\SSR;

use JulienLinard\Carousel\Carousel;

/**
 * Interface for Server-Side Rendering
 */
interface SSRRendererInterface
{
    /**
     * Render static HTML (no JavaScript required for initial display)
     * 
     * @param Carousel $carousel The carousel to render
     * @return string Static HTML output
     */
    public function renderStatic(Carousel $carousel): string;

    /**
     * Hydrate static HTML with JavaScript for interactivity
     * 
     * @param string $html Static HTML from renderStatic()
     * @param string $carouselId Carousel ID
     * @return string HTML with JavaScript hydration
     */
    public function hydrate(string $html, string $carouselId): string;
}


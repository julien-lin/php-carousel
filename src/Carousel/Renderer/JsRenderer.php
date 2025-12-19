<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselRenderer as LegacyRenderer;
use JulienLinard\Carousel\Translator\TranslatorInterface;

/**
 * JavaScript Renderer - renders only JavaScript code
 */
class JsRenderer extends AbstractRenderer
{
    private LegacyRenderer $legacyRenderer;

    public function __construct(?TranslatorInterface $translator = null)
    {
        parent::__construct($translator);
    }

    /**
     * Render JavaScript code
     * 
     * @param Carousel $carousel The carousel to render
     * @return string JavaScript output
     */
    public function render(Carousel $carousel): string
    {
        // Use legacy renderer for now (will be migrated progressively)
        // The legacy renderer will handle translator initialization from carousel options
        $this->legacyRenderer = new LegacyRenderer($carousel);
        
        return $this->legacyRenderer->renderJs();
    }
}


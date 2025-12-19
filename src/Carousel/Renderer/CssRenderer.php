<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselRenderer as LegacyRenderer;
use JulienLinard\Carousel\Translator\TranslatorInterface;

/**
 * CSS Renderer - renders only CSS styles
 */
class CssRenderer extends AbstractRenderer
{
    private LegacyRenderer $legacyRenderer;

    public function __construct(?TranslatorInterface $translator = null)
    {
        parent::__construct($translator);
    }

    /**
     * Render CSS styles
     * 
     * @param Carousel $carousel The carousel to render
     * @return string CSS output
     */
    public function render(Carousel $carousel): string
    {
        // Use legacy renderer for now (will be migrated progressively)
        // The legacy renderer will handle translator initialization from carousel options
        $this->legacyRenderer = new LegacyRenderer($carousel);
        
        return $this->legacyRenderer->renderCss();
    }
}


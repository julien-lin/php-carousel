<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Translator\TranslatorInterface;

/**
 * Composite Renderer - combines HTML, CSS and JS renderers
 */
class CompositeRenderer implements RendererInterface
{
    private HtmlRenderer $htmlRenderer;
    private CssRenderer $cssRenderer;
    private JsRenderer $jsRenderer;

    public function __construct(?TranslatorInterface $translator = null)
    {
        $this->htmlRenderer = new HtmlRenderer($translator);
        $this->cssRenderer = new CssRenderer($translator);
        $this->jsRenderer = new JsRenderer($translator);
    }

    /**
     * Render complete carousel (CSS + HTML + JS)
     * 
     * @param Carousel $carousel The carousel to render
     * @return string Complete output
     */
    public function render(Carousel $carousel): string
    {
        $css = $this->cssRenderer->render($carousel);
        $html = $this->htmlRenderer->render($carousel);
        $js = $this->jsRenderer->render($carousel);

        return $css . "\n" . $html . "\n" . $js;
    }

    /**
     * Get HTML renderer
     * 
     * @return HtmlRenderer
     */
    public function getHtmlRenderer(): HtmlRenderer
    {
        return $this->htmlRenderer;
    }

    /**
     * Get CSS renderer
     * 
     * @return CssRenderer
     */
    public function getCssRenderer(): CssRenderer
    {
        return $this->cssRenderer;
    }

    /**
     * Get JS renderer
     * 
     * @return JsRenderer
     */
    public function getJsRenderer(): JsRenderer
    {
        return $this->jsRenderer;
    }
}


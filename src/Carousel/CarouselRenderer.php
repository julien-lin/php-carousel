<?php

declare(strict_types=1);

namespace JulienLinard\Carousel;

use JulienLinard\Carousel\Exception;
use JulienLinard\Carousel\Helper\CssMinifier;
use JulienLinard\Carousel\Helper\JsMinifier;
use JulienLinard\Carousel\Image\ImageSourceSet;
use JulienLinard\Carousel\JavaScript\CarouselAPI;
use JulienLinard\Carousel\JavaScript\CarouselInstance;
use JulienLinard\Carousel\Translator\TranslatorInterface;
use JulienLinard\Carousel\Translator\ArrayTranslator;
use JulienLinard\Carousel\Validator\UrlValidator;

/**
 * Renders carousel HTML, CSS and JavaScript
 */
class CarouselRenderer
{
    private Carousel $carousel;
    private TranslatorInterface $translator;
    private static array $renderedCarousels = [];

    public function __construct(Carousel $carousel)
    {
        $this->carousel = $carousel;
        
        // Get translator from options or create default
        $options = $carousel->getOptions();
        $this->translator = $options['translator'] ?? new ArrayTranslator([], $options['locale'] ?? 'en');
        
        // Set locale if provided
        if (isset($options['locale'])) {
            $this->translator->setLocale($options['locale']);
        }
    }

    /**
     * Escape a string for HTML output
     * 
     * @param string $value The value to escape
     * @return string Escaped value
     */
    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Render complete carousel (HTML + CSS + JS)
     */
    public function render(): string
    {
        $html = $this->renderHtml();
        $css = $this->renderCss();
        $js = $this->renderJs();

        return $css . "\n" . $html . "\n" . $js;
    }

    /**
     * Render HTML structure
     */
    public function renderHtml(): string
    {
        $id = $this->carousel->getId();
        $type = $this->carousel->getType();
        $items = $this->carousel->getItems();
        $options = $this->carousel->getOptions();
        
        if (empty($items)) {
            throw new Exception\EmptyCarouselException();
        }
        
        $transition = $options['transition'] ?? 'slide';
        $html = '<div class="carousel-container" id="carousel-' . $this->escape($id) . '" data-carousel-id="' . $this->escape($id) . '" data-carousel-type="' . $this->escape($type) . '" data-carousel-transition="' . $this->escape($transition) . '">';
        
        // Loading indicator
        $html .= '<div class="carousel-loading" aria-hidden="true" role="status" aria-label="' . $this->escape($this->translator->translate('loading')) . '">';
        $html .= '<div class="carousel-spinner"></div>';
        $html .= '</div>';
        
        // Screen reader announcement
        $html .= '<div class="sr-only carousel-announcement" aria-live="polite" aria-atomic="true"></div>';
        
        // Wrapper
        $html .= '<div class="carousel-wrapper">';
        
        // Track
        $html .= '<div class="carousel-track" role="region" aria-label="' . $this->escape($this->translator->translate('carousel')) . '" aria-live="polite" aria-atomic="true">';
        
        foreach ($items as $index => $item) {
            $html .= $this->renderItem($item, $index, $type);
        }
        
        $html .= '</div>'; // .carousel-track
        
        // Navigation arrows
        if ($options['showArrows'] ?? true) {
            $html .= '<button class="carousel-arrow carousel-arrow-prev" aria-label="' . $this->escape($this->translator->translate('previous_slide')) . '" type="button">';
            $html .= '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            $html .= '<path d="M15 18l-6-6 6-6"/>';
            $html .= '</svg>';
            $html .= '</button>';
            
            $html .= '<button class="carousel-arrow carousel-arrow-next" aria-label="' . $this->escape($this->translator->translate('next_slide')) . '" type="button">';
            $html .= '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            $html .= '<path d="M9 18l6-6-6-6"/>';
            $html .= '</svg>';
            $html .= '</button>';
        }
        
        $html .= '</div>'; // .carousel-wrapper
        
        // Dots navigation
        if (($options['showDots'] ?? true) && count($items) > 1) {
            $html .= '<div class="carousel-dots" role="tablist">';
            foreach ($items as $index => $item) {
                $html .= '<button class="carousel-dot' . ($index === 0 ? ' active' : '') . '" role="tab" aria-label="' . $this->escape($this->translator->translate('go_to_slide', null, ['index' => $index + 1])) . '" data-slide="' . $index . '" type="button"></button>';
            }
            $html .= '</div>';
        }
        
        // Thumbnails (for gallery type)
        if (($options['showThumbnails'] ?? false) && $type === Carousel::TYPE_GALLERY) {
            $html .= '<div class="carousel-thumbnails">';
            foreach ($items as $index => $item) {
                $html .= '<button class="carousel-thumbnail' . ($index === 0 ? ' active' : '') . '" data-slide="' . $index . '" type="button">';
                if ($item->image) {
                    $html .= '<img src="' . $this->escape($item->image) . '" alt="' . $this->escape($item->title ?: 'Thumbnail ' . ($index + 1)) . '" loading="lazy">';
                }
                $html .= '</button>';
            }
            $html .= '</div>';
        }
        
        $html .= '</div>'; // .carousel-container
        
        return $html;
    }

    /**
     * Render a single carousel item
     */
    private function renderItem(CarouselItem $item, int $index, string $type): string
    {
        $items = $this->carousel->getItems();
        $isActive = $index === 0;
        $totalSlides = count($items);
        
        $html = '<div class="carousel-slide' . ($isActive ? ' active' : '') . '" ';
        $html .= 'data-slide-index="' . $index . '" ';
        $html .= 'role="group" ';
        $html .= 'aria-roledescription="slide" ';
        $html .= 'aria-label="' . $this->escape($this->translator->translate('slide_of', null, ['current' => $index + 1, 'total' => $totalSlides])) . '" ';
        $html .= 'aria-hidden="' . ($isActive ? 'false' : 'true') . '" ';
        if ($isActive) {
            $html .= 'aria-current="true" ';
        }
        $html .= '>';
        
        switch ($type) {
            case Carousel::TYPE_IMAGE:
                $html .= $this->renderImageItem($item, $index);
                break;
            case Carousel::TYPE_CARD:
                $html .= $this->renderCardItem($item, $index);
                break;
            case Carousel::TYPE_TESTIMONIAL:
                $html .= $this->renderTestimonialItem($item);
                break;
            case Carousel::TYPE_GALLERY:
                $html .= $this->renderGalleryItem($item, $index);
                break;
            case Carousel::TYPE_INFINITE:
                // Infinite carousel uses image rendering
                $html .= $this->renderImageItem($item, $index);
                break;
            default:
                $html .= $this->renderSimpleItem($item);
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Render image item
     */
    private function renderImageItem(CarouselItem $item, int $index): string
    {
        $html = '';
        $lazyLoad = $this->carousel->getOption('lazyLoad', true);
        // Load first 2 slides immediately, lazy load the rest
        $shouldLazyLoad = $lazyLoad && $index > 1;
        
        if ($item->link) {
            $html .= '<a href="' . UrlValidator::sanitize($item->link) . '" class="carousel-image-link">';
        }
        
        if ($item->hasImageSourceSet()) {
            // Use responsive image source set
            $html .= '<div class="carousel-image-wrapper">';
            $html .= $item->getImageSourceSet()->render(!$shouldLazyLoad);
            $html .= '</div>';
        } elseif ($item->image) {
            // Fallback to regular image
            if ($shouldLazyLoad) {
                $html .= '<img data-src="' . $this->escape($item->image) . '" alt="' . $this->escape($item->title ?: '') . '" class="carousel-image" loading="lazy">';
            } else {
                $html .= '<img src="' . $this->escape($item->image) . '" alt="' . $this->escape($item->title ?: '') . '" class="carousel-image">';
            }
        }
        
        if ($item->title || $item->content) {
            $html .= '<div class="carousel-caption">';
            if ($item->title) {
                $html .= '<h3 class="carousel-title">' . $this->escape($item->title) . '</h3>';
            }
            if ($item->content) {
                $html .= '<p class="carousel-content">' . $this->escape($item->content) . '</p>';
            }
            $html .= '</div>';
        }
        
        if ($item->link) {
            $html .= '</a>';
        }
        
        return $html;
    }

    /**
     * Render card item
     */
    private function renderCardItem(CarouselItem $item, int $index): string
    {
        $html = '<div class="carousel-card">';
        $lazyLoad = $this->carousel->getOption('lazyLoad', true);
        $shouldLazyLoad = $lazyLoad && $index > 1;
        
        if ($item->hasImageSourceSet()) {
            // Use responsive image source set
            $html .= '<div class="carousel-card-image">';
            $html .= $item->getImageSourceSet()->render(!$shouldLazyLoad);
            $html .= '</div>';
        } elseif ($item->image) {
            // Fallback to regular image
            $html .= '<div class="carousel-card-image">';
            if ($shouldLazyLoad) {
                $html .= '<img data-src="' . $this->escape($item->image) . '" alt="' . $this->escape($item->title ?: '') . '" loading="lazy">';
            } else {
                $html .= '<img src="' . $this->escape($item->image) . '" alt="' . $this->escape($item->title ?: '') . '">';
            }
            $html .= '</div>';
        }
        
        $html .= '<div class="carousel-card-body">';
        if ($item->title) {
            $html .= '<h3 class="carousel-card-title">' . $this->escape($item->title) . '</h3>';
        }
        if ($item->content) {
            $html .= '<p class="carousel-card-content">' . $this->escape($item->content) . '</p>';
        }
        if ($item->link) {
            $html .= '<a href="' . UrlValidator::sanitize($item->link) . '" class="carousel-card-link">Learn more</a>';
        }
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Render testimonial item
     */
    private function renderTestimonialItem(CarouselItem $item): string
    {
        $html = '<div class="carousel-testimonial">';
        
        if ($item->content) {
            $html .= '<blockquote class="carousel-testimonial-quote">';
            $html .= '<p>' . $this->escape($item->content) . '</p>';
            $html .= '</blockquote>';
        }
        
        if ($item->title) {
            $html .= '<div class="carousel-testimonial-author">';
            if ($item->image) {
                $html .= '<img src="' . $this->escape($item->image) . '" alt="' . $this->escape($item->title) . '" class="carousel-testimonial-avatar" loading="lazy">';
            }
            $html .= '<div class="carousel-testimonial-info">';
            $html .= '<cite class="carousel-testimonial-name">' . $this->escape($item->title) . '</cite>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Render gallery item
     */
    private function renderGalleryItem(CarouselItem $item, int $index): string
    {
        $html = '<div class="carousel-gallery-item">';
        $lazyLoad = $this->carousel->getOption('lazyLoad', true);
        $shouldLazyLoad = $lazyLoad && $index > 1;
        
        if ($item->hasImageSourceSet()) {
            // Use responsive image source set
            $html .= '<div class="carousel-gallery-image-wrapper">';
            $html .= $item->getImageSourceSet()->render(!$shouldLazyLoad);
            $html .= '</div>';
        } elseif ($item->image) {
            // Fallback to regular image
            if ($shouldLazyLoad) {
                $html .= '<img data-src="' . $this->escape($item->image) . '" alt="' . $this->escape($item->title ?: '') . '" class="carousel-gallery-image" loading="lazy">';
            } else {
                $html .= '<img src="' . $this->escape($item->image) . '" alt="' . $this->escape($item->title ?: '') . '" class="carousel-gallery-image">';
            }
        }
        
        if ($item->title || $item->content) {
            $html .= '<div class="carousel-gallery-caption">';
            if ($item->title) {
                $html .= '<h4 class="carousel-gallery-title">' . $this->escape($item->title) . '</h4>';
            }
            if ($item->content) {
                $html .= '<p class="carousel-gallery-content">' . $this->escape($item->content) . '</p>';
            }
            $html .= '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Render simple item
     */
    private function renderSimpleItem(CarouselItem $item): string
    {
        $html = '<div class="carousel-simple-item">';
        
        if ($item->title) {
            $html .= '<h3>' . $this->escape($item->title) . '</h3>';
        }
        
        if ($item->content) {
            $html .= '<div>' . $this->escape($item->content) . '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Render CSS styles
     */
    public function renderCss(): string
    {
        $id = $this->carousel->getId();
        $options = $this->carousel->getOptions();
        $type = $this->carousel->getType();
        
        // Only render CSS once per carousel ID
        if (isset(self::$renderedCarousels[$id])) {
            return '';
        }
        self::$renderedCarousels[$id] = true;
        
        $cssId = '#carousel-' . $id;
        $gap = $options['gap'] ?? 16;
        $transitionDuration = ($options['transitionDuration'] ?? 500) . 'ms';
        
        $css = '<style id="carousel-style-' . $this->escape($id) . '">';
        
        // Base styles
        $css .= $this->getBaseCss($cssId, $gap, $transitionDuration);
        
        // Type-specific styles
        switch ($type) {
            case Carousel::TYPE_IMAGE:
                $css .= $this->getImageCss($cssId);
                break;
            case Carousel::TYPE_CARD:
                $css .= $this->getCardCss($cssId, $options);
                break;
            case Carousel::TYPE_TESTIMONIAL:
                $css .= $this->getTestimonialCss($cssId);
                break;
            case Carousel::TYPE_GALLERY:
                $css .= $this->getGalleryCss($cssId);
                break;
            case Carousel::TYPE_INFINITE:
                // Infinite carousel uses card CSS with multiple items
                $css .= $this->getCardCss($cssId, $options);
                break;
        }
        
        // Responsive styles
        if ($options['responsive'] ?? true) {
            $css .= $this->getResponsiveCss($cssId, $options);
        }
        
        $css .= '</style>';
        
        // Minify CSS if option is enabled
        $minify = $options['minify'] ?? false;
        if ($minify) {
            // Extract CSS content (between <style> tags)
            $cssContent = preg_replace('/<style[^>]*>/', '', $css);
            $cssContent = preg_replace('/<\/style>/', '', $cssContent);
            $minified = CssMinifier::minify($cssContent);
            $css = '<style id="carousel-style-' . $this->escape($id) . '">' . $minified . '</style>';
        }
        
        return $css;
    }

    /**
     * Get base CSS
     */
    private function getBaseCss(string $cssId, int $gap, string $transitionDuration): string
    {
        return <<<CSS
{$cssId} {
    position: relative;
    width: 100%;
    margin: 0 auto;
}

{$cssId} .carousel-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

{$cssId}[data-carousel-transition="fade"] .carousel-wrapper {
    min-height: 300px;
}

{$cssId}[data-carousel-type="image"] .carousel-wrapper {
    min-height: 300px;
}

{$cssId} .carousel-track {
    display: flex;
    transition: transform {$transitionDuration} cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform;
}

/* Screen reader only */
{$cssId} .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Loading indicator */
{$cssId} .carousel-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 100;
    pointer-events: none;
}

{$cssId} .carousel-loading.hidden {
    display: none;
}

{$cssId} .carousel-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-top-color: #0066cc;
    border-radius: 50%;
    animation: carousel-spin 0.8s linear infinite;
}

@keyframes carousel-spin {
    to { transform: rotate(360deg); }
}

/* Respect prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
    {$cssId} .carousel-track,
    {$cssId} .carousel-slide,
    {$cssId} .carousel-spinner {
        transition: none !important;
        animation: none !important;
    }
}

{$cssId} .carousel-slide {
    flex: 0 0 100%;
    min-width: 0;
    position: relative;
}

{$cssId}[data-carousel-type="image"] .carousel-slide {
    min-height: 300px;
}

{$cssId}[data-carousel-transition="fade"] .carousel-track {
    position: relative;
}

{$cssId}[data-carousel-transition="fade"] .carousel-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    opacity: 0;
    transition: opacity {$transitionDuration} ease-in-out;
    z-index: 1;
}

{$cssId}[data-carousel-transition="fade"] .carousel-slide.active {
    opacity: 1;
    z-index: 2;
    position: relative;
}

{$cssId}[data-carousel-transition="slide"] .carousel-slide {
    opacity: 1;
}

{$cssId} .carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    color: #333;
}

{$cssId} .carousel-arrow:hover {
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transform: translateY(-50%) scale(1.1);
}

{$cssId} .carousel-arrow:active {
    transform: translateY(-50%) scale(0.95);
}

{$cssId} .carousel-arrow-prev {
    left: 16px;
}

{$cssId} .carousel-arrow-next {
    right: 16px;
}

{$cssId} .carousel-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
    padding: 0;
}

{$cssId} .carousel-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: none;
    background: rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
}

{$cssId} .carousel-dot:hover {
    background: rgba(0, 0, 0, 0.4);
    transform: scale(1.2);
}

{$cssId} .carousel-dot.active {
    background: rgba(0, 0, 0, 0.8);
    width: 24px;
    border-radius: 6px;
}

CSS;
    }

    /**
     * Get image carousel CSS
     */
    private function getImageCss(string $cssId): string
    {
        $options = $this->carousel->getOptions();
        $height = $options['height'] ?? 'auto';
        $minHeight = ($height === 'auto') ? '400px' : $height;
        
        return <<<CSS
{$cssId} .carousel-image-link {
    display: block;
    position: relative;
    width: 100%;
    height: 100%;
}

{$cssId} .carousel-image {
    width: 100%;
    height: {$height};
    display: block;
    object-fit: cover;
    max-width: 100%;
}

{$cssId}[data-carousel-type="image"] .carousel-wrapper {
    min-height: {$minHeight};
}

{$cssId} .carousel-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    color: white;
    padding: 32px 24px 24px;
}

{$cssId} .carousel-title {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 600;
}

{$cssId} .carousel-content {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
}

CSS;
    }

    /**
     * Get card carousel CSS
     */
    private function getCardCss(string $cssId, array $options): string
    {
        $itemsPerSlide = $options['itemsPerSlide'] ?? 3;
        $slideWidth = 100 / $itemsPerSlide;
        
        return <<<CSS
{$cssId} .carousel-track {
    gap: {$options['gap']}px;
}

{$cssId} .carousel-slide {
    flex: 0 0 calc({$slideWidth}% - {$options['gap']}px);
}

{$cssId} .carousel-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

{$cssId} .carousel-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

{$cssId} .carousel-card-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

{$cssId} .carousel-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

{$cssId} .carousel-card:hover .carousel-card-image img {
    transform: scale(1.05);
}

{$cssId} .carousel-card-body {
    padding: 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

{$cssId} .carousel-card-title {
    margin: 0 0 12px 0;
    font-size: 20px;
    font-weight: 600;
    color: #1a1a1a;
}

{$cssId} .carousel-card-content {
    margin: 0 0 16px 0;
    color: #666;
    line-height: 1.6;
    flex: 1;
}

{$cssId} .carousel-card-link {
    color: #0066cc;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

{$cssId} .carousel-card-link:hover {
    color: #0052a3;
    text-decoration: underline;
}

CSS;
    }

    /**
     * Get testimonial carousel CSS
     */
    private function getTestimonialCss(string $cssId): string
    {
        return <<<CSS
{$cssId} .carousel-testimonial {
    text-align: center;
    padding: 48px 24px;
    max-width: 800px;
    margin: 0 auto;
}

{$cssId} .carousel-testimonial-quote {
    margin: 0 0 32px 0;
    font-size: 20px;
    line-height: 1.8;
    color: #333;
    font-style: italic;
}

{$cssId} .carousel-testimonial-quote p {
    margin: 0;
}

{$cssId} .carousel-testimonial-author {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
}

{$cssId} .carousel-testimonial-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
}

{$cssId} .carousel-testimonial-name {
    font-weight: 600;
    font-style: normal;
    color: #1a1a1a;
}

CSS;
    }

    /**
     * Get gallery carousel CSS
     */
    private function getGalleryCss(string $cssId): string
    {
        return <<<CSS
{$cssId} .carousel-gallery-item {
    position: relative;
    width: 100%;
}

{$cssId} .carousel-gallery-image {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
    max-height: 600px;
    margin: 0 auto;
}

{$cssId} .carousel-gallery-caption {
    padding: 16px;
    text-align: center;
    background: rgba(255, 255, 255, 0.95);
}

{$cssId} .carousel-gallery-title {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 600;
}

{$cssId} .carousel-gallery-content {
    margin: 0;
    color: #666;
    font-size: 14px;
}

{$cssId} .carousel-thumbnails {
    display: flex;
    gap: 8px;
    margin-top: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

{$cssId} .carousel-thumbnail {
    border: 2px solid transparent;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    padding: 0;
    background: none;
    transition: all 0.3s ease;
    opacity: 0.6;
}

{$cssId} .carousel-thumbnail:hover {
    opacity: 0.8;
    transform: scale(1.05);
}

{$cssId} .carousel-thumbnail.active {
    border-color: #0066cc;
    opacity: 1;
}

{$cssId} .carousel-thumbnail img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    display: block;
}

CSS;
    }

    /**
     * Get responsive CSS
     */
    private function getResponsiveCss(string $cssId, array $options): string
    {
        $desktopItems = $options['itemsPerSlideDesktop'] ?? $options['itemsPerSlide'] ?? 1;
        $tabletItems = $options['itemsPerSlideTablet'] ?? $options['itemsPerSlide'] ?? 1;
        $mobileItems = $options['itemsPerSlideMobile'] ?? 1;
        
        $desktopWidth = 100 / $desktopItems;
        $tabletWidth = 100 / $tabletItems;
        $mobileWidth = 100 / $mobileItems;
        
        return <<<CSS
@media (max-width: 768px) {
    {$cssId} .carousel-slide {
        flex: 0 0 calc({$mobileWidth}% - {$options['gap']}px);
    }
    
    {$cssId} .carousel-arrow {
        width: 40px;
        height: 40px;
    }
    
    {$cssId} .carousel-arrow-prev {
        left: 8px;
    }
    
    {$cssId} .carousel-arrow-next {
        right: 8px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    {$cssId} .carousel-slide {
        flex: 0 0 calc({$tabletWidth}% - {$options['gap']}px);
    }
}

@media (min-width: 1025px) {
    {$cssId} .carousel-slide {
        flex: 0 0 calc({$desktopWidth}% - {$options['gap']}px);
    }
}

CSS;
    }

    /**
     * Render JavaScript
     */
    public function renderJs(): string
    {
        $id = $this->carousel->getId();
        $options = $this->carousel->getOptions();
        
        // Only render JS once per carousel ID
        if (isset(self::$renderedCarousels[$id . '_js'])) {
            return '';
        }
        self::$renderedCarousels[$id . '_js'] = true;
        
        // Include CarouselAPI and CarouselInstance (only once globally)
        $apiIncluded = isset(self::$renderedCarousels['_api']);
        if (!$apiIncluded) {
            self::$renderedCarousels['_api'] = true;
            $js = '<script id="carousel-api">';
            $js .= CarouselAPI::generate();
            $js .= "\n";
            $js .= CarouselInstance::generate();
            $js .= '</script>';
            $js .= "\n";
        } else {
            $js = '';
        }
        
        $js .= '<script id="carousel-script-' . $this->escape($id) . '">';
        $js .= '(function() {';
        $js .= 'const carousel = document.getElementById("carousel-' . $this->escape($id) . '");';
        $js .= 'if (!carousel) return;';
        
        $js .= $this->getCarouselJs($id, $options);
        
        $js .= '})();';
        $js .= '</script>';
        
        // Minify JS if option is enabled
        $minify = $options['minify'] ?? false;
        if ($minify) {
            // Extract JS content (between <script> tags)
            $jsContent = preg_replace('/<script[^>]*>/', '', $js);
            $jsContent = preg_replace('/<\/script>/', '', $jsContent);
            $minified = JsMinifier::minify($jsContent);
            $js = '<script id="carousel-script-' . $this->escape($id) . '">' . $minified . '</script>';
        }
        
        return $js;
    }

    /**
     * Get carousel JavaScript
     */
    private function getCarouselJs(string $id, array $options): string
    {
        $autoplay = $options['autoplay'] ?? true ? 'true' : 'false';
        $autoplayInterval = $options['autoplayInterval'] ?? 5000;
        $loop = $options['loop'] ?? true ? 'true' : 'false';
        $transition = $options['transition'] ?? 'slide';
        $keyboardNav = $options['keyboardNavigation'] ?? true ? 'true' : 'false';
        $touchSwipe = $options['touchSwipe'] ?? true ? 'true' : 'false';
        
        // Get translations for JavaScript
        $translations = [
            'slide_of' => $this->translator->translate('slide_of', null, ['current' => '{current}', 'total' => '{total}']),
            'image_unavailable' => $this->translator->translate('image_unavailable'),
        ];
        
        // Escape for JavaScript
        $translationsJs = json_encode($translations, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        
        return <<<JS
const carouselId = "{$id}";
const carouselEl = carousel;
const track = carouselEl.querySelector('.carousel-track');
const slides = Array.from(carouselEl.querySelectorAll('.carousel-slide'));
const prevBtn = carouselEl.querySelector('.carousel-arrow-prev');
const nextBtn = carouselEl.querySelector('.carousel-arrow-next');
const dots = Array.from(carouselEl.querySelectorAll('.carousel-dot'));
const thumbnails = Array.from(carouselEl.querySelectorAll('.carousel-thumbnail'));

// Translations
const translations = {$translationsJs};

let currentIndex = 0;
let autoplayTimer = null;
let resizeTimer = null;
let resizeRAF = null;
let imageObserver = null;
const autoplay = {$autoplay};
const autoplayInterval = {$autoplayInterval};
const loop = {$loop};
const transition = "{$transition}";
const keyboardNav = {$keyboardNav};
const touchSwipe = {$touchSwipe};

// Store event handler references for cleanup
const handlePrevClick = () => prevSlide();
const handleNextClick = () => nextSlide();
const handleKeydown = (e) => {
    if (e.key === 'ArrowLeft') {
        prevSlide();
    } else if (e.key === 'ArrowRight') {
        nextSlide();
    }
};
const handleTouchStart = (e) => {
    touchStartX = e.changedTouches[0].screenX;
};
const handleTouchEnd = (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
};
const handleMouseEnter = () => {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
    }
};
const handleMouseLeave = () => {
    resetAutoplay();
};
const handleResize = () => {
    if (resizeRAF) return;
    resizeRAF = requestAnimationFrame(() => {
        updateCarousel();
        resizeRAF = null;
    });
};

let touchStartX = 0;
let touchEndX = 0;

function updateCarousel() {
    slides.forEach((slide, index) => {
        const isActive = index === currentIndex;
        slide.classList.toggle('active', isActive);
        slide.setAttribute('aria-hidden', !isActive);
        if (isActive) {
            slide.setAttribute('aria-current', 'true');
        } else {
            slide.removeAttribute('aria-current');
        }
    });
    
    if (dots.length > 0) {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }
    
    if (thumbnails.length > 0) {
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active', index === currentIndex);
        });
    }
    
    if (track) {
        if (transition === 'slide') {
            const slideWidth = slides[0]?.offsetWidth || 0;
            const gap = parseInt(getComputedStyle(track).gap) || 0;
            const translateX = -(currentIndex * (slideWidth + gap));
            track.style.transform = `translateX(\${translateX}px)`;
        }
        // For fade transition, opacity is handled by CSS
    }
    
    // Announce slide change to screen readers
    const announcement = carouselEl.querySelector('.carousel-announcement');
    if (announcement) {
        const slideText = translations.slide_of
            .replace('{current}', currentIndex + 1)
            .replace('{total}', slides.length);
        announcement.textContent = slideText;
    }
}

function goToSlide(index) {
    if (index < 0) {
        currentIndex = loop ? slides.length - 1 : 0;
    } else if (index >= slides.length) {
        currentIndex = loop ? 0 : slides.length - 1;
    } else {
        currentIndex = index;
    }
    updateCarousel();
    resetAutoplay();
}

function nextSlide() {
    goToSlide(currentIndex + 1);
}

function prevSlide() {
    goToSlide(currentIndex - 1);
}

function resetAutoplay() {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
    }
    if (autoplay) {
        autoplayTimer = setInterval(nextSlide, autoplayInterval);
    }
}

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            nextSlide();
        } else {
            prevSlide();
        }
    }
}

// Navigation buttons
if (prevBtn) {
    prevBtn.addEventListener('click', handlePrevClick);
}

if (nextBtn) {
    nextBtn.addEventListener('click', handleNextClick);
}

// Dots navigation - store handlers for cleanup
const dotHandlers = [];
dots.forEach((dot, index) => {
    const handler = () => goToSlide(index);
    dotHandlers.push({ dot, handler });
    dot.addEventListener('click', handler);
});

// Thumbnails navigation - store handlers for cleanup
const thumbnailHandlers = [];
thumbnails.forEach((thumb, index) => {
    const handler = () => goToSlide(index);
    thumbnailHandlers.push({ thumb, handler });
    thumb.addEventListener('click', handler);
});

// Keyboard navigation
if (keyboardNav) {
    carouselEl.addEventListener('keydown', handleKeydown);
    carouselEl.setAttribute('tabindex', '0');
}

// Touch swipe
if (touchSwipe) {
    carouselEl.addEventListener('touchstart', handleTouchStart);
    carouselEl.addEventListener('touchend', handleTouchEnd);
}

// Pause autoplay on hover
if (autoplay) {
    carouselEl.addEventListener('mouseenter', handleMouseEnter);
    carouselEl.addEventListener('mouseleave', handleMouseLeave);
}

// Lazy loading with Intersection Observer
if (typeof IntersectionObserver !== 'undefined') {
    imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            }
        });
    }, { 
        rootMargin: '50px',
        threshold: 0.01 
    });
    
    // Observe all images with data-src
    const lazyImages = carouselEl.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });
}

// Check for prefers-reduced-motion and disable autoplay if needed
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (prefersReducedMotion && autoplay) {
    autoplay = false;
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
}

// Image error handling
const placeholderImage = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iODAwIiBoZWlnaHQ9IjQwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBub24gZGlzcG9uaWJsZTwvdGV4dD48L3N2Zz4=';
const images = carouselEl.querySelectorAll('img');
const imageUnavailableText = translations.image_unavailable;
images.forEach(img => {
    img.addEventListener('error', function() {
        this.src = placeholderImage;
        this.alt = imageUnavailableText;
        this.setAttribute('aria-label', imageUnavailableText);
    });
});

// Loading indicator management
const loadingEl = carouselEl.querySelector('.carousel-loading');
let loadedCount = 0;
const totalImages = images.length;

if (totalImages === 0 && loadingEl) {
    loadingEl.classList.add('hidden');
} else {
    images.forEach(img => {
        if (img.complete) {
            loadedCount++;
            checkAllLoaded();
        } else {
            img.addEventListener('load', () => {
                loadedCount++;
                checkAllLoaded();
            });
            img.addEventListener('error', () => {
                loadedCount++;
                checkAllLoaded();
            });
        }
    });
}

function checkAllLoaded() {
    if (loadedCount >= totalImages && loadingEl) {
        loadingEl.classList.add('hidden');
        loadingEl.setAttribute('aria-hidden', 'true');
    }
}

// Initialize
updateCarousel();
resetAutoplay();

// Handle window resize with requestAnimationFrame
window.addEventListener('resize', handleResize);

// Cleanup function
function destroy() {
    // Clear timers
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
    if (resizeTimer) {
        clearTimeout(resizeTimer);
        resizeTimer = null;
    }
    if (resizeRAF) {
        cancelAnimationFrame(resizeRAF);
        resizeRAF = null;
    }
    
    // Remove event listeners
    if (prevBtn) {
        prevBtn.removeEventListener('click', handlePrevClick);
    }
    if (nextBtn) {
        nextBtn.removeEventListener('click', handleNextClick);
    }
    
    dotHandlers.forEach(({ dot, handler }) => {
        dot.removeEventListener('click', handler);
    });
    
    thumbnailHandlers.forEach(({ thumb, handler }) => {
        thumb.removeEventListener('click', handler);
    });
    
    if (keyboardNav) {
        carouselEl.removeEventListener('keydown', handleKeydown);
    }
    
    if (touchSwipe) {
        carouselEl.removeEventListener('touchstart', handleTouchStart);
        carouselEl.removeEventListener('touchend', handleTouchEnd);
    }
    
    if (autoplay) {
        carouselEl.removeEventListener('mouseenter', handleMouseEnter);
        carouselEl.removeEventListener('mouseleave', handleMouseLeave);
    }
    
    window.removeEventListener('resize', handleResize);
    
    // Unobserve images
    if (imageObserver) {
        const lazyImages = carouselEl.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            imageObserver.unobserve(img);
        });
        imageObserver.disconnect();
        imageObserver = null;
    }
}

// Expose destroy method and controls (for backward compatibility)
window.carouselInstances = window.carouselInstances || {};
window.carouselInstances[carouselId] = { 
    destroy, 
    goToSlide, 
    nextSlide, 
    prevSlide,
    getCurrentIndex: () => currentIndex,
    resetAutoplay: resetAutoplay
};

// Initialize via CarouselAPI if available
if (typeof window.CarouselAPI !== 'undefined') {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.CarouselAPI.init(carouselId);
        });
    } else {
        window.CarouselAPI.init(carouselId);
    }
}

JS;
    }
}


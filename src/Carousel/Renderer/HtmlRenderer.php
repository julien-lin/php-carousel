<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;
use JulienLinard\Carousel\Exception;
use JulienLinard\Carousel\Translator\TranslatorInterface;
use JulienLinard\Carousel\Validator\UrlValidator;

/**
 * HTML Renderer - renders only HTML structure
 */
class HtmlRenderer extends AbstractRenderer
{
    /**
     * Render HTML structure
     * 
     * @param Carousel $carousel The carousel to render
     * @return string HTML output
     */
    public function render(Carousel $carousel): string
    {
        // Initialize translator from carousel options
        $this->initializeTranslator($carousel);
        
        // Create render context
        $context = new RenderContext($carousel, $this->translator);
        
        return $this->renderHtml($context);
    }

    /**
     * Render HTML structure
     * 
     * @param RenderContext $context Render context
     * @return string HTML output
     */
    private function renderHtml(RenderContext $context): string
    {
        $id = $context->getId();
        $type = $context->getType();
        $items = $context->getItems();
        $options = $context->getOptions();
        
        if (empty($items)) {
            throw new Exception\EmptyCarouselException();
        }
        
        $transition = $options['transition'] ?? 'slide';
        // If customTransition is defined, use 'custom' transition
        if (isset($options['customTransition']) && is_array($options['customTransition'])) {
            $transition = 'custom';
        }
        $theme = $options['theme'] ?? 'auto';
        $hasCustomColors = isset($options['themeColors']) && is_array($options['themeColors']);
        // Only add data-theme attribute if theme is explicitly set (not default 'auto') or custom colors provided
        // This maintains backward compatibility with CarouselRenderer
        $themeAttr = ($theme !== 'auto' || $hasCustomColors) ? ' data-theme="' . $this->escape($theme) . '"' : '';
        $html = '<div class="carousel-container" id="carousel-' . $this->escape($id) . '" data-carousel-id="' . $this->escape($id) . '" data-carousel-type="' . $this->escape($type) . '" data-carousel-transition="' . $this->escape($transition) . '"' . $themeAttr . '>';
        
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
            $html .= $this->renderItem($item, $index, $type, $context);
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
     * 
     * @param CarouselItem $item The item to render
     * @param int $index Item index
     * @param string $type Carousel type
     * @param RenderContext $context Render context
     * @return string HTML output
     */
    private function renderItem(CarouselItem $item, int $index, string $type, RenderContext $context): string
    {
        $items = $context->getItems();
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
                $html .= $this->renderImageItem($item, $index, $context);
                break;
            case Carousel::TYPE_CARD:
                $html .= $this->renderCardItem($item, $index, $context);
                break;
            case Carousel::TYPE_TESTIMONIAL:
                $html .= $this->renderTestimonialItem($item);
                break;
            case Carousel::TYPE_GALLERY:
                $html .= $this->renderGalleryItem($item, $index, $context);
                break;
            case Carousel::TYPE_INFINITE:
                // Infinite carousel uses image rendering
                $html .= $this->renderImageItem($item, $index, $context);
                break;
            default:
                $html .= $this->renderSimpleItem($item);
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Render image item
     * 
     * @param CarouselItem $item The item to render
     * @param int $index Item index
     * @param RenderContext $context Render context
     * @return string HTML output
     */
    private function renderImageItem(CarouselItem $item, int $index, RenderContext $context): string
    {
        $html = '';
        $lazyLoad = $context->getOption('lazyLoad', true);
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
     * 
     * @param CarouselItem $item The item to render
     * @param int $index Item index
     * @param RenderContext $context Render context
     * @return string HTML output
     */
    private function renderCardItem(CarouselItem $item, int $index, RenderContext $context): string
    {
        $html = '<div class="carousel-card">';
        $lazyLoad = $context->getOption('lazyLoad', true);
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
     * 
     * @param CarouselItem $item The item to render
     * @return string HTML output
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
     * 
     * @param CarouselItem $item The item to render
     * @param int $index Item index
     * @param RenderContext $context Render context
     * @return string HTML output
     */
    private function renderGalleryItem(CarouselItem $item, int $index, RenderContext $context): string
    {
        $html = '<div class="carousel-gallery-item">';
        $lazyLoad = $context->getOption('lazyLoad', true);
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
     * 
     * @param CarouselItem $item The item to render
     * @return string HTML output
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
}


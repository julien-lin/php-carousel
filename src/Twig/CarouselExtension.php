<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Twig;

use JulienLinard\Carousel\Carousel;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig Extension for PHP Carousel
 * 
 * Provides Twig functions to create and render carousels
 */
class CarouselExtension extends AbstractExtension
{
    /**
     * Get Twig functions
     * 
     * @return array<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('carousel', [CarouselRuntime::class, 'createCarousel']),
            new TwigFunction('carousel_image', [CarouselRuntime::class, 'createImageCarousel']),
            new TwigFunction('carousel_card', [CarouselRuntime::class, 'createCardCarousel']),
            new TwigFunction('carousel_infinite', [CarouselRuntime::class, 'createInfiniteCarousel']),
            new TwigFunction('carousel_hero', [CarouselRuntime::class, 'createHeroBanner']),
            new TwigFunction('carousel_products', [CarouselRuntime::class, 'createProductShowcase']),
            new TwigFunction('carousel_testimonial', [CarouselRuntime::class, 'createTestimonialCarousel']),
            new TwigFunction('carousel_gallery', [CarouselRuntime::class, 'createGalleryCarousel']),
        ];
    }
}


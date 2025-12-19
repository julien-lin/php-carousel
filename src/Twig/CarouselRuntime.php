<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Twig;

use JulienLinard\Carousel\Carousel;

/**
 * Runtime functions for Twig Carousel Extension
 * 
 * These methods are called by Twig functions to create carousel instances
 */
class CarouselRuntime
{
    /**
     * Create a generic carousel
     * 
     * @param string $id Unique carousel identifier
     * @param string $type Carousel type (image, card, testimonial, gallery, simple, infinite)
     * @param array $items Array of carousel items
     * @param array $options Carousel options
     * @return Carousel
     */
    public static function createCarousel(string $id, string $type, array $items, array $options = []): Carousel
    {
        $carousel = new Carousel($id, $type, $options);
        $carousel->addItems($items);
        return $carousel;
    }
    
    /**
     * Create an image carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $images Array of image URLs or CarouselItem arrays
     * @param array $options Carousel options
     * @return Carousel
     */
    public static function createImageCarousel(string $id, array $images, array $options = []): Carousel
    {
        return Carousel::image($id, $images, $options);
    }
    
    /**
     * Create an infinite scrolling carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $images Array of image URLs or CarouselItem arrays
     * @param array $options Carousel options
     * @return Carousel
     */
    public static function createInfiniteCarousel(string $id, array $images, array $options = []): Carousel
    {
        return Carousel::infiniteCarousel($id, $images, $options);
    }
    
    /**
     * Create a hero banner carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $banners Array of banner data
     * @param array $options Carousel options
     * @return Carousel
     */
    public static function createHeroBanner(string $id, array $banners, array $options = []): Carousel
    {
        return Carousel::heroBanner($id, $banners, $options);
    }
    
    /**
     * Create a product showcase carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $products Array of product data
     * @param array $options Carousel options
     * @return Carousel
     */
    public static function createProductShowcase(string $id, array $products, array $options = []): Carousel
    {
        return Carousel::productShowcase($id, $products, $options);
    }
    
    /**
     * Create a card carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $cards Array of card data
     * @param array $options Carousel options
     * @return Carousel
     */
    public static function createCardCarousel(string $id, array $cards, array $options = []): Carousel
    {
        return Carousel::card($id, $cards, $options);
    }
    
    /**
     * Create a testimonial carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $testimonials Array of testimonial data
     * @param array $options Carousel options
     * @return Carousel
     */
    public static function createTestimonialCarousel(string $id, array $testimonials, array $options = []): Carousel
    {
        return Carousel::testimonial($id, $testimonials, $options);
    }
    
    /**
     * Create a gallery carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $images Array of image URLs or CarouselItem arrays
     * @param array $options Carousel options
     * @return Carousel
     */
    public static function createGalleryCarousel(string $id, array $images, array $options = []): Carousel
    {
        return Carousel::gallery($id, $images, $options);
    }
}


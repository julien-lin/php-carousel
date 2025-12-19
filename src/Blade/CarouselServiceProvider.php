<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Blade;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use JulienLinard\Carousel\Carousel;

/**
 * Laravel Service Provider for PHP Carousel Blade integration
 * 
 * Registers Blade directives and helper functions for carousel creation
 */
class CarouselServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // No services to register
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Directive @carousel (generic)
        Blade::directive('carousel', function ($expression) {
            return "<?php echo \JulienLinard\Carousel\Carousel::image{$expression}->render(); ?>";
        });
        
        // Directive @carousel_image
        Blade::directive('carousel_image', function ($expression) {
            return "<?php echo \JulienLinard\Carousel\Carousel::image{$expression}->render(); ?>";
        });
        
        // Directive @carousel_card
        Blade::directive('carousel_card', function ($expression) {
            return "<?php echo \JulienLinard\Carousel\Carousel::card{$expression}->render(); ?>";
        });
        
        // Directive @carousel_infinite
        Blade::directive('carousel_infinite', function ($expression) {
            return "<?php echo \JulienLinard\Carousel\Carousel::infiniteCarousel{$expression}->render(); ?>";
        });
        
        // Directive @carousel_hero
        Blade::directive('carousel_hero', function ($expression) {
            return "<?php echo \JulienLinard\Carousel\Carousel::heroBanner{$expression}->render(); ?>";
        });
        
        // Directive @carousel_products
        Blade::directive('carousel_products', function ($expression) {
            return "<?php echo \JulienLinard\Carousel\Carousel::productShowcase{$expression}->render(); ?>";
        });
        
        // Directive @carousel_testimonial
        Blade::directive('carousel_testimonial', function ($expression) {
            return "<?php echo \JulienLinard\Carousel\Carousel::testimonial{$expression}->render(); ?>";
        });
        
        // Directive @carousel_gallery
        Blade::directive('carousel_gallery', function ($expression) {
            return "<?php echo \JulienLinard\Carousel\Carousel::gallery{$expression}->render(); ?>";
        });
        
        // Helper functions (only if not already defined)
        $this->registerHelperFunctions();
    }
    
    /**
     * Register helper functions
     */
    private function registerHelperFunctions(): void
    {
        if (!function_exists('carousel')) {
            /**
             * Create a generic carousel
             * 
             * @param string $id Unique carousel identifier
             * @param string $type Carousel type
             * @param array $items Array of carousel items
             * @param array $options Carousel options
             * @return Carousel
             */
            function carousel(string $id, string $type, array $items, array $options = []): Carousel
            {
                $carousel = new Carousel($id, $type, $options);
                $carousel->addItems($items);
                return $carousel;
            }
        }
        
        if (!function_exists('carousel_image')) {
            /**
             * Create an image carousel
             * 
             * @param string $id Unique carousel identifier
             * @param array $images Array of image URLs or CarouselItem arrays
             * @param array $options Carousel options
             * @return Carousel
             */
            function carousel_image(string $id, array $images, array $options = []): Carousel
            {
                return Carousel::image($id, $images, $options);
            }
        }
        
        if (!function_exists('carousel_card')) {
            /**
             * Create a card carousel
             * 
             * @param string $id Unique carousel identifier
             * @param array $cards Array of card data
             * @param array $options Carousel options
             * @return Carousel
             */
            function carousel_card(string $id, array $cards, array $options = []): Carousel
            {
                return Carousel::card($id, $cards, $options);
            }
        }
        
        if (!function_exists('carousel_infinite')) {
            /**
             * Create an infinite scrolling carousel
             * 
             * @param string $id Unique carousel identifier
             * @param array $images Array of image URLs or CarouselItem arrays
             * @param array $options Carousel options
             * @return Carousel
             */
            function carousel_infinite(string $id, array $images, array $options = []): Carousel
            {
                return Carousel::infiniteCarousel($id, $images, $options);
            }
        }
        
        if (!function_exists('carousel_hero')) {
            /**
             * Create a hero banner carousel
             * 
             * @param string $id Unique carousel identifier
             * @param array $banners Array of banner data
             * @param array $options Carousel options
             * @return Carousel
             */
            function carousel_hero(string $id, array $banners, array $options = []): Carousel
            {
                return Carousel::heroBanner($id, $banners, $options);
            }
        }
        
        if (!function_exists('carousel_products')) {
            /**
             * Create a product showcase carousel
             * 
             * @param string $id Unique carousel identifier
             * @param array $products Array of product data
             * @param array $options Carousel options
             * @return Carousel
             */
            function carousel_products(string $id, array $products, array $options = []): Carousel
            {
                return Carousel::productShowcase($id, $products, $options);
            }
        }
        
        if (!function_exists('carousel_testimonial')) {
            /**
             * Create a testimonial carousel
             * 
             * @param string $id Unique carousel identifier
             * @param array $testimonials Array of testimonial data
             * @param array $options Carousel options
             * @return Carousel
             */
            function carousel_testimonial(string $id, array $testimonials, array $options = []): Carousel
            {
                return Carousel::testimonial($id, $testimonials, $options);
            }
        }
        
        if (!function_exists('carousel_gallery')) {
            /**
             * Create a gallery carousel
             * 
             * @param string $id Unique carousel identifier
             * @param array $images Array of image URLs or CarouselItem arrays
             * @param array $options Carousel options
             * @return Carousel
             */
            function carousel_gallery(string $id, array $images, array $options = []): Carousel
            {
                return Carousel::gallery($id, $images, $options);
            }
        }
    }
}


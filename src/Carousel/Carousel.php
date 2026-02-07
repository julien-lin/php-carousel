<?php

declare(strict_types=1);

namespace JulienLinard\Carousel;

use JulienLinard\Carousel\Analytics\AnalyticsInterface;
use JulienLinard\Carousel\Exception\InvalidCarouselTypeException;
use JulienLinard\Carousel\Validator\IdSanitizer;
use JulienLinard\Carousel\Validator\OptionsValidator;

/**
 * Main Carousel class
 * 
 * Creates beautiful, performant carousels with no external dependencies
 */
class Carousel
{
    public const TYPE_IMAGE = 'image';
    public const TYPE_CARD = 'card';
    public const TYPE_TESTIMONIAL = 'testimonial';
    public const TYPE_GALLERY = 'gallery';
    public const TYPE_SIMPLE = 'simple';
    public const TYPE_INFINITE = 'infinite';

    private string $id;
    private string $type;
    private array $items = [];
    private array $options = [];
    private ?\JulienLinard\Carousel\Renderer\CompositeRenderer $renderer = null;
    private ?AnalyticsInterface $analyticsProvider = null;

    public function __construct(
        string $id,
        string $type = self::TYPE_IMAGE,
        array $options = []
    ) {
        // Sanitize ID
        $this->id = IdSanitizer::sanitize($id);
        
        // Validate type
        $validTypes = [
            self::TYPE_IMAGE,
            self::TYPE_CARD,
            self::TYPE_TESTIMONIAL,
            self::TYPE_GALLERY,
            self::TYPE_SIMPLE,
            self::TYPE_INFINITE,
        ];
        
        if (!in_array($type, $validTypes, true)) {
            throw new InvalidCarouselTypeException($type, $validTypes);
        }
        
        $this->type = $type;
        
        // Validate and merge options
        $defaultOptions = $this->getDefaultOptions();
        $validatedOptions = OptionsValidator::validate($options);
        $this->options = array_merge($defaultOptions, $validatedOptions);
        
        // Set analytics provider if provided
        if (isset($options['analyticsProvider']) && $options['analyticsProvider'] instanceof AnalyticsInterface) {
            $this->analyticsProvider = $options['analyticsProvider'];
        }
    }

    /**
     * Add an item to the carousel
     */
    public function addItem(CarouselItem|array $item): self
    {
        $maxItems = (int) ($this->options['maxItems'] ?? 100);
        if (count($this->items) >= $maxItems) {
            throw new \RuntimeException('Maximum ' . $maxItems . ' items allowed per carousel');
        }
        
        if (is_array($item)) {
            $item = CarouselItem::fromArray($item);
        }
        
        $this->items[] = $item;
        return $this;
    }

    /**
     * Add multiple items at once
     */
    public function addItems(array $items): self
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }

    /**
     * Set carousel options
     */
    public function setOptions(array $options): self
    {
        $validatedOptions = OptionsValidator::validate($options);
        $this->options = array_merge($this->options, $validatedOptions);
        return $this;
    }

    /**
     * Get a specific option
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Get or create the renderer instance
     */
    private function getRenderer(): \JulienLinard\Carousel\Renderer\CompositeRenderer
    {
        if ($this->renderer === null) {
            $this->renderer = new \JulienLinard\Carousel\Renderer\CompositeRenderer();
        }
        return $this->renderer;
    }

    /**
     * Render the carousel HTML
     */
    public function render(): string
    {
        return $this->getRenderer()->render($this);
    }

    /**
     * Render only the HTML structure (without CSS/JS)
     */
    public function renderHtml(): string
    {
        return $this->getRenderer()->getHtmlRenderer()->render($this);
    }

    /**
     * Render only the CSS
     */
    public function renderCss(): string
    {
        return $this->getRenderer()->getCssRenderer()->render($this);
    }

    /**
     * Render only the JavaScript
     */
    public function renderJs(): string
    {
        return $this->getRenderer()->getJsRenderer()->render($this);
    }

    /**
     * Render static HTML (SSR - no JavaScript for initial display)
     * 
     * @return string Static HTML with CSS (no JavaScript)
     */
    public function renderStatic(): string
    {
        $html = $this->getRenderer()->getHtmlRenderer()->render($this);
        $css = $this->getRenderer()->getCssRenderer()->render($this);
        
        return $css . "\n" . $html;
    }

    /**
     * Hydrate static HTML with JavaScript for interactivity
     * 
     * @param string $staticHtml Static HTML from renderStatic()
     * @return string HTML with JavaScript hydration
     */
    public function hydrate(string $staticHtml): string
    {
        $js = $this->getRenderer()->getJsRenderer()->render($this);
        
        return $staticHtml . "\n" . $js;
    }

    /**
     * Get carousel ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get carousel type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get all items
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get all options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get analytics provider
     */
    public function getAnalyticsProvider(): ?AnalyticsInterface
    {
        return $this->analyticsProvider;
    }

    /**
     * Set analytics provider
     */
    public function setAnalyticsProvider(?AnalyticsInterface $provider): self
    {
        $this->analyticsProvider = $provider;
        return $this;
    }

    /**
     * Export carousel configuration to array
     * 
     * @return array Configuration array with id, type, items, and options
     */
    public function exportConfig(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            if ($item instanceof CarouselItem) {
                $items[] = $item->toArray();
            } else {
                // If somehow an array got in, keep it as is
                $items[] = $item;
            }
        }
        
        return [
            'id' => $this->id,
            'type' => $this->type,
            'items' => $items,
            'options' => $this->options,
        ];
    }

    /**
     * Create a carousel from exported configuration
     * 
     * @param array $config Configuration array (from exportConfig())
     * @return self New Carousel instance
     * @throws \InvalidArgumentException If configuration is invalid
     */
    public static function fromConfig(array $config): self
    {
        // Validate required fields
        if (!isset($config['id']) || !is_string($config['id'])) {
            throw new \InvalidArgumentException('Configuration must contain a valid "id" field');
        }
        
        if (!isset($config['type']) || !is_string($config['type'])) {
            throw new \InvalidArgumentException('Configuration must contain a valid "type" field');
        }
        
        // Validate type
        $validTypes = [
            self::TYPE_IMAGE,
            self::TYPE_CARD,
            self::TYPE_TESTIMONIAL,
            self::TYPE_GALLERY,
            self::TYPE_SIMPLE,
            self::TYPE_INFINITE,
        ];
        
        if (!in_array($config['type'], $validTypes, true)) {
            throw new InvalidCarouselTypeException($config['type'], $validTypes);
        }
        
        // Create carousel
        $options = $config['options'] ?? [];
        $carousel = new self($config['id'], $config['type'], $options);

        if (isset($config['items']) && is_array($config['items'])) {
            if (count($config['items']) > 1000) {
                throw new \InvalidArgumentException('Too many items in configuration (max 1000)');
            }
            foreach ($config['items'] as $index => $item) {
                if (!is_array($item)) {
                    throw new \InvalidArgumentException('Configuration item at index ' . $index . ' must be an array');
                }
            }
            $carousel->addItems($config['items']);
        }

        return $carousel;
    }

    /**
     * Get default options
     */
    private function getDefaultOptions(): array
    {
        return [
            'autoplay' => true,
            'autoplayInterval' => 5000,
            'loop' => true,
            'showArrows' => true,
            'showDots' => true,
            'showThumbnails' => false,
            'itemsPerSlide' => 1,
            'itemsPerSlideDesktop' => 1,
            'itemsPerSlideTablet' => 1,
            'itemsPerSlideMobile' => 1,
            'gap' => 16,
            'maxItems' => 100, // Limite d'items par carousel (1–10000, configurable via options)
            'theme' => 'auto', // auto, light, dark
            'themeColors' => null, // null = defaults, or ['light' => [...], 'dark' => [...]]
            'transition' => 'slide', // slide, fade, cube
            'transitionDuration' => 500,
            'height' => 'auto',
            'width' => '100%',
            'responsive' => true,
            'lazyLoad' => true,
            'keyboardNavigation' => true,
            'touchSwipe' => true,
            'virtualization' => false, // Enable virtualization for large carousels (50+ items)
            'virtualizationThreshold' => 50, // Minimum items to enable virtualization
            'virtualizationBuffer' => 3, // Number of slides to keep visible on each side
            'customTransition' => null, // Custom transition configuration (see docs)
            'animations' => [], // Custom CSS animations (key => value pairs)
        ];
    }

    /**
     * Create a simple image carousel
     */
    public static function image(string $id, array $images, array $options = []): self
    {
        $carousel = new self($id, self::TYPE_IMAGE, $options);
        
        foreach ($images as $image) {
            if (is_string($image)) {
                $carousel->addItem([
                    'id' => uniqid('img_'),
                    'image' => $image,
                ]);
            } else {
                $carousel->addItem($image);
            }
        }
        
        return $carousel;
    }

    /**
     * Create a card carousel
     */
    public static function card(string $id, array $cards, array $options = []): self
    {
        $carousel = new self($id, self::TYPE_CARD, array_merge([
            'itemsPerSlide' => 3,
            'itemsPerSlideDesktop' => 3,
            'itemsPerSlideTablet' => 2,
            'itemsPerSlideMobile' => 1,
        ], $options));
        
        $carousel->addItems($cards);
        return $carousel;
    }

    /**
     * Create a testimonial carousel
     */
    public static function testimonial(string $id, array $testimonials, array $options = []): self
    {
        $carousel = new self($id, self::TYPE_TESTIMONIAL, array_merge([
            'transition' => 'fade',
        ], $options));
        
        $carousel->addItems($testimonials);
        return $carousel;
    }

    /**
     * Create a gallery carousel
     */
    public static function gallery(string $id, array $images, array $options = []): self
    {
        $carousel = new self($id, self::TYPE_GALLERY, array_merge([
            'showThumbnails' => true,
            'itemsPerSlide' => 1,
        ], $options));
        
        foreach ($images as $image) {
            if (is_string($image)) {
                $carousel->addItem([
                    'id' => uniqid('gallery_'),
                    'image' => $image,
                ]);
            } else {
                $carousel->addItem($image);
            }
        }
        
        return $carousel;
    }

    /**
     * Create an infinite scrolling carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $images Array of image URLs or CarouselItem arrays
     * @param array $options Carousel options
     * @return self
     */
    public static function infiniteCarousel(string $id, array $images, array $options = []): self
    {
        $carousel = new self($id, self::TYPE_INFINITE, array_merge([
            'loop' => true,
            'autoplay' => true,
            'autoplayInterval' => 3000,
            'transition' => 'slide',
            'showDots' => false,
            'itemsPerSlide' => 3,
            'itemsPerSlideDesktop' => 4,
            'itemsPerSlideTablet' => 3,
            'itemsPerSlideMobile' => 2,
        ], $options));
        
        foreach ($images as $image) {
            if (is_string($image)) {
                $carousel->addItem([
                    'id' => uniqid('inf_'),
                    'image' => $image,
                ]);
            } else {
                $carousel->addItem($image);
            }
        }
        
        return $carousel;
    }

    /**
     * Create a hero banner carousel (full-width, large images)
     * 
     * @param string $id Unique carousel identifier
     * @param array $banners Array of banner data
     * @param array $options Carousel options
     * @return self
     */
    public static function heroBanner(string $id, array $banners, array $options = []): self
    {
        $carousel = new self($id, self::TYPE_IMAGE, array_merge([
            'height' => '600px',
            'autoplay' => true,
            'autoplayInterval' => 5000,
            'transition' => 'fade',
            'showDots' => true,
            'showArrows' => true,
            'itemsPerSlide' => 1,
        ], $options));
        
        $carousel->addItems($banners);
        return $carousel;
    }

    /**
     * Create a product showcase carousel
     * 
     * @param string $id Unique carousel identifier
     * @param array $products Array of product data
     * @param array $options Carousel options
     * @return self
     */
    public static function productShowcase(string $id, array $products, array $options = []): self
    {
        $carousel = new self($id, self::TYPE_CARD, array_merge([
            'itemsPerSlide' => 4,
            'itemsPerSlideDesktop' => 4,
            'itemsPerSlideTablet' => 3,
            'itemsPerSlideMobile' => 2,
            'gap' => 20,
            'autoplay' => false,
            'showArrows' => true,
            'showDots' => false,
        ], $options));
        
        $carousel->addItems($products);
        return $carousel;
    }

    /**
     * Create a testimonial slider (alias for testimonial with optimized defaults)
     * 
     * @param string $id Unique carousel identifier
     * @param array $testimonials Array of testimonial data
     * @param array $options Carousel options
     * @return self
     */
    public static function testimonialSlider(string $id, array $testimonials, array $options = []): self
    {
        return self::testimonial($id, $testimonials, array_merge([
            'transition' => 'fade',
            'autoplayInterval' => 6000,
            'showDots' => true,
            'showArrows' => false,
        ], $options));
    }
}


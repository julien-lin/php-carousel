<?php

declare(strict_types=1);

namespace JulienLinard\Carousel;

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

    private string $id;
    private string $type;
    private array $items = [];
    private array $options = [];

    public function __construct(
        string $id,
        string $type = self::TYPE_IMAGE,
        array $options = []
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->options = array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * Add an item to the carousel
     */
    public function addItem(CarouselItem|array $item): self
    {
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
        $this->options = array_merge($this->options, $options);
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
     * Render the carousel HTML
     */
    public function render(): string
    {
        $renderer = new CarouselRenderer($this);
        return $renderer->render();
    }

    /**
     * Render only the HTML structure (without CSS/JS)
     */
    public function renderHtml(): string
    {
        $renderer = new CarouselRenderer($this);
        return $renderer->renderHtml();
    }

    /**
     * Render only the CSS
     */
    public function renderCss(): string
    {
        $renderer = new CarouselRenderer($this);
        return $renderer->renderCss();
    }

    /**
     * Render only the JavaScript
     */
    public function renderJs(): string
    {
        $renderer = new CarouselRenderer($this);
        return $renderer->renderJs();
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
            'transition' => 'slide', // slide, fade, cube
            'transitionDuration' => 500,
            'height' => 'auto',
            'width' => '100%',
            'responsive' => true,
            'lazyLoad' => true,
            'keyboardNavigation' => true,
            'touchSwipe' => true,
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
}


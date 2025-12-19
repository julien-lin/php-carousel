<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;
use JulienLinard\Carousel\Translator\TranslatorInterface;

/**
 * Context object to share carousel data between renderer methods
 * Replaces direct access to $this->carousel in CarouselRenderer
 */
class RenderContext
{
    public function __construct(
        private Carousel $carousel,
        private TranslatorInterface $translator
    ) {}

    /**
     * Get the carousel instance
     * 
     * @return Carousel
     */
    public function getCarousel(): Carousel
    {
        return $this->carousel;
    }

    /**
     * Get the translator instance
     * 
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * Get carousel ID
     * 
     * @return string
     */
    public function getId(): string
    {
        return $this->carousel->getId();
    }

    /**
     * Get carousel type
     * 
     * @return string
     */
    public function getType(): string
    {
        return $this->carousel->getType();
    }

    /**
     * Get carousel items
     * 
     * @return array<CarouselItem>
     */
    public function getItems(): array
    {
        return $this->carousel->getItems();
    }

    /**
     * Get all carousel options
     * 
     * @return array
     */
    public function getOptions(): array
    {
        return $this->carousel->getOptions();
    }

    /**
     * Get a specific option
     * 
     * @param string $key Option key
     * @param mixed $default Default value if option doesn't exist
     * @return mixed
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->carousel->getOption($key, $default);
    }
}


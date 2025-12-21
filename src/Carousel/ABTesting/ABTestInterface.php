<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\ABTesting;

use JulienLinard\Carousel\Carousel;

/**
 * Interface for A/B testing implementations
 */
interface ABTestInterface
{
    /**
     * Get the test ID
     * 
     * @return string Test ID
     */
    public function getTestId(): string;

    /**
     * Get the selected variant ID
     * 
     * @return string Variant ID
     */
    public function getSelectedVariant(): string;

    /**
     * Get the carousel for the selected variant
     * 
     * @return Carousel Carousel instance
     */
    public function getCarousel(): Carousel;

    /**
     * Get all variant IDs
     * 
     * @return array Array of variant IDs
     */
    public function getVariantIds(): array;

    /**
     * Check if a variant is selected
     * 
     * @param string $variantId Variant ID
     * @return bool True if variant is selected
     */
    public function isVariantSelected(string $variantId): bool;
}


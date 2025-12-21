<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Analytics;

/**
 * Interface for analytics providers
 */
interface AnalyticsInterface
{
    /**
     * Track carousel impression (when carousel is displayed)
     * 
     * @param string $carouselId Carousel ID
     * @param int $slideIndex Slide index (0-based)
     * @return void
     */
    public function trackImpression(string $carouselId, int $slideIndex): void;

    /**
     * Track slide click
     * 
     * @param string $carouselId Carousel ID
     * @param int $slideIndex Slide index (0-based)
     * @param string|null $url Clicked URL (if applicable)
     * @return void
     */
    public function trackClick(string $carouselId, int $slideIndex, ?string $url = null): void;

    /**
     * Track custom interaction
     * 
     * @param string $carouselId Carousel ID
     * @param string $event Event name (e.g., 'arrow_click', 'dot_click', 'swipe')
     * @param array $data Additional event data
     * @return void
     */
    public function trackInteraction(string $carouselId, string $event, array $data = []): void;

    /**
     * Get analytics report
     * 
     * @param string $carouselId Carousel ID
     * @param \DateTime|null $startDate Start date (null = all time)
     * @param \DateTime|null $endDate End date (null = all time)
     * @return array Report data
     */
    public function getReport(string $carouselId, ?\DateTime $startDate = null, ?\DateTime $endDate = null): array;
}


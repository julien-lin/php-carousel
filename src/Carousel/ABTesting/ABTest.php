<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\ABTesting;

use JulienLinard\Carousel\Analytics\AnalyticsInterface;
use JulienLinard\Carousel\Carousel;

/**
 * A/B Testing implementation for carousels
 * 
 * Allows testing multiple carousel variants and tracking their performance
 */
class ABTest implements ABTestInterface
{
    private string $testId;
    private array $variants;
    private string $selectedVariant;
    private ?AnalyticsInterface $analytics;
    private string $selectionMethod;
    private ?string $userId;

    /**
     * Selection methods
     */
    public const METHOD_COOKIE = 'cookie';
    public const METHOD_RANDOM = 'random';
    public const METHOD_HASH = 'hash';

    /**
     * Constructor
     * 
     * @param string $testId Test ID (unique identifier)
     * @param array $variants Array of variants ['variant_id' => ['carousel' => Carousel, 'weight' => int]]
     * @param array $options Options ['method' => 'cookie|random|hash', 'userId' => string, 'analytics' => AnalyticsInterface]
     */
    public function __construct(string $testId, array $variants, array $options = [])
    {
        $this->testId = $testId;
        $this->variants = $variants;
        $this->selectionMethod = $options['method'] ?? self::METHOD_COOKIE;
        $this->userId = $options['userId'] ?? null;
        $this->analytics = $options['analytics'] ?? null;

        // Validate variants
        $this->validateVariants();

        // Select variant
        $this->selectedVariant = $this->selectVariant();

        // Track variant selection
        $this->trackVariantSelection();
    }

    /**
     * Get test ID
     */
    public function getTestId(): string
    {
        return $this->testId;
    }

    /**
     * Get selected variant ID
     */
    public function getSelectedVariant(): string
    {
        return $this->selectedVariant;
    }

    /**
     * Get carousel for selected variant
     */
    public function getCarousel(): Carousel
    {
        return $this->variants[$this->selectedVariant]['carousel'];
    }

    /**
     * Get all variant IDs
     */
    public function getVariantIds(): array
    {
        return array_keys($this->variants);
    }

    /**
     * Check if variant is selected
     */
    public function isVariantSelected(string $variantId): bool
    {
        return $this->selectedVariant === $variantId;
    }

    /**
     * Validate variants structure
     */
    private function validateVariants(): void
    {
        if (empty($this->variants)) {
            throw new \InvalidArgumentException('At least one variant is required');
        }

        $totalWeight = 0;
        foreach ($this->variants as $variantId => $variant) {
            if (!isset($variant['carousel']) || !$variant['carousel'] instanceof Carousel) {
                throw new \InvalidArgumentException("Variant '{$variantId}' must have a valid Carousel instance");
            }

            $weight = $variant['weight'] ?? 50;
            if ($weight < 0) {
                throw new \InvalidArgumentException("Variant '{$variantId}' weight must be >= 0");
            }

            $totalWeight += $weight;
        }

        // Normalize weights if total != 100 (allow weights > 100, normalize to 100)
        if ($totalWeight > 0 && $totalWeight != 100) {
            foreach ($this->variants as $variantId => &$variant) {
                $variant['weight'] = (int) round(($variant['weight'] ?? 50) * 100 / $totalWeight);
            }
            unset($variant);
        }
    }

    /**
     * Select variant based on method
     */
    private function selectVariant(): string
    {
        // Try to get from cookie first (if method is cookie)
        if ($this->selectionMethod === self::METHOD_COOKIE) {
            $cookieValue = $_COOKIE['ab_test_' . $this->testId] ?? null;
            if ($cookieValue && isset($this->variants[$cookieValue])) {
                return $cookieValue;
            }
        }

        // Try hash-based selection for consistency
        if ($this->selectionMethod === self::METHOD_HASH && $this->userId !== null) {
            return $this->selectByHash();
        }

        // Random selection based on weights
        return $this->selectByWeight();
    }

    /**
     * Select variant by hash (consistent for same user)
     */
    private function selectByHash(): string
    {
        $hash = md5($this->testId . '_' . $this->userId);
        $hashInt = hexdec(substr($hash, 0, 8));
        $random = ($hashInt % 100) / 100.0;

        $cumulative = 0;
        foreach ($this->variants as $variantId => $variant) {
            $weight = ($variant['weight'] ?? 50) / 100.0;
            $cumulative += $weight;
            if ($random < $cumulative) {
                return $variantId;
            }
        }

        // Fallback to last variant
        return array_key_last($this->variants);
    }

    /**
     * Select variant by weight (random with weights)
     */
    private function selectByWeight(): string
    {
        $random = mt_rand(1, 100);
        $cumulative = 0;

        foreach ($this->variants as $variantId => $variant) {
            $weight = $variant['weight'] ?? 50;
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $variantId;
            }
        }

        // Fallback to last variant
        return array_key_last($this->variants);
    }

    /**
     * Track variant selection
     */
    private function trackVariantSelection(): void
    {
        if ($this->analytics === null) {
            return;
        }

        $carousel = $this->getCarousel();
        $this->analytics->trackInteraction(
            $carousel->getId(),
            'ab_test_variant_selected',
            [
                'test_id' => $this->testId,
                'variant_id' => $this->selectedVariant,
                'method' => $this->selectionMethod,
            ]
        );
    }

    /**
     * Set cookie for variant (to be called in response)
     * 
     * @param int $expiryDays Cookie expiry in days (default: 30)
     * @return void
     */
    public function setCookie(int $expiryDays = 30): void
    {
        if ($this->selectionMethod !== self::METHOD_COOKIE) {
            return;
        }

        $cookieName = 'ab_test_' . $this->testId;
        $expiry = time() + ($expiryDays * 24 * 60 * 60);
        
        setcookie(
            $cookieName,
            $this->selectedVariant,
            $expiry,
            '/',
            '',
            false,
            true // HttpOnly
        );
    }

    /**
     * Get variant statistics from analytics
     * 
     * @param \DateTime|null $startDate Start date
     * @param \DateTime|null $endDate End date
     * @return array Statistics per variant
     */
    public function getVariantStats(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        if ($this->analytics === null) {
            return [];
        }

        $stats = [];
        foreach ($this->variants as $variantId => $variant) {
            $carousel = $variant['carousel'];
            $report = $this->analytics->getReport($carousel->getId(), $startDate, $endDate);
            
            $stats[$variantId] = [
                'impressions' => $report['total_impressions'] ?? 0,
                'clicks' => $report['total_clicks'] ?? 0,
                'ctr' => $report['ctr'] ?? 0.0,
                'most_viewed_slide' => $report['most_viewed_slide'] ?? null,
            ];
        }

        return $stats;
    }
}


<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Accessibility;

/**
 * Accessibility enhancements for WCAG 2.1 AAA compliance
 */
class AccessibilityEnhancer
{
    /**
     * Get enhanced ARIA attributes for carousel container
     * 
     * @param string $id Carousel ID
     * @param int $totalSlides Total number of slides
     * @return array ARIA attributes
     */
    public static function getCarouselAriaAttributes(string $id, int $totalSlides): array
    {
        return [
            'role' => 'region',
            'aria-label' => 'Image carousel',
            'aria-roledescription' => 'carousel',
            'aria-describedby' => 'carousel-description-' . $id,
        ];
    }

    /**
     * Get enhanced ARIA attributes for slide
     * 
     * @param int $index Slide index (0-based)
     * @param int $total Total slides
     * @param bool $isActive Is slide active
     * @return array ARIA attributes
     */
    public static function getSlideAriaAttributes(int $index, int $total, bool $isActive): array
    {
        return [
            'role' => 'group',
            'aria-roledescription' => 'slide',
            'aria-label' => sprintf('Slide %d of %d', $index + 1, $total),
            'aria-hidden' => $isActive ? 'false' : 'true',
            'aria-current' => $isActive ? 'true' : null,
            'aria-posinset' => (string)($index + 1),
            'aria-setsize' => (string)$total,
        ];
    }

    /**
     * Get enhanced ARIA attributes for navigation button
     * 
     * @param string $direction 'prev' or 'next'
     * @param int $currentIndex Current slide index
     * @param int $total Total slides
     * @param bool $loop Is loop enabled
     * @return array ARIA attributes
     */
    public static function getNavigationButtonAriaAttributes(
        string $direction,
        int $currentIndex,
        int $total,
        bool $loop
    ): array {
        $isDisabled = !$loop && (
            ($direction === 'prev' && $currentIndex === 0) ||
            ($direction === 'next' && $currentIndex === $total - 1)
        );

        $label = $direction === 'prev' 
            ? sprintf('Previous slide, currently on slide %d of %d', $currentIndex + 1, $total)
            : sprintf('Next slide, currently on slide %d of %d', $currentIndex + 1, $total);

        return [
            'aria-label' => $label,
            'aria-controls' => 'carousel-track',
            'aria-disabled' => $isDisabled ? 'true' : 'false',
        ];
    }

    /**
     * Get enhanced ARIA attributes for dot navigation
     * 
     * @param int $index Dot index (0-based)
     * @param int $total Total dots
     * @param bool $isActive Is dot active
     * @return array ARIA attributes
     */
    public static function getDotAriaAttributes(int $index, int $total, bool $isActive): array
    {
        return [
            'role' => 'tab',
            'aria-label' => sprintf('Go to slide %d of %d', $index + 1, $total),
            'aria-selected' => $isActive ? 'true' : 'false',
            'aria-controls' => 'carousel-slide-' . $index,
            'tabindex' => $isActive ? '0' : '-1',
        ];
    }

    /**
     * Get enhanced ARIA attributes for tablist (dots container)
     * 
     * @param int $total Total dots
     * @return array ARIA attributes
     */
    public static function getTablistAriaAttributes(int $total): array
    {
        return [
            'role' => 'tablist',
            'aria-label' => 'Slide navigation',
            'aria-orientation' => 'horizontal',
        ];
    }

    /**
     * Calculate color contrast ratio (WCAG)
     * 
     * @param string $color1 First color (hex)
     * @param string $color2 Second color (hex)
     * @return float Contrast ratio (1.0 to 21.0)
     */
    public static function calculateContrastRatio(string $color1, string $color2): float
    {
        $luminance1 = self::getLuminance($color1);
        $luminance2 = self::getLuminance($color2);
        
        $lighter = max($luminance1, $luminance2);
        $darker = min($luminance1, $luminance2);
        
        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Get relative luminance of a color (WCAG formula)
     * 
     * @param string $color Hex color (#RRGGBB)
     * @return float Luminance (0.0 to 1.0)
     */
    private static function getLuminance(string $color): float
    {
        // Remove # if present
        $color = ltrim($color, '#');
        
        // Convert to RGB
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
        
        // Normalize to 0-1
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;
        
        // Apply gamma correction
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * Check if contrast meets WCAG AA standard (4.5:1 for normal text, 3:1 for large text)
     * 
     * @param string $foreground Foreground color
     * @param string $background Background color
     * @param bool $isLargeText Is large text (18pt+ or 14pt+ bold)
     * @return bool True if meets AA standard
     */
    public static function meetsWCAGAA(string $foreground, string $background, bool $isLargeText = false): bool
    {
        $ratio = self::calculateContrastRatio($foreground, $background);
        return $isLargeText ? $ratio >= 3.0 : $ratio >= 4.5;
    }

    /**
     * Check if contrast meets WCAG AAA standard (7:1 for normal text, 4.5:1 for large text)
     * 
     * @param string $foreground Foreground color
     * @param string $background Background color
     * @param bool $isLargeText Is large text (18pt+ or 14pt+ bold)
     * @return bool True if meets AAA standard
     */
    public static function meetsWCAGAAA(string $foreground, string $background, bool $isLargeText = false): bool
    {
        $ratio = self::calculateContrastRatio($foreground, $background);
        return $isLargeText ? $ratio >= 4.5 : $ratio >= 7.0;
    }

    /**
     * Get keyboard shortcuts description
     * 
     * @return string Description of keyboard shortcuts
     */
    public static function getKeyboardShortcutsDescription(): string
    {
        return 'Use arrow keys to navigate slides. Press Escape to stop autoplay.';
    }
}


<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;

/**
 * Tests for accessibility and UX/UI features (Phase 5)
 */
class AccessibilityTest extends TestCase
{
    /**
     * Test ARIA attributes on carousel track
     */
    public function testAriaAttributesOnTrack(): void
    {
        $carousel = Carousel::image('test', ['image1.jpg']);
        $html = $carousel->renderHtml();
        
        // Track should have role="region" and aria-label
        $this->assertStringContainsString('role="region"', $html);
        $this->assertStringContainsString('aria-label="Carousel"', $html);
        $this->assertStringContainsString('aria-live="polite"', $html);
        $this->assertStringContainsString('aria-atomic="true"', $html);
    }

    /**
     * Test ARIA attributes on slides
     */
    public function testAriaAttributesOnSlides(): void
    {
        $carousel = Carousel::image('test', [
            'image1.jpg',
            'image2.jpg',
            'image3.jpg',
        ]);
        $html = $carousel->renderHtml();
        
        // First slide should have aria-hidden="false" and aria-current="true"
        $this->assertStringContainsString('aria-hidden="false"', $html);
        $this->assertStringContainsString('aria-current="true"', $html);
        $this->assertStringContainsString('role="group"', $html);
        $this->assertStringContainsString('aria-roledescription="slide"', $html);
        $this->assertStringContainsString('aria-label="Slide 1 of 3"', $html);
    }

    /**
     * Test ARIA attributes on inactive slides
     */
    public function testAriaAttributesOnInactiveSlides(): void
    {
        $carousel = Carousel::image('test', [
            'image1.jpg',
            'image2.jpg',
        ]);
        $html = $carousel->renderHtml();
        
        // Should contain aria-hidden="true" for inactive slides
        // Count occurrences - first slide is active (false), second is inactive (true)
        $hiddenCount = substr_count($html, 'aria-hidden="true"');
        $this->assertGreaterThan(0, $hiddenCount, 'Should have at least one hidden slide');
        
        // Should have aria-label with slide numbers
        $this->assertStringContainsString('aria-label="Slide 1 of 2"', $html);
        $this->assertStringContainsString('aria-label="Slide 2 of 2"', $html);
    }

    /**
     * Test loading indicator presence
     */
    public function testLoadingIndicatorPresent(): void
    {
        $carousel = Carousel::image('test', ['image1.jpg']);
        $html = $carousel->renderHtml();
        
        // Should have loading indicator
        $this->assertStringContainsString('carousel-loading', $html);
        $this->assertStringContainsString('carousel-spinner', $html);
        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringContainsString('aria-label="Loading carousel"', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
    }

    /**
     * Test screen reader announcement element
     */
    public function testScreenReaderAnnouncement(): void
    {
        $carousel = Carousel::image('test', ['image1.jpg']);
        $html = $carousel->renderHtml();
        
        // Should have announcement element for screen readers
        $this->assertStringContainsString('carousel-announcement', $html);
        $this->assertStringContainsString('sr-only', $html);
        $this->assertStringContainsString('aria-live="polite"', $html);
        $this->assertStringContainsString('aria-atomic="true"', $html);
    }

    /**
     * Test prefers-reduced-motion CSS support
     */
    public function testPrefersReducedMotionSupport(): void
    {
        $uniqueId = 'test-motion-css-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg']);
        $css = $carousel->renderCss();
        
        // Should have prefers-reduced-motion media query
        $this->assertStringContainsString('prefers-reduced-motion', $css);
        $this->assertStringContainsString('transition: none !important', $css);
        $this->assertStringContainsString('animation: none !important', $css);
    }

    /**
     * Test sr-only class in CSS
     */
    public function testSrOnlyClassInCss(): void
    {
        $uniqueId = 'test-sr-only-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg']);
        $css = $carousel->renderCss();
        
        // Should have sr-only class definition
        $this->assertStringContainsString('.sr-only', $css);
        $this->assertStringContainsString('position: absolute', $css);
        $this->assertStringContainsString('width: 1px', $css);
        $this->assertStringContainsString('height: 1px', $css);
        $this->assertStringContainsString('overflow: hidden', $css);
    }

    /**
     * Test loading spinner CSS
     */
    public function testLoadingSpinnerCss(): void
    {
        $uniqueId = 'test-spinner-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg']);
        $css = $carousel->renderCss();
        
        // Should have spinner styles
        $this->assertStringContainsString('carousel-loading', $css);
        $this->assertStringContainsString('carousel-spinner', $css);
        $this->assertStringContainsString('carousel-spin', $css);
        $this->assertStringContainsString('@keyframes', $css);
    }

    /**
     * Test image error handling in JavaScript
     */
    public function testImageErrorHandlingInJs(): void
    {
        $uniqueId = 'test-error-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg']);
        $js = $carousel->renderJs();
        
        // Should have placeholder image for errors
        $this->assertStringContainsString('placeholderImage', $js);
        $this->assertStringContainsString('data:image/svg+xml', $js);
        $this->assertStringContainsString('addEventListener(\'error\'', $js);
        $this->assertStringContainsString('Image unavailable', $js);
        $this->assertStringContainsString('imageUnavailableText', $js);
    }

    /**
     * Test loading indicator management in JavaScript
     */
    public function testLoadingIndicatorManagementInJs(): void
    {
        $uniqueId = 'test-loading-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg']);
        $js = $carousel->renderJs();
        
        // Should have loading indicator management
        $this->assertStringContainsString('carousel-loading', $js);
        $this->assertStringContainsString('checkAllLoaded', $js);
        $this->assertStringContainsString('loadedCount', $js);
        $this->assertStringContainsString('totalImages', $js);
    }

    /**
     * Test prefers-reduced-motion handling in JavaScript
     */
    public function testPrefersReducedMotionInJs(): void
    {
        $uniqueId = 'test-motion-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg']);
        $js = $carousel->renderJs();
        
        // Should check for prefers-reduced-motion
        $this->assertStringContainsString('prefers-reduced-motion', $js);
        $this->assertStringContainsString('matchMedia', $js);
    }

    /**
     * Test ARIA announcement update in JavaScript
     */
    public function testAriaAnnouncementUpdateInJs(): void
    {
        $uniqueId = 'test-announce-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg', 'image2.jpg']);
        $js = $carousel->renderJs();
        
        // Should update announcement element
        $this->assertStringContainsString('carousel-announcement', $js);
        $this->assertStringContainsString('textContent', $js);
        $this->assertStringContainsString('Slide', $js);
    }

    /**
     * Test ARIA attributes update in updateCarousel function
     */
    public function testAriaAttributesUpdateInJs(): void
    {
        $uniqueId = 'test-aria-update-' . uniqid();
        $carousel = Carousel::image($uniqueId, ['image1.jpg', 'image2.jpg']);
        $js = $carousel->renderJs();
        
        // Should update aria-hidden and aria-current
        $this->assertStringContainsString('setAttribute(\'aria-hidden\'', $js);
        $this->assertStringContainsString('setAttribute(\'aria-current\'', $js);
        $this->assertStringContainsString('removeAttribute(\'aria-current\'', $js);
    }

    /**
     * Test navigation buttons have proper ARIA labels
     */
    public function testNavigationButtonsAriaLabels(): void
    {
        $carousel = Carousel::image('test', ['image1.jpg', 'image2.jpg']);
        $html = $carousel->renderHtml();
        
        // Navigation buttons should have aria-label
        $this->assertStringContainsString('aria-label="Previous slide"', $html);
        $this->assertStringContainsString('aria-label="Next slide"', $html);
    }

    /**
     * Test dots navigation has proper ARIA attributes
     */
    public function testDotsNavigationAriaAttributes(): void
    {
        $carousel = Carousel::image('test', ['image1.jpg', 'image2.jpg']);
        $html = $carousel->renderHtml();
        
        // Dots should have role="tablist" and role="tab"
        $this->assertStringContainsString('role="tablist"', $html);
        $this->assertStringContainsString('role="tab"', $html);
        $this->assertStringContainsString('aria-label="Go to slide 1"', $html);
        $this->assertStringContainsString('aria-label="Go to slide 2"', $html);
    }
}


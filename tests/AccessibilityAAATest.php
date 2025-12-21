<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Accessibility\AccessibilityEnhancer;
use JulienLinard\Carousel\Carousel;
use PHPUnit\Framework\TestCase;

/**
 * Tests for WCAG 2.1 AAA accessibility compliance
 */
class AccessibilityAAATest extends TestCase
{
    public function testFocusVisibleStyles(): void
    {
        $carousel = Carousel::image('test-a11y-aaa', ['image1.jpg']);
        $css = $carousel->renderCss();
        
        // Should have focus-visible styles for enhanced accessibility
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('outline:', $css);
        $this->assertStringContainsString('outline-offset:', $css);
    }

    public function testAccessibilityEnhancerCalculatesContrastRatio(): void
    {
        // White on black should have high contrast
        $ratio = AccessibilityEnhancer::calculateContrastRatio('#ffffff', '#000000');
        $this->assertGreaterThan(20.0, $ratio);
        
        // Black on white should have high contrast
        $ratio = AccessibilityEnhancer::calculateContrastRatio('#000000', '#ffffff');
        $this->assertGreaterThan(20.0, $ratio);
    }

    public function testAccessibilityEnhancerMeetsWCAGAA(): void
    {
        // White on black meets AA
        $this->assertTrue(AccessibilityEnhancer::meetsWCAGAA('#ffffff', '#000000'));
        
        // Light gray on white does not meet AA
        $this->assertFalse(AccessibilityEnhancer::meetsWCAGAA('#cccccc', '#ffffff'));
    }

    public function testAccessibilityEnhancerMeetsWCAGAAA(): void
    {
        // White on black meets AAA
        $this->assertTrue(AccessibilityEnhancer::meetsWCAGAAA('#ffffff', '#000000'));
        
        // Medium gray on white does not meet AAA
        $this->assertFalse(AccessibilityEnhancer::meetsWCAGAAA('#888888', '#ffffff'));
    }

    public function testAccessibilityEnhancerGetCarouselAriaAttributes(): void
    {
        $attrs = AccessibilityEnhancer::getCarouselAriaAttributes('test', 5);
        
        $this->assertArrayHasKey('role', $attrs);
        $this->assertArrayHasKey('aria-label', $attrs);
        $this->assertArrayHasKey('aria-roledescription', $attrs);
        $this->assertArrayHasKey('aria-describedby', $attrs);
        $this->assertEquals('region', $attrs['role']);
    }

    public function testAccessibilityEnhancerGetSlideAriaAttributes(): void
    {
        $attrs = AccessibilityEnhancer::getSlideAriaAttributes(0, 5, true);
        
        $this->assertArrayHasKey('role', $attrs);
        $this->assertArrayHasKey('aria-roledescription', $attrs);
        $this->assertArrayHasKey('aria-label', $attrs);
        $this->assertArrayHasKey('aria-hidden', $attrs);
        $this->assertArrayHasKey('aria-current', $attrs);
        $this->assertArrayHasKey('aria-posinset', $attrs);
        $this->assertArrayHasKey('aria-setsize', $attrs);
        $this->assertEquals('false', $attrs['aria-hidden']);
        $this->assertEquals('true', $attrs['aria-current']);
    }

    public function testAccessibilityEnhancerGetNavigationButtonAriaAttributes(): void
    {
        $attrs = AccessibilityEnhancer::getNavigationButtonAriaAttributes('next', 0, 5, true);
        
        $this->assertArrayHasKey('aria-label', $attrs);
        $this->assertArrayHasKey('aria-controls', $attrs);
        $this->assertArrayHasKey('aria-disabled', $attrs);
        $this->assertStringContainsString('Next slide', $attrs['aria-label']);
        $this->assertStringContainsString('currently on slide 1', $attrs['aria-label']);
    }

    public function testAccessibilityEnhancerGetDotAriaAttributes(): void
    {
        $attrs = AccessibilityEnhancer::getDotAriaAttributes(0, 5, true);
        
        $this->assertArrayHasKey('role', $attrs);
        $this->assertArrayHasKey('aria-label', $attrs);
        $this->assertArrayHasKey('aria-selected', $attrs);
        $this->assertArrayHasKey('aria-controls', $attrs);
        $this->assertArrayHasKey('tabindex', $attrs);
        $this->assertEquals('tab', $attrs['role']);
        $this->assertEquals('true', $attrs['aria-selected']);
        $this->assertEquals('0', $attrs['tabindex']);
    }

    public function testAccessibilityEnhancerGetTablistAriaAttributes(): void
    {
        $attrs = AccessibilityEnhancer::getTablistAriaAttributes(5);
        
        $this->assertArrayHasKey('role', $attrs);
        $this->assertArrayHasKey('aria-label', $attrs);
        $this->assertArrayHasKey('aria-orientation', $attrs);
        $this->assertEquals('tablist', $attrs['role']);
        $this->assertEquals('horizontal', $attrs['aria-orientation']);
    }

    public function testKeyboardShortcutsDescription(): void
    {
        $description = AccessibilityEnhancer::getKeyboardShortcutsDescription();
        
        $this->assertNotEmpty($description);
        $this->assertStringContainsString('arrow keys', $description);
        $this->assertStringContainsString('Escape', $description);
    }

    public function testEnhancedAriaAttributesInHtml(): void
    {
        $carousel = Carousel::image('test-aaa', [
            'image1.jpg',
            'image2.jpg',
        ], [
            'showArrows' => true,
            'showDots' => true,
        ]);
        
        $html = $carousel->renderHtml();
        
        // Should have enhanced ARIA attributes
        $this->assertStringContainsString('role="region"', $html);
        $this->assertStringContainsString('aria-label', $html);
        $this->assertStringContainsString('aria-roledescription="slide"', $html);
        $this->assertStringContainsString('role="tablist"', $html);
        $this->assertStringContainsString('role="tab"', $html);
    }

    public function testPrefersReducedMotionRespected(): void
    {
        $carousel = Carousel::image('test-motion-aaa', ['image1.jpg']);
        $css = $carousel->renderCss();
        $js = $carousel->renderJs();
        
        // CSS should respect prefers-reduced-motion
        $this->assertStringContainsString('prefers-reduced-motion', $css);
        $this->assertStringContainsString('transition: none', $css);
        
        // JS should check for prefers-reduced-motion
        $this->assertStringContainsString('prefers-reduced-motion', $js);
        $this->assertStringContainsString('matchMedia', $js);
    }
}


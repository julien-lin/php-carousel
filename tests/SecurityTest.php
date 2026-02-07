<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;
use JulienLinard\Carousel\Exception\InvalidCarouselTypeException;
use JulienLinard\Carousel\Validator\UrlValidator;
use JulienLinard\Carousel\Theme\Theme;
use JulienLinard\Carousel\Analytics\FileAnalytics;
use InvalidArgumentException;

class SecurityTest extends TestCase
{
    /**
     * Test XSS prevention in titles
     */
    public function testXssPreventionInTitle(): void
    {
        $carousel = Carousel::image('test', [
            [
                'id' => '1',
                'title' => '<script>alert("xss")</script>',
                'image' => 'image.jpg',
            ],
        ]);
        
        $html = $carousel->render();
        
        // Should escape script tags
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
    
    /**
     * Test XSS prevention in content
     */
    public function testXssPreventionInContent(): void
    {
        $carousel = Carousel::card('test', [
            [
                'id' => '1',
                'title' => 'Test',
                'content' => '<img src=x onerror=alert(1)>',
                'image' => 'image.jpg',
            ],
        ]);
        
        $html = $carousel->render();
        
        // Should escape HTML in content - check that HTML tags are escaped
        $this->assertStringContainsString('&lt;img', $html);
        // The onerror attribute should be escaped, not present as raw HTML
        $this->assertStringNotContainsString('<img src=x onerror=', $html);
        // Check that the entire tag is escaped
        $this->assertStringContainsString('&gt;', $html);
    }
    
    /**
     * Test XSS prevention with single quotes
     */
    public function testXssPreventionWithSingleQuotes(): void
    {
        $carousel = Carousel::image('test', [
            [
                'id' => '1',
                'title' => "Title' onclick='alert(1)",
                'image' => 'image.jpg',
            ],
        ]);
        
        $html = $carousel->render();
        
        // Should escape single quotes (ENT_QUOTES | ENT_HTML5 uses &#039; or &apos;)
        $this->assertStringNotContainsString("onclick='alert(1)", $html);
        // Check for escaped single quote (can be &#039; or &apos;)
        $this->assertTrue(
            strpos($html, '&#039;') !== false || strpos($html, '&apos;') !== false,
            'Single quote should be escaped'
        );
    }
    
    /**
     * Test URL validation - javascript: scheme
     */
    public function testUrlValidationJavaScriptScheme(): void
    {
        $carousel = Carousel::image('test', [
            [
                'id' => '1',
                'title' => 'Test',
                'image' => 'image.jpg',
                'link' => 'javascript:alert(1)',
            ],
        ]);
        
        $html = $carousel->render();
        
        // Should replace javascript: with #
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringContainsString('href="#"', $html);
    }
    
    /**
     * Test URL validation - data: scheme
     */
    public function testUrlValidationDataScheme(): void
    {
        $carousel = Carousel::card('test', [
            [
                'id' => '1',
                'title' => 'Test',
                'link' => 'data:text/html,<script>alert(1)</script>',
            ],
        ]);
        
        $html = $carousel->render();
        
        // Should replace data: with #
        $this->assertStringNotContainsString('data:', $html);
        $this->assertStringContainsString('href="#"', $html);
    }
    
    /**
     * Test URL validation - valid URLs
     */
    public function testUrlValidationValidUrls(): void
    {
        $validUrls = [
            'https://example.com',
            'http://example.com',
            '/relative/path',
            '../relative/path',
            '#anchor',
        ];
        
        foreach ($validUrls as $url) {
            $carousel = Carousel::image('test', [
                [
                    'id' => '1',
                    'title' => 'Test',
                    'image' => 'image.jpg',
                    'link' => $url,
                ],
            ]);
            
            $html = $carousel->render();
            
            // Should preserve valid URLs (escaped)
            $this->assertStringContainsString('href=', $html);
            // Should not be replaced with #
            $this->assertStringNotContainsString('href="#"', $html, "URL $url should be preserved");
        }
    }
    
    /**
     * Test invalid carousel type
     */
    public function testInvalidCarouselType(): void
    {
        $this->expectException(InvalidCarouselTypeException::class);
        
        new Carousel('test', 'invalid_type');
    }
    
    /**
     * Test options validation - autoplayInterval too low
     */
    public function testOptionsValidationAutoplayIntervalTooLow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('autoplayInterval must be between 1000 and 60000');
        
        new Carousel('test', Carousel::TYPE_IMAGE, [
            'autoplayInterval' => 500,
        ]);
    }
    
    /**
     * Test options validation - autoplayInterval too high
     */
    public function testOptionsValidationAutoplayIntervalTooHigh(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('autoplayInterval must be between 1000 and 60000');
        
        new Carousel('test', Carousel::TYPE_IMAGE, [
            'autoplayInterval' => 70000,
        ]);
    }
    
    /**
     * Test options validation - itemsPerSlide too high
     */
    public function testOptionsValidationItemsPerSlideTooHigh(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('itemsPerSlide must be between 1 and 10');
        
        new Carousel('test', Carousel::TYPE_CARD, [
            'itemsPerSlide' => 15,
        ]);
    }
    
    /**
     * Test options validation - transition invalid
     */
    public function testOptionsValidationInvalidTransition(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('transition must be one of:');
        
        new Carousel('test', Carousel::TYPE_IMAGE, [
            'transition' => 'invalid_transition',
        ]);
    }
    
    /**
     * Test ID sanitization
     */
    public function testIdSanitization(): void
    {
        // ID with special characters
        $carousel = new Carousel('test<script>alert(1)</script>', Carousel::TYPE_IMAGE);
        
        $id = $carousel->getId();
        
        // Should remove HTML tags and special characters
        $this->assertStringNotContainsString('<script>', $id);
        $this->assertStringNotContainsString('</script>', $id);
        $this->assertStringNotContainsString('<', $id);
        $this->assertStringNotContainsString('>', $id);
        $this->assertStringNotContainsString('(', $id);
        $this->assertStringNotContainsString(')', $id);
        // ID should only contain alphanumeric, underscore, and hyphen
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]+$/', $id);
    }
    
    /**
     * Test ID sanitization - empty ID
     */
    public function testIdSanitizationEmptyId(): void
    {
        $carousel = new Carousel('', Carousel::TYPE_IMAGE);
        
        $id = $carousel->getId();
        
        // Should generate a valid ID
        $this->assertNotEmpty($id);
        // Use preg_match for compatibility
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]+$/', $id);
    }
    
    /**
     * Test maximum items limit - can add 100 items
     */
    public function testMaximumItemsLimitCanAdd100(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);
        
        // Add 100 items (should work)
        for ($i = 0; $i < 100; $i++) {
            $carousel->addItem([
                'id' => "item_$i",
                'image' => "image_$i.jpg",
            ]);
        }
        
        $this->assertCount(100, $carousel->getItems());
    }
    
    /**
     * Test maximum items limit - throws exception on 101st item
     */
    public function testMaximumItemsLimitThrowsException(): void
    {
        $carousel = new Carousel('test', Carousel::TYPE_IMAGE);
        
        // Add 100 items
        for ($i = 0; $i < 100; $i++) {
            $carousel->addItem([
                'id' => "item_$i",
                'image' => "image_$i.jpg",
            ]);
        }
        
        // Try to add 101st item (should fail)
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Maximum 100 items allowed per carousel');
        
        $carousel->addItem([
            'id' => 'item_101',
            'image' => 'image_101.jpg',
        ]);
    }
    
    /**
     * Test attributes sanitization
     */
    public function testAttributesSanitization(): void
    {
        $item = new CarouselItem(
            id: 'test',
            title: 'Test',
            attributes: [
                'class' => 'test-class',
                'data-id' => '123',
                'aria-label' => 'Test label',
                'onclick' => 'alert(1)', // Should be removed
                'style' => 'color: red', // Should be removed
            ]
        );
        
        $array = $item->toArray();
        
        // Should keep safe attributes
        $this->assertArrayHasKey('class', $array['attributes']);
        $this->assertArrayHasKey('data-id', $array['attributes']);
        $this->assertArrayHasKey('aria-label', $array['attributes']);
        
        // Should remove unsafe attributes
        $this->assertArrayNotHasKey('onclick', $array['attributes']);
        $this->assertArrayNotHasKey('style', $array['attributes']);
    }
    
    /**
     * Test attributes XSS prevention (whitelist + value escaping)
     */
    public function testAttributesXssPrevention(): void
    {
        $item = new CarouselItem(
            id: 'test',
            title: 'Test',
            attributes: [
                'data-content' => '<script>alert(1)</script>',
            ]
        );
        
        $array = $item->toArray();
        
        // Should escape HTML in attribute values (allowed attribute)
        $this->assertArrayHasKey('data-content', $array['attributes']);
        $this->assertStringNotContainsString('<script>', $array['attributes']['data-content']);
        $this->assertStringContainsString('&lt;script&gt;', $array['attributes']['data-content']);
    }

    public function testUrlValidatorRejectsProtocolRelative(): void
    {
        $this->assertFalse(UrlValidator::isSafe('//evil.com/phishing'));
        $this->assertSame('#', UrlValidator::sanitize('//evil.com'));
    }

    public function testUrlValidatorRejectsDangerousSchemes(): void
    {
        $this->assertFalse(UrlValidator::isSafe('ftp://example.com'));
        $this->assertFalse(UrlValidator::isSafe('ws://example.com'));
        $this->assertFalse(UrlValidator::isSafe('wss://example.com'));
        $this->assertSame('#', UrlValidator::sanitize('javascript:alert(1)'));
    }

    public function testThemeRejectsUnsafeColorValue(): void
    {
        $this->assertFalse(Theme::isSafeColorValue('"); } body { background: url("//evil.com"); } /*'));
        $this->assertFalse(Theme::isSafeColorValue('<script>'));
        $this->assertTrue(Theme::isSafeColorValue('#fff'));
        $this->assertTrue(Theme::isSafeColorValue('rgb(255, 0, 0)'));
        $this->assertTrue(Theme::isSafeColorValue('rgba(0, 0, 0, 0.5)'));
    }

    public function testFileAnalyticsRejectsPathTraversal(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must not contain');
        new FileAnalytics('../../../etc/passwd');
    }
}


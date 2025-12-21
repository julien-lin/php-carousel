<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests\E2E;

use JulienLinard\Carousel\Carousel;

/**
 * End-to-end tests for carousel functionality
 * 
 * These tests verify that the generated HTML/CSS/JS works correctly
 * when rendered in a browser-like environment.
 */
class CarouselE2ETest extends E2ETestBase
{
    public function testBasicCarouselRendering(): void
    {
        $carousel = Carousel::image('e2e-test', [
            'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
            'https://via.placeholder.com/800x400/CC0066/FFFFFF?text=Slide+2',
            'https://via.placeholder.com/800x400/66CC00/FFFFFF?text=Slide+3',
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'basic.html');
        
        // Verify HTML structure
        $this->assertHtmlValid($filepath);
        $this->assertHtmlContains($filepath, 'id="carousel-e2e-test"');
        $this->assertHtmlContains($filepath, 'carousel-slide');
        
        // Verify CSS is present
        $this->assertCssPresent($filepath);
        
        // Verify JavaScript is present
        $this->assertJavaScriptPresent($filepath);
        $this->assertHtmlContains($filepath, 'carousel-script-e2e-test');
    }

    public function testCarouselWithNavigation(): void
    {
        $carousel = Carousel::image('e2e-nav', [
            'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
            'https://via.placeholder.com/800x400/CC0066/FFFFFF?text=Slide+2',
        ], [
            'showArrows' => true,
            'showDots' => true,
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'navigation.html');
        
        // Verify navigation elements
        $this->assertHtmlContains($filepath, 'carousel-arrow-prev');
        $this->assertHtmlContains($filepath, 'carousel-arrow-next');
        $this->assertHtmlContains($filepath, 'carousel-dot');
    }

    public function testCarouselWithAutoplay(): void
    {
        $carousel = Carousel::image('e2e-autoplay', [
            'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
            'https://via.placeholder.com/800x400/CC0066/FFFFFF?text=Slide+2',
        ], [
            'autoplay' => true,
            'autoplayInterval' => 3000,
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'autoplay.html');
        
        // Verify autoplay is configured
        $this->assertHtmlContains($filepath, 'autoplay = true');
        $this->assertHtmlContains($filepath, 'autoplayInterval = 3000');
    }

    public function testCarouselWithTheme(): void
    {
        $carousel = Carousel::image('e2e-theme', [
            'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
        ], [
            'theme' => 'dark',
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'theme.html');
        
        // Verify theme is applied
        $this->assertHtmlContains($filepath, 'data-theme="dark"');
        $this->assertHtmlContains($filepath, '--carousel-background');
    }

    public function testCarouselWithAnalytics(): void
    {
        $carousel = Carousel::image('e2e-analytics', [
            'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
        ], [
            'analytics' => true,
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'analytics.html');
        
        // Verify analytics code is present
        $this->assertHtmlContains($filepath, 'analyticsEnabled = true');
        $this->assertHtmlContains($filepath, 'function trackAnalytics');
    }

    public function testCardCarouselRendering(): void
    {
        $carousel = Carousel::card('e2e-card', [
            [
                'id' => '1',
                'title' => 'Card 1',
                'content' => 'Content 1',
                'image' => 'https://via.placeholder.com/300x200',
                'link' => '/card/1',
            ],
            [
                'id' => '2',
                'title' => 'Card 2',
                'content' => 'Content 2',
                'image' => 'https://via.placeholder.com/300x200',
                'link' => '/card/2',
            ],
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'card.html');
        
        // Verify card structure
        $this->assertHtmlValid($filepath);
        $this->assertHtmlContains($filepath, 'carousel-card');
        $this->assertHtmlContains($filepath, 'Card 1');
        $this->assertHtmlContains($filepath, 'Card 2');
    }

    public function testTestimonialCarouselRendering(): void
    {
        $carousel = Carousel::testimonial('e2e-testimonial', [
            [
                'id' => '1',
                'title' => 'John Doe',
                'content' => 'Great product!',
                'image' => 'https://via.placeholder.com/100x100',
            ],
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'testimonial.html');
        
        // Verify testimonial structure
        $this->assertHtmlValid($filepath);
        $this->assertHtmlContains($filepath, 'carousel-testimonial');
        $this->assertHtmlContains($filepath, 'John Doe');
    }

    public function testGalleryCarouselRendering(): void
    {
        $carousel = Carousel::gallery('e2e-gallery', [
            'https://via.placeholder.com/800x600/0066CC/FFFFFF?text=Image+1',
            'https://via.placeholder.com/800x600/CC0066/FFFFFF?text=Image+2',
        ], [
            'showThumbnails' => true,
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'gallery.html');
        
        // Verify gallery structure
        $this->assertHtmlValid($filepath);
        $this->assertHtmlContains($filepath, 'carousel-gallery');
        $this->assertHtmlContains($filepath, 'carousel-thumbnail');
    }

    public function testMultipleCarouselsOnSamePage(): void
    {
        $carousel1 = Carousel::image('e2e-multi-1', [
            'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
        ]);
        
        $carousel2 = Carousel::card('e2e-multi-2', [
            [
                'id' => '1',
                'title' => 'Card 1',
                'image' => 'https://via.placeholder.com/300x200',
            ],
        ]);

        $filepath = $this->getTestDir() . '/multiple.html';
        
        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Multiple Carousels</title>
    ' . $carousel1->renderCss() . '
    ' . $carousel2->renderCss() . '
</head>
<body>
    ' . $carousel1->renderHtml() . '
    ' . $carousel2->renderHtml() . '
    ' . $carousel1->renderJs() . '
    ' . $carousel2->renderJs() . '
</body>
</html>';

        file_put_contents($filepath, $html);
        
        // Verify both carousels are present
        $this->assertHtmlContains($filepath, 'id="carousel-e2e-multi-1"');
        $this->assertHtmlContains($filepath, 'id="carousel-e2e-multi-2"');
    }

    public function testCarouselWithVirtualization(): void
    {
        // Create carousel with many items to trigger virtualization
        $images = [];
        for ($i = 1; $i <= 60; $i++) {
            $images[] = "https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+{$i}";
        }

        $carousel = Carousel::image('e2e-virtualization', $images, [
            'virtualization' => true,
            'virtualizationThreshold' => 50,
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'virtualization.html');
        
        // Verify virtualization is enabled
        $this->assertHtmlContains($filepath, 'virtualizationEnabled = true');
        $this->assertHtmlContains($filepath, 'virtualizationThreshold = 50');
    }

    public function testCarouselWithCustomAnimations(): void
    {
        $carousel = Carousel::image('e2e-animations', [
            'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
        ], [
            'animations' => [
                'fadeIn' => [
                    'keyframes' => [
                        'name' => 'fade-in',
                        'steps' => [
                            '0%' => ['opacity' => '0'],
                            '100%' => ['opacity' => '1'],
                        ],
                    ],
                    'duration' => '0.5s',
                ],
            ],
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'animations.html');
        
        // Verify animations are present
        $this->assertHtmlContains($filepath, '@keyframes');
        $this->assertHtmlContains($filepath, 'fade-in');
    }

    public function testCarouselAccessibility(): void
    {
        $carousel = Carousel::image('e2e-a11y', [
            'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
        ], [
            'showArrows' => true,
            'showDots' => true,
        ]);

        $filepath = $this->generateHtmlFile($carousel, 'accessibility.html');
        
        // Verify accessibility attributes
        $this->assertHtmlContains($filepath, 'aria-label');
        $this->assertHtmlContains($filepath, 'aria-hidden');
        $this->assertHtmlContains($filepath, 'type="button"');
        $this->assertHtmlContains($filepath, 'role="region"');
    }
}


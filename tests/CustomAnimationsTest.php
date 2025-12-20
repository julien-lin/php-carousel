<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\RenderCacheService;
use PHPUnit\Framework\TestCase;

class CustomAnimationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RenderCacheService::clear();
    }

    public function testCustomTransitionIsApplied(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'customTransition' => [
                'duration' => 600,
                'timingFunction' => 'cubic-bezier(0.4, 0, 0.2, 1)',
                'properties' => ['transform', 'opacity'],
            ],
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $html = $carousel->renderHtml();
        $css = $carousel->renderCss();
        
        // HTML should have data-carousel-transition="custom"
        $this->assertStringContainsString('data-carousel-transition="custom"', $html);
        
        // CSS should contain custom transition
        $this->assertStringContainsString('[data-carousel-transition="custom"]', $css);
        $this->assertStringContainsString('transform 600ms cubic-bezier(0.4, 0, 0.2, 1)', $css);
        $this->assertStringContainsString('opacity 600ms cubic-bezier(0.4, 0, 0.2, 1)', $css);
    }

    public function testCustomTransitionWithDefaultValues(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'customTransition' => [
                'duration' => 800,
            ],
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $css = $carousel->renderCss();
        
        // Should use default timing function and properties
        $this->assertStringContainsString('transform 800ms cubic-bezier(0.4, 0, 0.2, 1)', $css);
    }

    public function testCustomAnimationsWithStringValue(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'animations' => [
                'slideIn' => 'slideInFromRight 0.5s ease-out',
                'slideOut' => 'slideOutToLeft 0.5s ease-in',
            ],
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $css = $carousel->renderCss();
        
        $this->assertStringContainsString('.carousel-animation-slideIn', $css);
        $this->assertStringContainsString('animation: slideInFromRight 0.5s ease-out', $css);
        $this->assertStringContainsString('.carousel-animation-slideOut', $css);
        $this->assertStringContainsString('animation: slideOutToLeft 0.5s ease-in', $css);
    }

    public function testCustomAnimationsWithKeyframesArray(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'animations' => [
                'fadeIn' => [
                    'keyframes' => [
                        'name' => 'carousel-fade-in',
                        'steps' => [
                            '0%' => ['opacity' => '0'],
                            '100%' => ['opacity' => '1'],
                        ],
                    ],
                    'duration' => '0.5s',
                    'timingFunction' => 'ease-out',
                ],
            ],
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $css = $carousel->renderCss();
        
        // Should generate @keyframes
        $this->assertStringContainsString('@keyframes carousel-fade-in', $css);
        $this->assertStringContainsString('0%', $css);
        $this->assertStringContainsString('opacity: 0', $css);
        $this->assertStringContainsString('100%', $css);
        $this->assertStringContainsString('opacity: 1', $css);
        
        // Should generate animation class
        $this->assertStringContainsString('.carousel-animation-fadeIn', $css);
        $this->assertStringContainsString('animation: carousel-fade-in 0.5s ease-out', $css);
    }

    public function testCustomAnimationsWithFullOptions(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'animations' => [
                'bounce' => [
                    'keyframes' => [
                        'name' => 'carousel-bounce',
                        'steps' => [
                            '0%, 100%' => ['transform' => 'translateY(0)'],
                            '50%' => ['transform' => 'translateY(-20px)'],
                        ],
                    ],
                    'duration' => '1s',
                    'timingFunction' => 'ease-in-out',
                    'delay' => '0.2s',
                    'iterationCount' => 'infinite',
                    'direction' => 'alternate',
                ],
            ],
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $css = $carousel->renderCss();
        
        $this->assertStringContainsString('@keyframes carousel-bounce', $css);
        $this->assertStringContainsString('animation: carousel-bounce 1s ease-in-out 0.2s infinite alternate', $css);
    }

    public function testCustomTransitionNotAppliedWhenNotSet(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'transition' => 'slide',
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $html = $carousel->renderHtml();
        $css = $carousel->renderCss();
        
        // Should not have custom transition
        $this->assertStringNotContainsString('data-carousel-transition="custom"', $html);
        $this->assertStringNotContainsString('[data-carousel-transition="custom"]', $css);
    }

    public function testCustomAnimationsNotAppliedWhenEmpty(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'animations' => [],
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $css = $carousel->renderCss();
        
        // Should not contain animation classes
        $this->assertStringNotContainsString('.carousel-animation-', $css);
    }

    public function testCustomTransitionOverridesTransitionOption(): void
    {
        $carousel = new Carousel('test-' . uniqid(), Carousel::TYPE_IMAGE, [
            'transition' => 'fade',
            'customTransition' => [
                'duration' => 600,
                'timingFunction' => 'ease',
                'properties' => ['transform'],
            ],
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $html = $carousel->renderHtml();
        
        // Should use 'custom' instead of 'fade'
        $this->assertStringContainsString('data-carousel-transition="custom"', $html);
        $this->assertStringNotContainsString('data-carousel-transition="fade"', $html);
    }

    public function testCustomAnimationsWorkWithAllCarouselTypes(): void
    {
        $types = [
            Carousel::TYPE_IMAGE,
            Carousel::TYPE_CARD,
            Carousel::TYPE_TESTIMONIAL,
            Carousel::TYPE_GALLERY,
        ];
        
        foreach ($types as $type) {
            $carousel = new Carousel('test-' . uniqid(), $type, [
                'animations' => [
                    'slideIn' => 'slideIn 0.5s ease-out',
                ],
                'items' => [['image' => 'test.jpg']],
            ]);
            $carousel->addItem(['image' => 'test.jpg']);
            
            $css = $carousel->renderCss();
            
            $this->assertStringContainsString('.carousel-animation-slideIn', $css, "Failed for type: {$type}");
        }
    }
}


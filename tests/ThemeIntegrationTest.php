<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\RenderCacheService;
use JulienLinard\Carousel\Theme\Theme;
use PHPUnit\Framework\TestCase;

class ThemeIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RenderCacheService::clear();
    }

    public function testCarouselWithLightTheme(): void
    {
        $carousel = new Carousel('test-light-' . uniqid(), Carousel::TYPE_IMAGE, [
            'theme' => Theme::MODE_LIGHT,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $html = $carousel->render();
        
        $this->assertStringContainsString('data-theme="light"', $html);
        $this->assertStringContainsString('--carousel-background', $html);
    }

    public function testCarouselWithDarkTheme(): void
    {
        $carousel = new Carousel('test-dark-' . uniqid(), Carousel::TYPE_IMAGE, [
            'theme' => Theme::MODE_DARK,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $html = $carousel->render();
        
        $this->assertStringContainsString('data-theme="dark"', $html);
        $this->assertStringContainsString('--carousel-background', $html);
    }

    public function testCarouselWithAutoTheme(): void
    {
        $carousel = new Carousel('test-auto-' . uniqid(), Carousel::TYPE_IMAGE, [
            'theme' => Theme::MODE_AUTO,
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $html = $carousel->render();
        
        // Auto theme should not generate data-theme attribute by default (backward compatibility)
        $this->assertStringNotContainsString('data-theme="auto"', $html);
        // Auto theme should not generate CSS variables by default (backward compatibility)
        $this->assertStringNotContainsString('--carousel-background', $html);
    }

    public function testCarouselWithCustomThemeColors(): void
    {
        $carousel = new Carousel('test-custom-' . uniqid(), Carousel::TYPE_CARD, [
            'theme' => Theme::MODE_LIGHT,
            'themeColors' => [
                'light' => [
                    'background' => '#ff0000',
                    'text' => '#00ff00',
                ],
            ],
            'items' => [['image' => 'test.jpg', 'title' => 'Test']],
        ]);
        $carousel->addItem(['image' => 'test.jpg', 'title' => 'Test']);
        
        $css = $carousel->renderCss();
        
        $this->assertStringContainsString('--carousel-background: #ff0000', $css);
        $this->assertStringContainsString('--carousel-text: #00ff00', $css);
    }

    public function testCarouselThemeWithAutoModeAndCustomColors(): void
    {
        $carousel = new Carousel('test-auto-custom-' . uniqid(), Carousel::TYPE_IMAGE, [
            'theme' => Theme::MODE_AUTO,
            'themeColors' => [
                'light' => [
                    'background' => '#ffffff',
                ],
                'dark' => [
                    'background' => '#000000',
                ],
            ],
            'items' => [['image' => 'test.jpg']],
        ]);
        $carousel->addItem(['image' => 'test.jpg']);
        
        $css = $carousel->renderCss();
        
        // Should generate CSS variables because custom colors are provided
        $this->assertStringContainsString('--carousel-background', $css);
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $css);
    }

    public function testCarouselThemeVariablesUsedInCardCss(): void
    {
        $carousel = new Carousel('test-card-theme-' . uniqid(), Carousel::TYPE_CARD, [
            'theme' => Theme::MODE_LIGHT,
            'themeColors' => [
                'light' => [
                    'cardBackground' => '#f5f5f5',
                    'cardText' => '#333333',
                ],
            ],
            'items' => [['image' => 'test.jpg', 'title' => 'Test']],
        ]);
        $carousel->addItem(['image' => 'test.jpg', 'title' => 'Test']);
        
        $css = $carousel->renderCss();
        
        // Theme is enabled, so CSS variables should be defined
        $this->assertStringContainsString('--carousel-card-background: #f5f5f5', $css);
        $this->assertStringContainsString('--carousel-card-text: #333333', $css);
        // CSS should use variables (theme is enabled)
        $this->assertStringContainsString('var(--carousel-card-background', $css);
        $this->assertStringContainsString('var(--carousel-card-text', $css);
    }
}


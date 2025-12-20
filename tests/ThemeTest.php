<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Theme\Theme;
use PHPUnit\Framework\TestCase;

class ThemeTest extends TestCase
{
    public function testThemeDefaultModeIsAuto(): void
    {
        $theme = new Theme();
        $this->assertEquals(Theme::MODE_AUTO, $theme->getMode());
    }

    public function testThemeCanBeSetToLight(): void
    {
        $theme = new Theme(Theme::MODE_LIGHT);
        $this->assertEquals(Theme::MODE_LIGHT, $theme->getMode());
    }

    public function testThemeCanBeSetToDark(): void
    {
        $theme = new Theme(Theme::MODE_DARK);
        $this->assertEquals(Theme::MODE_DARK, $theme->getMode());
    }

    public function testThemeThrowsExceptionForInvalidMode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Theme('invalid');
    }

    public function testThemeHasDefaultLightColors(): void
    {
        $theme = new Theme();
        $lightColors = $theme->getLightColors();
        
        $this->assertArrayHasKey('background', $lightColors);
        $this->assertArrayHasKey('text', $lightColors);
        $this->assertArrayHasKey('arrow', $lightColors);
        $this->assertArrayHasKey('dot', $lightColors);
        $this->assertEquals('#ffffff', $lightColors['background']);
        $this->assertEquals('#1a1a1a', $lightColors['text']);
    }

    public function testThemeHasDefaultDarkColors(): void
    {
        $theme = new Theme();
        $darkColors = $theme->getDarkColors();
        
        $this->assertArrayHasKey('background', $darkColors);
        $this->assertArrayHasKey('text', $darkColors);
        $this->assertArrayHasKey('arrow', $darkColors);
        $this->assertArrayHasKey('dot', $darkColors);
        $this->assertEquals('#1a1a1a', $darkColors['background']);
        $this->assertEquals('#ffffff', $darkColors['text']);
    }

    public function testThemeCanHaveCustomLightColors(): void
    {
        $customColors = [
            'background' => '#f0f0f0',
            'text' => '#000000',
        ];
        
        $theme = new Theme(Theme::MODE_LIGHT, $customColors);
        $lightColors = $theme->getLightColors();
        
        $this->assertEquals('#f0f0f0', $lightColors['background']);
        $this->assertEquals('#000000', $lightColors['text']);
        // Other colors should still be defaults
        $this->assertEquals('#333333', $lightColors['arrow']);
    }

    public function testThemeCanHaveCustomDarkColors(): void
    {
        $customColors = [
            'background' => '#000000',
            'text' => '#ffffff',
        ];
        
        $theme = new Theme(Theme::MODE_DARK, null, $customColors);
        $darkColors = $theme->getDarkColors();
        
        $this->assertEquals('#000000', $darkColors['background']);
        $this->assertEquals('#ffffff', $darkColors['text']);
        // Other colors should still be defaults
        $this->assertEquals('#ffffff', $darkColors['arrow']);
    }

    public function testThemeGetColorReturnsCorrectValue(): void
    {
        $theme = new Theme();
        
        $this->assertEquals('#ffffff', $theme->getColor('background', false));
        $this->assertEquals('#1a1a1a', $theme->getColor('background', true));
    }

    public function testThemeFromArrayCreatesThemeWithOptions(): void
    {
        $config = [
            'theme' => Theme::MODE_DARK,
            'themeColors' => [
                'light' => [
                    'background' => '#ffffff',
                ],
                'dark' => [
                    'background' => '#000000',
                ],
            ],
        ];
        
        $theme = Theme::fromArray($config);
        
        $this->assertEquals(Theme::MODE_DARK, $theme->getMode());
        $this->assertEquals('#ffffff', $theme->getLightColors()['background']);
        $this->assertEquals('#000000', $theme->getDarkColors()['background']);
    }

    public function testThemeFromArrayWithOnlyThemeMode(): void
    {
        $config = [
            'theme' => Theme::MODE_LIGHT,
        ];
        
        $theme = Theme::fromArray($config);
        
        $this->assertEquals(Theme::MODE_LIGHT, $theme->getMode());
        // Should use default colors
        $this->assertEquals('#ffffff', $theme->getLightColors()['background']);
    }
}


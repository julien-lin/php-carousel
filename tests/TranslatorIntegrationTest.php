<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Translator\ArrayTranslator;

class TranslatorIntegrationTest extends TestCase
{
    /**
     * Test carousel uses English translations by default
     */
    public function testCarouselUsesEnglishTranslationsByDefault(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('Loading carousel', $html);
        $this->assertStringContainsString('Previous slide', $html);
        $this->assertStringContainsString('Next slide', $html);
    }

    /**
     * Test carousel uses French translations when locale is set
     */
    public function testCarouselUsesFrenchTranslationsWhenLocaleIsSet(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg'], [
            'locale' => 'fr',
        ]);
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('Chargement du carousel', $html);
        $this->assertStringContainsString('Slide précédent', $html);
        $this->assertStringContainsString('Slide suivant', $html);
    }

    /**
     * Test carousel uses Spanish translations
     */
    public function testCarouselUsesSpanishTranslations(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg'], [
            'locale' => 'es',
        ]);
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('Cargando carrusel', $html);
        $this->assertStringContainsString('Diapositiva anterior', $html);
    }

    /**
     * Test custom translator can be provided
     */
    public function testCustomTranslatorCanBeProvided(): void
    {
        $translator = new ArrayTranslator([
            'en' => [
                'loading' => 'Custom loading text',
                'previous_slide' => 'Custom previous',
                'next_slide' => 'Custom next',
            ],
        ], 'en');
        
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg'], [
            'translator' => $translator,
        ]);
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('Custom loading text', $html);
        $this->assertStringContainsString('Custom previous', $html);
        $this->assertStringContainsString('Custom next', $html);
    }

    /**
     * Test slide_of translation in HTML
     */
    public function testSlideOfTranslationInHtml(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), [
            'image1.jpg',
            'image2.jpg',
            'image3.jpg',
        ], [
            'locale' => 'en',
        ]);
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('Slide 1 of 3', $html);
        $this->assertStringContainsString('Slide 2 of 3', $html);
        $this->assertStringContainsString('Slide 3 of 3', $html);
    }

    /**
     * Test slide_of translation in French
     */
    public function testSlideOfTranslationInFrench(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), [
            'image1.jpg',
            'image2.jpg',
        ], [
            'locale' => 'fr',
        ]);
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('Slide 1 sur 2', $html);
        $this->assertStringContainsString('Slide 2 sur 2', $html);
    }

    /**
     * Test go_to_slide translation in dots
     */
    public function testGoToSlideTranslationInDots(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), [
            'image1.jpg',
            'image2.jpg',
        ], [
            'locale' => 'en',
            'showDots' => true,
        ]);
        $html = $carousel->renderHtml();
        
        $this->assertStringContainsString('Go to slide 1', $html);
        $this->assertStringContainsString('Go to slide 2', $html);
    }
}


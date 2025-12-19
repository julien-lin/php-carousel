<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Translator\ArrayTranslator;
use JulienLinard\Carousel\Translator\TranslatorInterface;

class TranslatorTest extends TestCase
{
    /**
     * Test translator can be instantiated
     */
    public function testTranslatorCanBeInstantiated(): void
    {
        $translator = new ArrayTranslator();
        $this->assertInstanceOf(TranslatorInterface::class, $translator);
    }

    /**
     * Test default locale is English
     */
    public function testDefaultLocaleIsEnglish(): void
    {
        $translator = new ArrayTranslator();
        $this->assertEquals('en', $translator->getLocale());
    }

    /**
     * Test locale can be set
     */
    public function testLocaleCanBeSet(): void
    {
        $translator = new ArrayTranslator([], 'fr');
        $this->assertEquals('fr', $translator->getLocale());
        
        $translator->setLocale('es');
        $this->assertEquals('es', $translator->getLocale());
    }

    /**
     * Test translation in English
     */
    public function testTranslationInEnglish(): void
    {
        $translator = new ArrayTranslator([], 'en');
        $this->assertEquals('Loading carousel', $translator->translate('loading'));
        $this->assertEquals('Previous slide', $translator->translate('previous_slide'));
        $this->assertEquals('Next slide', $translator->translate('next_slide'));
    }

    /**
     * Test translation in French
     */
    public function testTranslationInFrench(): void
    {
        $translator = new ArrayTranslator([], 'fr');
        $this->assertEquals('Chargement du carousel', $translator->translate('loading'));
        $this->assertEquals('Slide précédent', $translator->translate('previous_slide'));
        $this->assertEquals('Slide suivant', $translator->translate('next_slide'));
    }

    /**
     * Test translation in Spanish
     */
    public function testTranslationInSpanish(): void
    {
        $translator = new ArrayTranslator([], 'es');
        $this->assertEquals('Cargando carrusel', $translator->translate('loading'));
        $this->assertEquals('Diapositiva anterior', $translator->translate('previous_slide'));
    }

    /**
     * Test translation with placeholders
     */
    public function testTranslationWithPlaceholders(): void
    {
        $translator = new ArrayTranslator([], 'en');
        $result = $translator->translate('slide_of', null, ['current' => 2, 'total' => 5]);
        $this->assertEquals('Slide 2 of 5', $result);
    }

    /**
     * Test translation with placeholders in French
     */
    public function testTranslationWithPlaceholdersInFrench(): void
    {
        $translator = new ArrayTranslator([], 'fr');
        $result = $translator->translate('slide_of', null, ['current' => 3, 'total' => 10]);
        $this->assertEquals('Slide 3 sur 10', $result);
    }

    /**
     * Test fallback to English when translation missing
     */
    public function testFallbackToEnglishWhenTranslationMissing(): void
    {
        $translator = new ArrayTranslator([], 'xx'); // Non-existent locale
        // Should fallback to English
        $this->assertEquals('Loading carousel', $translator->translate('loading'));
    }

    /**
     * Test fallback to key when translation completely missing
     */
    public function testFallbackToKeyWhenTranslationCompletelyMissing(): void
    {
        $translator = new ArrayTranslator([], 'en');
        $result = $translator->translate('nonexistent_key');
        $this->assertEquals('nonexistent_key', $result);
    }

    /**
     * Test has method returns true for existing key
     */
    public function testHasMethodReturnsTrueForExistingKey(): void
    {
        $translator = new ArrayTranslator([], 'en');
        $this->assertTrue($translator->has('loading'));
        $this->assertTrue($translator->has('previous_slide'));
    }

    /**
     * Test has method returns false for non-existing key
     */
    public function testHasMethodReturnsFalseForNonExistingKey(): void
    {
        $translator = new ArrayTranslator([], 'en');
        $this->assertFalse($translator->has('nonexistent_key'));
    }

    /**
     * Test custom translations can be added
     */
    public function testCustomTranslationsCanBeAdded(): void
    {
        $translator = new ArrayTranslator([], 'en');
        $translator->addTranslations([
            'en' => [
                'custom_key' => 'Custom translation',
            ],
        ]);
        
        $this->assertTrue($translator->has('custom_key'));
        $this->assertEquals('Custom translation', $translator->translate('custom_key'));
    }

    /**
     * Test translation with specific locale parameter
     */
    public function testTranslationWithSpecificLocaleParameter(): void
    {
        $translator = new ArrayTranslator([], 'en');
        
        // Translate in French even though default is English
        $result = $translator->translate('loading', 'fr');
        $this->assertEquals('Chargement du carousel', $result);
    }

    /**
     * Test multiple languages support
     */
    public function testMultipleLanguagesSupport(): void
    {
        $languages = ['en', 'fr', 'es', 'de', 'it', 'pt', 'nl', 'pl', 'ru', 'ja', 'zh'];
        
        foreach ($languages as $lang) {
            $translator = new ArrayTranslator([], $lang);
            $translation = $translator->translate('loading');
            $this->assertNotEmpty($translation, "Translation for 'loading' in {$lang} should not be empty");
            $this->assertNotEquals('loading', $translation, "Translation for 'loading' in {$lang} should be translated");
        }
    }

    /**
     * Test go_to_slide translation with placeholder
     */
    public function testGoToSlideTranslationWithPlaceholder(): void
    {
        $translator = new ArrayTranslator([], 'en');
        $result = $translator->translate('go_to_slide', null, ['index' => 5]);
        $this->assertEquals('Go to slide 5', $result);
    }

    /**
     * Test image_unavailable translation
     */
    public function testImageUnavailableTranslation(): void
    {
        $translator = new ArrayTranslator([], 'en');
        $this->assertEquals('Image unavailable', $translator->translate('image_unavailable'));
        
        $translator->setLocale('fr');
        $this->assertEquals('Image non disponible', $translator->translate('image_unavailable'));
    }
}


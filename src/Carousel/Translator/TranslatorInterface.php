<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Translator;

/**
 * Interface for carousel translation
 */
interface TranslatorInterface
{
    /**
     * Translate a key
     * 
     * @param string $key Translation key
     * @param string|null $locale Locale (default: current locale)
     * @param array $params Parameters for placeholder replacement
     * @return string Translated string
     */
    public function translate(string $key, ?string $locale = null, array $params = []): string;

    /**
     * Set current locale
     * 
     * @param string $locale Locale code (e.g., 'en', 'fr', 'es')
     * @return void
     */
    public function setLocale(string $locale): void;

    /**
     * Get current locale
     * 
     * @return string Current locale code
     */
    public function getLocale(): string;

    /**
     * Check if a translation key exists
     * 
     * @param string $key Translation key
     * @param string|null $locale Locale (default: current locale)
     * @return bool True if key exists
     */
    public function has(string $key, ?string $locale = null): bool;
}


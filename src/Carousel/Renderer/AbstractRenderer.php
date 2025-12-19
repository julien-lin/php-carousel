<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Translator\TranslatorInterface;
use JulienLinard\Carousel\Translator\ArrayTranslator;

/**
 * Abstract base renderer with common functionality
 */
abstract class AbstractRenderer implements RendererInterface
{
    protected TranslatorInterface $translator;

    public function __construct(?TranslatorInterface $translator = null)
    {
        $this->translator = $translator ?? new ArrayTranslator();
    }

    /**
     * Escape a string for HTML output
     * 
     * @param string $value The value to escape
     * @return string Escaped value
     */
    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Initialize translator from carousel options
     * 
     * @param Carousel $carousel The carousel
     * @return void
     */
    protected function initializeTranslator(Carousel $carousel): void
    {
        $options = $carousel->getOptions();
        
        if (isset($options['translator']) && $options['translator'] instanceof TranslatorInterface) {
            $this->translator = $options['translator'];
        } else {
            $locale = $options['locale'] ?? 'en';
            $this->translator = new ArrayTranslator([], $locale);
        }
        
        if (isset($options['locale'])) {
            $this->translator->setLocale($options['locale']);
        }
    }
}


<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Renderer\AbstractRenderer;
use JulienLinard\Carousel\Renderer\RendererInterface;
use JulienLinard\Carousel\Translator\ArrayTranslator;

/**
 * Test concrete implementation of AbstractRenderer
 */
class TestRenderer extends AbstractRenderer
{
    public function render(\JulienLinard\Carousel\Carousel $carousel): string
    {
        return '';
    }

    // Expose protected method for testing
    public function testEscape(string $value): string
    {
        return $this->escape($value);
    }
}

class AbstractRendererTest extends TestCase
{
    /**
     * Test AbstractRenderer implements RendererInterface
     */
    public function testAbstractRendererImplementsInterface(): void
    {
        $renderer = new TestRenderer();
        $this->assertInstanceOf(RendererInterface::class, $renderer);
    }

    /**
     * Test escape() escapes HTML special characters
     */
    public function testEscapeEscapesHtmlSpecialCharacters(): void
    {
        $renderer = new TestRenderer();
        
        $this->assertEquals('&lt;script&gt;', $renderer->testEscape('<script>'));
        $this->assertEquals('&amp;', $renderer->testEscape('&'));
        $this->assertEquals('&quot;test&quot;', $renderer->testEscape('"test"'));
        // ENT_HTML5 uses &apos; for single quotes
        $this->assertEquals('&apos;test&apos;', $renderer->testEscape("'test'"));
    }

    /**
     * Test escape() handles empty string
     */
    public function testEscapeHandlesEmptyString(): void
    {
        $renderer = new TestRenderer();
        
        $this->assertEquals('', $renderer->testEscape(''));
    }

    /**
     * Test escape() handles normal text
     */
    public function testEscapeHandlesNormalText(): void
    {
        $renderer = new TestRenderer();
        
        $this->assertEquals('Hello World', $renderer->testEscape('Hello World'));
    }

    /**
     * Test escape() uses ENT_QUOTES | ENT_HTML5
     */
    public function testEscapeUsesCorrectFlags(): void
    {
        $renderer = new TestRenderer();
        
        // ENT_QUOTES escapes both single and double quotes
        $this->assertEquals('&quot;test&quot;', $renderer->testEscape('"test"'));
        // ENT_HTML5 uses &apos; for single quotes (not &#039;)
        $this->assertEquals('&apos;test&apos;', $renderer->testEscape("'test'"));
        
        // ENT_HTML5 uses HTML5 entities
        $this->assertEquals('&amp;', $renderer->testEscape('&'));
    }

    /**
     * Test translator is initialized with default
     */
    public function testTranslatorIsInitializedWithDefault(): void
    {
        $renderer = new TestRenderer();
        
        // Use reflection to access protected property
        $reflection = new \ReflectionClass($renderer);
        $property = $reflection->getProperty('translator');
        $property->setAccessible(true);
        
        $this->assertInstanceOf(ArrayTranslator::class, $property->getValue($renderer));
    }

    /**
     * Test translator can be set via constructor
     */
    public function testTranslatorCanBeSetViaConstructor(): void
    {
        $customTranslator = new ArrayTranslator([], 'fr');
        $renderer = new TestRenderer($customTranslator);
        
        // Use reflection to access protected property
        $reflection = new \ReflectionClass($renderer);
        $property = $reflection->getProperty('translator');
        $property->setAccessible(true);
        
        $this->assertSame($customTranslator, $property->getValue($renderer));
    }
}


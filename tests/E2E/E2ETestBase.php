<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests\E2E;

use JulienLinard\Carousel\Carousel;
use PHPUnit\Framework\TestCase;

/**
 * Base class for E2E tests
 * 
 * Provides helper methods for generating test HTML files
 */
abstract class E2ETestBase extends TestCase
{
    private string $testDir;
    private array $generatedFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = sys_get_temp_dir() . '/php-carousel-e2e-' . uniqid();
        mkdir($this->testDir, 0755, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up generated files
        foreach ($this->generatedFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        
        // Remove test directory
        if (is_dir($this->testDir)) {
            $files = glob($this->testDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->testDir);
        }
    }

    /**
     * Generate HTML file with carousel
     * 
     * @param Carousel $carousel Carousel instance
     * @param string $filename Filename (without path)
     * @return string Full path to generated file
     */
    protected function generateHtmlFile(Carousel $carousel, string $filename = 'test.html'): string
    {
        $filepath = $this->testDir . '/' . $filename;
        
        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel E2E Test</title>
    ' . $carousel->renderCss() . '
</head>
<body>
    ' . $carousel->renderHtml() . '
    ' . $carousel->renderJs() . '
</body>
</html>';

        file_put_contents($filepath, $html);
        $this->generatedFiles[] = $filepath;
        
        return $filepath;
    }

    /**
     * Get test directory
     */
    protected function getTestDir(): string
    {
        return $this->testDir;
    }

    /**
     * Assert that HTML file contains expected content
     */
    protected function assertHtmlContains(string $filepath, string $expected): void
    {
        $this->assertFileExists($filepath);
        $content = file_get_contents($filepath);
        $this->assertStringContainsString($expected, $content);
    }

    /**
     * Assert that HTML file is valid
     */
    protected function assertHtmlValid(string $filepath): void
    {
        $this->assertFileExists($filepath);
        $content = file_get_contents($filepath);
        
        // Basic HTML validation
        $this->assertStringContainsString('<!DOCTYPE html>', $content);
        $this->assertStringContainsString('<html', $content);
        $this->assertStringContainsString('</html>', $content);
        $this->assertStringContainsString('<body', $content);
        $this->assertStringContainsString('</body>', $content);
    }

    /**
     * Assert that JavaScript is present and valid
     */
    protected function assertJavaScriptPresent(string $filepath): void
    {
        $content = file_get_contents($filepath);
        
        // Check for script tags
        $this->assertStringContainsString('<script', $content);
        $this->assertStringContainsString('</script>', $content);
        
        // Check for carousel initialization
        $this->assertStringContainsString('carousel', $content);
    }

    /**
     * Assert that CSS is present and valid
     */
    protected function assertCssPresent(string $filepath): void
    {
        $content = file_get_contents($filepath);
        
        // Check for style tags or inline styles
        $hasStyleTag = strpos($content, '<style') !== false;
        $hasInlineStyle = strpos($content, 'style=') !== false;
        
        $this->assertTrue($hasStyleTag || $hasInlineStyle, 'CSS should be present in HTML');
    }
}


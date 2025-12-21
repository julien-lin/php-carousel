<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselRenderer;
use JulienLinard\Carousel\Renderer\JsRenderer;
use JulienLinard\Carousel\Renderer\RenderCacheService;

/**
 * Tests to verify JsRenderer output matches CarouselRenderer output
 */
class JsRendererMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear cache before each test
        RenderCacheService::clear();
    }

    /**
     * Normalize JavaScript whitespace for comparison
     */
    private function normalizeWhitespace(string $js): string
    {
        // Convert all types of whitespace to spaces
        $js = preg_replace('/[ \t]+/', ' ', $js);
        // Reduce multiple blank lines to single
        $js = preg_replace('/\n\s*\n(\s*\n)*/', "\n", $js);
        // Trim each line
        $lines = explode("\n", $js);
        $lines = array_map('trim', $lines);
        return implode("\n", $lines);
    }

    /**
     * Normalize JavaScript by removing new features (virtualization, analytics)
     */
    private function normalizeVirtualization(string $js): string
    {
        // Special handling for minified legacy output that includes window.CarouselAPI and CarouselInstance
        // Legacy minifies API + carousel together, but JsRenderer keeps them separate
        // Also remove CarouselInstance class if present (legacy includes it, new doesn't)
        if (strpos($js, 'class CarouselInstance') !== false) {
            // Find and remove the entire CarouselInstance class
            $classStart = strpos($js, 'class CarouselInstance');
            if ($classStart !== false) {
                // Find opening brace
                $braceStart = strpos($js, '{', $classStart);
                if ($braceStart !== false) {
                    $braceCount = 1;
                    $i = $braceStart + 1;
                    $inString = false;
                    $stringChar = '';
                    
                    while ($i < strlen($js) && $braceCount > 0) {
                        $char = $js[$i];
                        if (!$inString) {
                            if (($char === '"' || $char === "'" || $char === '`') && ($i === 0 || $js[$i - 1] !== '\\')) {
                                $inString = true;
                                $stringChar = $char;
                            } elseif ($char === '{') {
                                $braceCount++;
                            } elseif ($char === '}') {
                                $braceCount--;
                            }
                        } elseif ($char === $stringChar && ($i === 0 || $js[$i - 1] !== '\\')) {
                            $inString = false;
                        }
                        $i++;
                    }
                    
                    // Remove the class
                    $js = substr_replace($js, '', $classStart, $i - $classStart);
                }
            }
        }
        
        if (strpos($js, 'window.CarouselAPI=') !== false) {
            // Find and remove the entire CarouselAPI IIFE
            // Pattern: window.CarouselAPI=(function(){{...}})();class...
            $apiStart = strpos($js, 'window.CarouselAPI=');
            if ($apiStart !== false) {
                // Skip to opening paren after = 
                $openParen = strpos($js, '(', $apiStart);
                if ($openParen !== false) {
                    // Count parens to find matching close for the IIFE expression
                    $parenCount = 1;
                    $i = $openParen + 1;
                    $inString = false;
                    $stringChar = '';

                    while ($i < strlen($js) && $parenCount > 0) {
                        $char = $js[$i];
                        if (!$inString) {
                            if (($char === '"' || $char === "'" || $char === '`') && ($i === 0 || $js[$i - 1] !== '\\')) {
                                $inString = true;
                                $stringChar = $char;
                            } elseif ($char === '(') {
                                $parenCount++;
                            } elseif ($char === ')') {
                                $parenCount--;
                            }
                        } elseif ($char === $stringChar && ($i === 0 || $js[$i - 1] !== '\\')) {
                            $inString = false;
                        }
                        $i++;
                    }

                    // After loop, $i points AFTER the closing ) of the IIFE expression
                    // Now we need to consume (); which immediately follows
                    // Pattern: })();
                    if ($i < strlen($js) && $js[$i] === '(') {
                        $i++; // Skip opening paren of invocation
                    }
                    if ($i < strlen($js) && $js[$i] === ')') {
                        $i++; // Skip closing paren of invocation
                    }
                    // Also consume the semicolon if present
                    if ($i < strlen($js) && $js[$i] === ';') {
                        $i++;
                    }

                    // Remove the entire API declaration
                    $js = substr_replace($js, '', $apiStart, $i - $apiStart);
                }
            }
        }

        // Remove analytics variable declarations
        $js = str_replace("const analyticsEnabled = false;\n", '', $js);
        $js = str_replace('const analyticsEndpoint = "/api/carousel/analytics";' . "\n", '', $js);

        // Remove the entire analytics tracking function block
        $analyticsStart = strpos($js, '// Analytics tracking function');
        if ($analyticsStart !== false) {
            $funcStart = strpos($js, 'function trackAnalytics', $analyticsStart);
            if ($funcStart !== false) {
                // Find end of parameter list
                $parenEnd = strpos($js, ')', $funcStart);
                if ($parenEnd !== false) {
                    // Find opening brace of function body (after params)
                    $braceStart = strpos($js, '{', $parenEnd);
                    if ($braceStart !== false) {
                        $braceCount = 1;
                        $i = $braceStart + 1;
                        $inString = false;
                        $stringChar = '';

                        // Find matching closing brace
                        while ($i < strlen($js) && $braceCount > 0) {
                            $char = $js[$i];
                            if (!$inString) {
                                if ($char === '"' || $char === "'" || $char === '`') {
                                    $inString = true;
                                    $stringChar = $char;
                                } elseif ($char === '{') {
                                    $braceCount++;
                                } elseif ($char === '}') {
                                    $braceCount--;
                                }
                            } elseif ($char === $stringChar && ($i === 0 || $js[$i - 1] !== '\\')) {
                                $inString = false;
                            }
                            $i++;
                        }

                        // Find next newline after closing brace and remove the whole block
                        $nextNewline = strpos($js, "\n", $i);
                        if ($nextNewline !== false) {
                            $js = substr_replace($js, '', $analyticsStart, $nextNewline - $analyticsStart + 1);
                        }
                    }
                }
            }
        }

        // Remove trackAnalytics calls and related analytics code (in proper order)
        // Pattern 1: Remove the whole block of const direction + trackAnalytics FIRST (multi-line)
        $js = preg_replace(
            '/\s*const direction = [^;]+;\s*\n\s*trackAnalytics\s*\([^)]+\);\s*\n/',
            "\n",
            $js
        );

        // Pattern 2: Then remove standalone trackAnalytics(...); calls
        $js = preg_replace('/\s*trackAnalytics\s*\([^)]+\);\s*\n/', "\n", $js);

        // Pattern 3: Then remove orphaned const direction
        $js = preg_replace('/\s*const direction = [^;]+;\s*\n/', '', $js);

        // Remove "// Track" comment lines
        $js = preg_replace('/\/\/ Track [a-z]+\s*\n/', '', $js);

        // Cleanup leftover braces from arrow functions that had trackAnalytics removed
        // Pattern: => { functionCall(); }; becomes => functionCall();
        // Only match handlers with specific function names that were modified by trackAnalytics removal
        $js = preg_replace(
            '/=> *\{\s*((?:prevSlide|nextSlide|goToSlide)\s*\([^)]*\))\s*;\s*\};/',
            '=> $1;',
            $js
        );
        
        // For minified code: handle arrow functions with trackAnalytics removed
        // Pattern: const handlePrevClick=()=>{prevSlide()}; should become const handlePrevClick=()=>prevSlide();
        // Pattern: const handleNextClick=()=>{nextSlide()}; should become const handleNextClick=()=>nextSlide();
        $js = preg_replace(
            '/const handlePrevClick=\(\)=>\{prevSlide\(\)\};/',
            'const handlePrevClick=()=>prevSlide();',
            $js
        );
        $js = preg_replace(
            '/const handleNextClick=\(\)=>\{nextSlide\(\)\};/',
            'const handleNextClick=()=>nextSlide();',
            $js
        );
        
        // Also handle handlers with trackAnalytics that were partially removed
        // Pattern: const handlePrevClick=()=>{trackAnalytics(...);prevSlide()}; should become const handlePrevClick=()=>prevSlide();
        $js = preg_replace(
            '/const handlePrevClick=\(\)=>\{[^}]*prevSlide\(\)\};/',
            'const handlePrevClick=()=>prevSlide();',
            $js
        );
        $js = preg_replace(
            '/const handleNextClick=\(\)=>\{[^}]*nextSlide\(\)\};/',
            'const handleNextClick=()=>nextSlide();',
            $js
        );
        
        // Also handle handlers in dotHandlers: handler=()=>{goToSlide(index)}; should become handler=()=>goToSlide(index);
        $js = preg_replace(
            '/handler=\(\)=>\{goToSlide\(index\)\};/',
            'handler=()=>goToSlide(index);',
            $js
        );

        // Remove minified analytics code
        $js = preg_replace('/const analyticsEnabled=false;/', '', $js);
        $js = preg_replace('/const analyticsEndpoint="[^"]+";/', '', $js);

        // For minified: Remove function trackAnalytics(...) {large block}
        // Must find the parameter list close ) first, then find the brace after it
        $analyticsFuncStart = strpos($js, 'function trackAnalytics(');
        if ($analyticsFuncStart !== false) {
            // Find the closing paren of the parameter list
            $parenClose = strpos($js, ')', $analyticsFuncStart);
            if ($parenClose !== false) {
                // Find opening brace AFTER the parameter list
                $bracePos = strpos($js, '{', $parenClose);
                if ($bracePos !== false) {
                    // Count braces to find the end of the function
                    $braceCount = 1;
                    $i = $bracePos + 1;
                    $inString = false;
                    $stringChar = '';

                    while ($i < strlen($js) && $braceCount > 0) {
                        $char = $js[$i];
                        if (!$inString) {
                            if (($char === '"' || $char === "'" || $char === '`') && ($i === 0 || $js[$i - 1] !== '\\')) {
                                $inString = true;
                                $stringChar = $char;
                            } elseif ($char === '{') {
                                $braceCount++;
                            } elseif ($char === '}') {
                                $braceCount--;
                            }
                        } elseif ($char === $stringChar && ($i === 0 || $js[$i - 1] !== '\\')) {
                            $inString = false;
                        }
                        $i++;
                    }

                    // Remove the function
                    $js = substr_replace($js, '', $analyticsFuncStart, $i - $analyticsFuncStart);
                }
            }
        }

        // Remove minified trackAnalytics calls - more careful to avoid partial matches
        // Remove calls like: trackAnalytics(...); or trackAnalytics(...)
        $js = preg_replace('/;?trackAnalytics\([^)]*\);/', '', $js);
        // Also remove calls not ending in semicolon
        $js = preg_replace('/trackAnalytics\([^)]*\)(?=[}])/', '', $js);
        
        // Normalize arrow functions after analytics removal
        // Pattern: const handlePrevClick=()=>{prevSlide()}; becomes const handlePrevClick=()=>prevSlide();
        $js = preg_replace(
            '/const handlePrevClick=\(\)=>\{prevSlide\(\)\};/',
            'const handlePrevClick=()=>prevSlide();',
            $js
        );
        $js = preg_replace(
            '/const handleNextClick=\(\)=>\{nextSlide\(\)\};/',
            'const handleNextClick=()=>nextSlide();',
            $js
        );
        
        // Normalize handler in dotHandlers: handler=()=>{goToSlide(index)}; becomes handler=()=>goToSlide(index);
        $js = preg_replace(
            '/handler=\(\)=>\{goToSlide\(index\)\};/',
            'handler=()=>goToSlide(index);',
            $js
        );
        
        // Fix handleSwipe: remove const direction=... that was left after analytics removal
        // Pattern: const direction=diff>0?'next':'prev'if(diff>0) should become if(diff>0)
        // In minified code, there's no semicolon: const direction=diff>0?'next':'prev'if(diff>0)
        // We need to match up to 'if(' but not include it
        $js = preg_replace(
            '/const direction=diff>0\?[^i]+if\(/',
            'if(',
            $js
        );
        // Also handle with semicolon: const direction=...;if(
        $js = preg_replace(
            '/const direction=[^;]+;if\(/',
            'if(',
            $js
        );

        // Remove virtualization variable declarations (non-minified)
        $js = preg_replace('/const virtualizationEnabled = [^;]+;\s*/', '', $js);
        $js = preg_replace('/const virtualizationThreshold = [^;]+;\s*/', '', $js);
        $js = preg_replace('/const virtualizationBuffer = [^;]+;\s*/', '', $js);

        // Remove virtualization variable declarations (minified)
        $js = preg_replace('/const virtualizationEnabled=[^;]+;/', '', $js);
        $js = preg_replace('/const virtualizationThreshold=[^;]+;/', '', $js);
        $js = preg_replace('/const virtualizationBuffer=[^;]+;/', '', $js);

        // Remove virtualization logic from updateCarousel function
        $js = preg_replace('/\/\/ Determine if virtualization should be active\s*/', '', $js);
        $js = preg_replace('/const shouldVirtualize = [^;]+;\s*/', '', $js);
        $js = preg_replace('/const shouldVirtualize=[^;]+;/', '', $js);
        $js = preg_replace('/\/\/ Virtualization: hide slides that are too far from current index\s*/', '', $js);

        // Remove the entire if (shouldVirtualize) block using balanced brace matching
        // Pattern: if (shouldVirtualize) { ... nested if/else ... } else { ... }
        // This also handles minified if(shouldVirtualize)
        $pos = 0;
        $found1 = strpos($js, 'if (shouldVirtualize)', $pos);
        $found2 = strpos($js, 'if(shouldVirtualize)', $pos);

        while ($found1 !== false || $found2 !== false) {
            // Use the position that is found first (lowest value)
            if ($found1 !== false && ($found2 === false || $found1 < $found2)) {
                $start = $found1;
            } else {
                $start = $found2;
            }
            $braceCount = 0;
            $inString = false;
            $stringChar = '';
            $i = $start;

            // Find the opening brace
            while ($i < strlen($js) && $js[$i] !== '{') {
                $i++;
            }
            if ($i >= strlen($js)) break;

            $braceStart = $i;
            $braceCount = 1;
            $i++;

            // Find matching closing brace for if block
            while ($i < strlen($js) && $braceCount > 0) {
                if (!$inString) {
                    if ($js[$i] === '"' || $js[$i] === "'") {
                        $inString = true;
                        $stringChar = $js[$i];
                    } elseif ($js[$i] === '{') {
                        $braceCount++;
                    } elseif ($js[$i] === '}') {
                        $braceCount--;
                    }
                } elseif ($js[$i] === $stringChar && $js[$i - 1] !== '\\') {
                    $inString = false;
                }
                $i++;
            }

            // Now find the else block
            $elsePos = strpos($js, 'else', $i);
            if ($elsePos !== false) {
                // Skip to opening brace of else
                $i = $elsePos;
                while ($i < strlen($js) && $js[$i] !== '{') {
                    $i++;
                }
                if ($i < strlen($js)) {
                    $braceCount = 1;
                    $i++;
                    while ($i < strlen($js) && $braceCount > 0) {
                        if (!$inString) {
                            if ($js[$i] === '"' || $js[$i] === "'") {
                                $inString = true;
                                $stringChar = $js[$i];
                            } elseif ($js[$i] === '{') {
                                $braceCount++;
                            } elseif ($js[$i] === '}') {
                                $braceCount--;
                            }
                        } elseif ($js[$i] === $stringChar && $js[$i - 1] !== '\\') {
                            $inString = false;
                        }
                        $i++;
                    }
                }
            }

            // Remove the entire block
            $js = substr_replace($js, '', $start, $i - $start);

            // Search for next occurrence
            $found1 = strpos($js, 'if (shouldVirtualize)', $start);
            $found2 = strpos($js, 'if(shouldVirtualize)', $start);
        }

        // Clean up leftover blank lines created by removal
        $js = preg_replace('/\n\s*\n\s*\n/', "\n\n", $js);
        $js = preg_replace('/\n\s+\n/', "\n\n", $js);

        // Normalize whitespace around braces and statements
        $js = preg_replace('/\n\s+\n/', "\n\n", $js);
        $js = preg_replace('/\s*\n\s*\n\s+/', "\n\n", $js);

        // Clean up any leftover orphaned braces and comments
        $js = preg_replace('/\}\s*\}\s*else\s*\{[^}]*\}\s*/', '', $js);
        $js = preg_replace('/\}\s*else\s*\{[^}]*\/\/ Ensure all slides are visible[^}]*\}\s*/', '', $js);

        // Final normalization of spaces and newlines for consistency
        $js = preg_replace('/\n    \n/', "\n\n", $js);

        return $js;
    }

    /**
     * Test JS output is identical for image carousel
     * Note: We compare only the carousel-specific script, not the API (which is included once globally)
     */
    public function testJsOutputIdenticalForImageCarousel(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);

        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        // Extract only the carousel-specific script (not the API)
        $legacyJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $legacyJs);

        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        // Extract only the carousel-specific script (not the API)
        $newJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $newJs);

        // Normalize: remove virtualization code (new feature, not in legacy renderer)
        $newJs = $this->normalizeVirtualization($newJs);

        // Normalize whitespace for comparison
        $legacyJs = $this->normalizeWhitespace($legacyJs);
        $newJs = $this->normalizeWhitespace($newJs);

        $this->assertEquals($legacyJs, $newJs);
    }

    /**
     * Test JS output is identical for card carousel
     */
    public function testJsOutputIdenticalForCardCarousel(): void
    {
        $carousel = Carousel::card('test-' . uniqid(), [
            ['id' => '1', 'title' => 'Card 1', 'content' => 'Content 1', 'image' => 'card1.jpg'],
        ]);

        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        $legacyJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $legacyJs);

        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        $newJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $newJs);

        // Normalize: remove virtualization code (new feature, not in legacy renderer)
        $newJs = $this->normalizeVirtualization($newJs);

        // Normalize whitespace for robust comparison
        $legacyJs = $this->normalizeWhitespace($legacyJs);
        $newJs = $this->normalizeWhitespace($newJs);

        $this->assertEquals($legacyJs, $newJs);
    }

    /**
     * Test JS output is identical with options
     */
    public function testJsOutputIdenticalWithOptions(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions([
            'autoplay' => false,
            'autoplayInterval' => 3000,
            'loop' => false,
            'transition' => 'fade',
            'keyboardNavigation' => false,
            'touchSwipe' => false,
        ]);

        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        $legacyJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $legacyJs);

        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        $newJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $newJs);

        // Normalize: remove virtualization code (new feature, not in legacy renderer)
        $newJs = $this->normalizeVirtualization($newJs);

        // Normalize whitespace for robust comparison
        $legacyJs = $this->normalizeWhitespace($legacyJs);
        $newJs = $this->normalizeWhitespace($newJs);

        $this->assertEquals($legacyJs, $newJs);
    }

    /**
     * Test JS output is identical with minification
     */
    public function testJsOutputIdenticalWithMinification(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions(['minify' => true]);

        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();

        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);

        // Extract only carousel-specific script (not API) from both
        preg_match('/<script id="carousel-script-[^"]+">(.*?)<\/script>/s', $legacyJs, $legacyMatches);
        preg_match('/<script id="carousel-script-[^"]+">(.*?)<\/script>/s', $newJs, $newMatches);

        $this->assertNotEmpty($legacyMatches[1] ?? null, 'Legacy JS should contain carousel script');
        $this->assertNotEmpty($newMatches[1] ?? null, 'New JS should contain carousel script');

        // Normalize both: legacy may have API included due to minification, new has virtualization code
        $normalizedLegacy = $this->normalizeVirtualization($legacyMatches[1] ?? '');
        $normalizedNew = $this->normalizeVirtualization($newMatches[1] ?? '');

        // Normalize whitespace before comparison
        $legacyCode = $this->normalizeWhitespace($normalizedLegacy);
        $newCode = $this->normalizeWhitespace($normalizedNew);

        // Compare minified carousel scripts (they should be identical after normalization)
        $this->assertEquals($legacyCode, $newCode);
    }

    /**
     * Test JS output contains CarouselAPI
     */
    public function testJsOutputContainsCarouselApi(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);

        $newRenderer = new JsRenderer();
        $js = $newRenderer->render($carousel);

        $this->assertStringContainsString('window.CarouselAPI', $js);
        $this->assertStringContainsString('CarouselInstance', $js);
    }

    /**
     * Test JS cache works correctly
     */
    public function testJsCacheWorksCorrectly(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);

        $newRenderer = new JsRenderer();
        $firstJs = $newRenderer->render($carousel);

        // Second render should return empty string (cached)
        $secondJs = $newRenderer->render($carousel);

        $this->assertNotEmpty($firstJs);
        $this->assertEmpty($secondJs);
    }

    /**
     * Test JS API is included only once globally
     */
    public function testJsApiIncludedOnlyOnceGlobally(): void
    {
        $carousel1 = Carousel::image('test1-' . uniqid(), ['image1.jpg']);
        $carousel2 = Carousel::image('test2-' . uniqid(), ['image2.jpg']);

        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $js1 = $newRenderer->render($carousel1);
        $js2 = $newRenderer->render($carousel2);

        // First render should include API
        $this->assertStringContainsString('<script id="carousel-api">', $js1);
        $this->assertStringContainsString('window.CarouselAPI', $js1);

        // Second render should NOT include API (already included)
        $this->assertStringNotContainsString('<script id="carousel-api">', $js2);
        $this->assertStringContainsString('carousel-script-', $js2); // But should still have carousel script
    }

    /**
     * Test JS output with different locales
     */
    public function testJsOutputWithDifferentLocales(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);
        $carousel->setOptions(['locale' => 'fr']);

        $legacyRenderer = new CarouselRenderer($carousel);
        $legacyJs = $legacyRenderer->renderJs();
        $legacyJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $legacyJs);

        RenderCacheService::clear();
        $newRenderer = new JsRenderer();
        $newJs = $newRenderer->render($carousel);
        $newJs = preg_replace('/<script id="carousel-api">.*?<\/script>\s*/s', '', $newJs);

        // Normalize: remove virtualization code (new feature, not in legacy renderer)
        $newJs = $this->normalizeVirtualization($newJs);

        // Normalize whitespace for robust comparison
        $legacyJs = $this->normalizeWhitespace($legacyJs);
        $newJs = $this->normalizeWhitespace($newJs);

        $this->assertEquals($legacyJs, $newJs);
        // Verify French translation is present
        $this->assertStringContainsString('sur', $newJs); // "sur" is in "Slide {current} sur {total}"
    }

    /**
     * Test JS output contains all required functions
     */
    public function testJsOutputContainsAllRequiredFunctions(): void
    {
        $carousel = Carousel::image('test-' . uniqid(), ['image1.jpg']);

        $newRenderer = new JsRenderer();
        $js = $newRenderer->render($carousel);

        $this->assertStringContainsString('function goToSlide', $js);
        $this->assertStringContainsString('function nextSlide', $js);
        $this->assertStringContainsString('function prevSlide', $js);
        $this->assertStringContainsString('function destroy', $js);
        $this->assertStringContainsString('function updateCarousel', $js);
        $this->assertStringContainsString('function resetAutoplay', $js);
    }
}

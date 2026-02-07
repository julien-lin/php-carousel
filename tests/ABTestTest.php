<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\ABTesting\ABTest;
use JulienLinard\Carousel\Analytics\FileAnalytics;
use JulienLinard\Carousel\Carousel;
use PHPUnit\Framework\TestCase;

class ABTestTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/php-carousel-abtest-' . uniqid();
        
        // Clear cookies
        $_COOKIE = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_COOKIE = [];
        if (session_status() === \PHP_SESSION_ACTIVE && isset($_SESSION['carousel_variant_test-cookie'])) {
            unset($_SESSION['carousel_variant_test-cookie']);
        }
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->tempDir);
        }
    }

    public function testCreateABTestWithTwoVariants(): void
    {
        $carouselA = Carousel::image('test-a', ['image1.jpg']);
        $carouselB = Carousel::image('test-b', ['image2.jpg']);

        $test = new ABTest('test-1', [
            'variant_a' => ['carousel' => $carouselA, 'weight' => 50],
            'variant_b' => ['carousel' => $carouselB, 'weight' => 50],
        ]);

        $this->assertEquals('test-1', $test->getTestId());
        $this->assertContains($test->getSelectedVariant(), ['variant_a', 'variant_b']);
        $this->assertInstanceOf(Carousel::class, $test->getCarousel());
    }

    public function testGetVariantIds(): void
    {
        $carouselA = Carousel::image('test-a', ['image1.jpg']);
        $carouselB = Carousel::image('test-b', ['image2.jpg']);
        $carouselC = Carousel::image('test-c', ['image3.jpg']);

        $test = new ABTest('test-2', [
            'variant_a' => ['carousel' => $carouselA, 'weight' => 33],
            'variant_b' => ['carousel' => $carouselB, 'weight' => 33],
            'variant_c' => ['carousel' => $carouselC, 'weight' => 34],
        ]);

        $variantIds = $test->getVariantIds();
        $this->assertCount(3, $variantIds);
        $this->assertContains('variant_a', $variantIds);
        $this->assertContains('variant_b', $variantIds);
        $this->assertContains('variant_c', $variantIds);
    }

    public function testIsVariantSelected(): void
    {
        $carouselA = Carousel::image('test-a', ['image1.jpg']);
        $carouselB = Carousel::image('test-b', ['image2.jpg']);

        $test = new ABTest('test-3', [
            'variant_a' => ['carousel' => $carouselA, 'weight' => 50],
            'variant_b' => ['carousel' => $carouselB, 'weight' => 50],
        ]);

        $selected = $test->getSelectedVariant();
        $this->assertTrue($test->isVariantSelected($selected));
        $this->assertFalse($test->isVariantSelected($selected === 'variant_a' ? 'variant_b' : 'variant_a'));
    }

    public function testCookieMethodUsesExistingSession(): void
    {
        if (session_status() === \PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['carousel_variant_test-cookie'] = 'variant_b';

        $carouselA = Carousel::image('test-a', ['image1.jpg']);
        $carouselB = Carousel::image('test-b', ['image2.jpg']);

        $test = new ABTest('test-cookie', [
            'variant_a' => ['carousel' => $carouselA, 'weight' => 50],
            'variant_b' => ['carousel' => $carouselB, 'weight' => 50],
        ], ['method' => ABTest::METHOD_COOKIE]);

        $this->assertEquals('variant_b', $test->getSelectedVariant());
    }

    public function testHashMethodConsistency(): void
    {
        $carouselA = Carousel::image('test-a', ['image1.jpg']);
        $carouselB = Carousel::image('test-b', ['image2.jpg']);

        $userId = 'user123';

        // Create test multiple times with same user ID
        $selectedVariants = [];
        for ($i = 0; $i < 10; $i++) {
            $test = new ABTest('test-hash', [
                'variant_a' => ['carousel' => $carouselA, 'weight' => 50],
                'variant_b' => ['carousel' => $carouselB, 'weight' => 50],
            ], ['method' => ABTest::METHOD_HASH, 'userId' => $userId]);

            $selectedVariants[] = $test->getSelectedVariant();
        }

        // All selections should be the same (consistency)
        $unique = array_unique($selectedVariants);
        $this->assertCount(1, $unique, 'Hash method should return consistent variant');
    }

    public function testWeightDistribution(): void
    {
        $carouselA = Carousel::image('test-a', ['image1.jpg']);
        $carouselB = Carousel::image('test-b', ['image2.jpg']);

        // Test with 80/20 distribution
        $selections = [];
        for ($i = 0; $i < 1000; $i++) {
            $test = new ABTest('test-weight', [
                'variant_a' => ['carousel' => $carouselA, 'weight' => 80],
                'variant_b' => ['carousel' => $carouselB, 'weight' => 20],
            ], ['method' => ABTest::METHOD_RANDOM]);

            $selections[] = $test->getSelectedVariant();
        }

        $countA = count(array_filter($selections, fn($v) => $v === 'variant_a'));
        $countB = count(array_filter($selections, fn($v) => $v === 'variant_b'));

        // Should be approximately 80/20 (allow 10% margin)
        $ratioA = $countA / 1000;
        $this->assertGreaterThan(0.70, $ratioA, 'Variant A should be selected ~80% of the time');
        $this->assertLessThan(0.90, $ratioA, 'Variant A should be selected ~80% of the time');
    }

    public function testInvalidVariantThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        new ABTest('test-invalid', []);
    }

    public function testInvalidCarouselThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        new ABTest('test-invalid-carousel', [
            'variant_a' => ['carousel' => 'not-a-carousel', 'weight' => 50],
        ]);
    }

    public function testInvalidWeightThrowsException(): void
    {
        $carouselA = Carousel::image('test-a', ['image1.jpg']);

        $this->expectException(\InvalidArgumentException::class);
        
        // Negative weight should throw exception
        new ABTest('test-invalid-weight', [
            'variant_a' => ['carousel' => $carouselA, 'weight' => -10],
        ]);
    }

    public function testNormalizeWeightsOver100(): void
    {
        $carouselA = Carousel::image('test-a', ['image1.jpg']);
        $carouselB = Carousel::image('test-b', ['image2.jpg']);

        // Weights sum to 200, should be normalized
        $test = new ABTest('test-normalize', [
            'variant_a' => ['carousel' => $carouselA, 'weight' => 120],
            'variant_b' => ['carousel' => $carouselB, 'weight' => 80],
        ]);

        // Should not throw exception
        $this->assertInstanceOf(ABTest::class, $test);
        $this->assertContains($test->getSelectedVariant(), ['variant_a', 'variant_b']);
    }

    public function testGetVariantStats(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        
        $carouselA = Carousel::image('test-a', ['image1.jpg'], ['analytics' => true, 'analyticsProvider' => $analytics]);
        $carouselB = Carousel::image('test-b', ['image2.jpg'], ['analytics' => true, 'analyticsProvider' => $analytics]);

        $test = new ABTest('test-stats', [
            'variant_a' => ['carousel' => $carouselA, 'weight' => 50],
            'variant_b' => ['carousel' => $carouselB, 'weight' => 50],
        ], ['analytics' => $analytics]);

        // Track some events
        $analytics->trackImpression('test-a', 0);
        $analytics->trackImpression('test-a', 0);
        $analytics->trackClick('test-a', 0, 'https://example.com');
        $analytics->trackImpression('test-b', 0);

        $stats = $test->getVariantStats();

        $this->assertArrayHasKey('variant_a', $stats);
        $this->assertArrayHasKey('variant_b', $stats);
        $this->assertEquals(2, $stats['variant_a']['impressions']);
        $this->assertEquals(1, $stats['variant_a']['clicks']);
        $this->assertEquals(1, $stats['variant_b']['impressions']);
    }

    public function testGetVariantStatsWithoutAnalytics(): void
    {
        $carouselA = Carousel::image('test-a', ['image1.jpg']);
        $carouselB = Carousel::image('test-b', ['image2.jpg']);

        $test = new ABTest('test-no-analytics', [
            'variant_a' => ['carousel' => $carouselA, 'weight' => 50],
            'variant_b' => ['carousel' => $carouselB, 'weight' => 50],
        ]);

        $stats = $test->getVariantStats();
        $this->assertEmpty($stats);
    }
}


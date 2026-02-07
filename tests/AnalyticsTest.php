<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use JulienLinard\Carousel\Analytics\FileAnalytics;
use PHPUnit\Framework\TestCase;

class AnalyticsTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/php-carousel-analytics-' . uniqid();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clean up temp files
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

    public function testTrackImpression(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        $analytics->trackImpression('test-carousel', 0);
        $analytics->flush();

        $date = date('Y-m-d');
        $file = $this->tempDir . '/analytics-' . $date . '.json';

        $this->assertFileExists($file);
        
        $logs = json_decode(file_get_contents($file), true);
        $this->assertCount(1, $logs);
        $this->assertEquals('impression', $logs[0]['event']);
        $this->assertEquals('test-carousel', $logs[0]['carousel_id']);
        $this->assertEquals(0, $logs[0]['slide_index']);
    }

    public function testTrackClick(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        $analytics->trackClick('test-carousel', 2, 'https://example.com');
        $analytics->flush();

        $date = date('Y-m-d');
        $file = $this->tempDir . '/analytics-' . $date . '.json';

        $logs = json_decode(file_get_contents($file), true);
        $this->assertCount(1, $logs);
        $this->assertEquals('click', $logs[0]['event']);
        $this->assertEquals('test-carousel', $logs[0]['carousel_id']);
        $this->assertEquals(2, $logs[0]['slide_index']);
        $this->assertEquals('https://example.com', $logs[0]['url']);
    }

    public function testTrackInteraction(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        $analytics->trackInteraction('test-carousel', 'arrow_click', ['direction' => 'next']);
        $analytics->flush();

        $date = date('Y-m-d');
        $file = $this->tempDir . '/analytics-' . $date . '.json';

        $logs = json_decode(file_get_contents($file), true);
        $this->assertCount(1, $logs);
        $this->assertEquals('interaction', $logs[0]['event']);
        $this->assertEquals('test-carousel', $logs[0]['carousel_id']);
        $this->assertEquals('arrow_click', $logs[0]['interaction_type']);
        $this->assertEquals(['direction' => 'next'], $logs[0]['data']);
    }

    public function testGetReport(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        
        // Track some events
        $analytics->trackImpression('test-carousel', 0);
        $analytics->trackImpression('test-carousel', 1);
        $analytics->trackImpression('test-carousel', 0);
        $analytics->trackClick('test-carousel', 0, 'https://example.com');
        $analytics->trackInteraction('test-carousel', 'arrow_click', ['direction' => 'next']);
        $analytics->flush();

        $report = $analytics->getReport('test-carousel');
        
        $this->assertEquals('test-carousel', $report['carousel_id']);
        $this->assertEquals(3, $report['total_impressions']);
        $this->assertEquals(1, $report['total_clicks']);
        $this->assertGreaterThan(0, $report['ctr']);
        $this->assertArrayHasKey('interaction_breakdown', $report);
        $this->assertArrayHasKey('slide_impressions', $report);
    }

    public function testGetReportWithDateRange(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        
        $analytics->trackImpression('test-carousel', 0);
        $analytics->flush();

        $startDate = new \DateTime('-7 days');
        $endDate = new \DateTime('now');
        
        $report = $analytics->getReport('test-carousel', $startDate, $endDate);
        
        $this->assertArrayHasKey('period', $report);
        $this->assertNotNull($report['period']['start']);
        $this->assertNotNull($report['period']['end']);
    }

    public function testGetReportFiltersByCarouselId(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        
        $analytics->trackImpression('carousel-1', 0);
        $analytics->trackImpression('carousel-2', 0);
        $analytics->trackImpression('carousel-1', 1);
        $analytics->flush();

        $report = $analytics->getReport('carousel-1');
        
        $this->assertEquals(2, $report['total_impressions']);
    }

    public function testMultipleEventsSameDay(): void
    {
        $analytics = new FileAnalytics($this->tempDir);
        
        $analytics->trackImpression('test', 0);
        $analytics->trackClick('test', 0);
        $analytics->trackInteraction('test', 'swipe');
        $analytics->flush();

        $date = date('Y-m-d');
        $file = $this->tempDir . '/analytics-' . $date . '.json';

        $logs = json_decode(file_get_contents($file), true);
        $this->assertCount(3, $logs);
    }
}


<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Analytics;

/**
 * File-based analytics implementation
 * 
 * Stores analytics data in JSON files
 */
class FileAnalytics implements AnalyticsInterface
{
    private string $storagePath;

    public function __construct(string $storagePath)
    {
        $this->storagePath = rtrim($storagePath, '/');
        
        // Create directory if it doesn't exist
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Track carousel impression
     */
    public function trackImpression(string $carouselId, int $slideIndex): void
    {
        $this->log([
            'event' => 'impression',
            'carousel_id' => $carouselId,
            'slide_index' => $slideIndex,
            'timestamp' => time(),
        ]);
    }

    /**
     * Track slide click
     */
    public function trackClick(string $carouselId, int $slideIndex, ?string $url = null): void
    {
        $this->log([
            'event' => 'click',
            'carousel_id' => $carouselId,
            'slide_index' => $slideIndex,
            'url' => $url,
            'timestamp' => time(),
        ]);
    }

    /**
     * Track custom interaction
     */
    public function trackInteraction(string $carouselId, string $event, array $data = []): void
    {
        $this->log([
            'event' => 'interaction',
            'carousel_id' => $carouselId,
            'interaction_type' => $event,
            'data' => $data,
            'timestamp' => time(),
        ]);
    }

    /**
     * Get analytics report
     */
    public function getReport(string $carouselId, ?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $logs = $this->readLogs($carouselId, $startDate, $endDate);
        
        $impressions = array_filter($logs, fn($log) => $log['event'] === 'impression');
        $clicks = array_filter($logs, fn($log) => $log['event'] === 'click');
        $interactions = array_filter($logs, fn($log) => $log['event'] === 'interaction');
        
        $totalImpressions = count($impressions);
        $totalClicks = count($clicks);
        $ctr = $totalImpressions > 0 ? round($totalClicks / $totalImpressions, 4) : 0.0;
        
        // Most viewed slide
        $slideImpressions = [];
        foreach ($impressions as $impression) {
            $index = $impression['slide_index'];
            $slideImpressions[$index] = ($slideImpressions[$index] ?? 0) + 1;
        }
        arsort($slideImpressions);
        $mostViewedSlide = !empty($slideImpressions) ? (int) array_key_first($slideImpressions) : null;
        
        // Average time per slide (simplified - would need more data)
        $averageTimePerSlide = 0.0;
        if ($totalImpressions > 0) {
            // Estimate based on autoplay interval if available
            $averageTimePerSlide = 3.5; // Default estimate
        }
        
        // Interaction breakdown
        $interactionBreakdown = [];
        foreach ($interactions as $interaction) {
            $type = $interaction['interaction_type'] ?? 'unknown';
            $interactionBreakdown[$type] = ($interactionBreakdown[$type] ?? 0) + 1;
        }
        
        return [
            'carousel_id' => $carouselId,
            'period' => [
                'start' => $startDate?->format('Y-m-d H:i:s'),
                'end' => $endDate?->format('Y-m-d H:i:s'),
            ],
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'ctr' => $ctr,
            'most_viewed_slide' => $mostViewedSlide,
            'average_time_per_slide' => $averageTimePerSlide,
            'interaction_breakdown' => $interactionBreakdown,
            'slide_impressions' => $slideImpressions,
        ];
    }

    /**
     * Log event to file
     */
    private function log(array $data): void
    {
        $date = date('Y-m-d');
        $file = $this->storagePath . '/analytics-' . $date . '.json';
        
        $logs = [];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $logs = json_decode($content, true) ?: [];
        }
        
        $logs[] = $data;
        
        file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT));
    }

    /**
     * Read logs for a carousel
     */
    private function readLogs(string $carouselId, ?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $allLogs = [];
        
        // Get date range
        $start = $startDate ? clone $startDate : new \DateTime('-30 days');
        $end = $endDate ? clone $endDate : new \DateTime('now');
        
        $current = clone $start;
        while ($current <= $end) {
            $date = $current->format('Y-m-d');
            $file = $this->storagePath . '/analytics-' . $date . '.json';
            
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $logs = json_decode($content, true) ?: [];
                
                // Filter by carousel ID
                $filtered = array_filter($logs, fn($log) => ($log['carousel_id'] ?? '') === $carouselId);
                $allLogs = array_merge($allLogs, $filtered);
            }
            
            $current->modify('+1 day');
        }
        
        // Filter by timestamp if dates are provided
        if ($startDate || $endDate) {
            $startTimestamp = $startDate ? $startDate->getTimestamp() : 0;
            $endTimestamp = $endDate ? $endDate->getTimestamp() : PHP_INT_MAX;
            
            $allLogs = array_filter($allLogs, function($log) use ($startTimestamp, $endTimestamp) {
                $timestamp = $log['timestamp'] ?? 0;
                return $timestamp >= $startTimestamp && $timestamp <= $endTimestamp;
            });
        }
        
        return array_values($allLogs);
    }
}


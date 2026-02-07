<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Analytics;

/**
 * File-based analytics implementation
 *
 * Stores analytics data in JSON files.
 * Path is validated to prevent directory traversal (path must be under an explicit base).
 */
class FileAnalytics implements AnalyticsInterface
{
    private string $storagePath;

    /**
     * @param string      $storagePath Path to the analytics storage directory (relative to $basePath if basePath is set)
     * @param string|null $basePath    Optional base directory. If set, $storagePath must be relative and resolve under this base. If null, path must not contain '..'
     * @throws \InvalidArgumentException If path is invalid or resolves outside the allowed base
     */
    public function __construct(string $storagePath, ?string $basePath = null)
    {
        if ($basePath !== null) {
            $this->storagePath = $this->resolvePathWithBase($storagePath, $basePath);
        } else {
            $this->storagePath = $this->resolvePathWithoutBase($storagePath);
        }

        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0700, true);
        }

        $resolved = realpath($this->storagePath);
        if ($resolved === false) {
            throw new \InvalidArgumentException('Storage path could not be resolved after creation.');
        }
        $this->storagePath = $resolved;
    }

    /**
     * Resolve path when an explicit base is provided (recommended for security).
     */
    private function resolvePathWithBase(string $storagePath, string $basePath): string
    {
        $baseReal = realpath($basePath);
        if ($baseReal === false || !is_dir($baseReal)) {
            throw new \InvalidArgumentException('Base path does not exist or is not a directory.');
        }

        $relative = $this->normalizeRelativePath($storagePath);
        $intendedPath = rtrim($baseReal . '/' . $relative, '/');

        if ($intendedPath !== $baseReal && !str_starts_with($intendedPath, $baseReal . '/')) {
            throw new \InvalidArgumentException('Storage path must resolve under the base path.');
        }

        return $intendedPath;
    }

    /**
     * Resolve path when no base is provided (reject traversal, use 0700).
     */
    private function resolvePathWithoutBase(string $storagePath): string
    {
        if (str_contains($storagePath, '..')) {
            throw new \InvalidArgumentException('Storage path must not contain "..".');
        }

        $path = rtrim(str_replace('\\', '/', $storagePath), '/');
        if ($path === '') {
            throw new \InvalidArgumentException('Storage path cannot be empty.');
        }

        if ($path[0] !== '/') {
            $cwd = getcwd();
            $path = ($cwd !== false ? $cwd : sys_get_temp_dir()) . '/' . $path;
        }

        return $path;
    }

    /**
     * Normalize relative path (remove ., .., duplicate slashes).
     *
     * @return string Path segments joined by /, no leading/trailing slash
     */
    private function normalizeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path, '/'));
        if ($path === '') {
            return 'carousel-analytics';
        }
        $parts = explode('/', $path);
        $resolved = [];
        foreach ($parts as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($resolved);
                continue;
            }
            $resolved[] = $segment;
        }
        $joined = implode('/', $resolved);
        return $joined === '' ? 'carousel-analytics' : $joined;
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


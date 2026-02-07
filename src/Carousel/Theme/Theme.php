<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Theme;

/**
 * Theme configuration for carousel
 */
class Theme
{
    public const MODE_AUTO = 'auto';
    public const MODE_LIGHT = 'light';
    public const MODE_DARK = 'dark';

    /** Formats de couleur autorisés (évite injection CSS) */
    private const SAFE_COLOR_HEX = '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/';
    private const SAFE_COLOR_RGB = '/^rgb\s*\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/';
    private const SAFE_COLOR_RGBA = '/^rgba\s*\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*[\d.]+\s*\)$/';

    private string $mode;
    private array $lightColors;
    private array $darkColors;

    /**
     * Default light theme colors
     */
    private const DEFAULT_LIGHT_COLORS = [
        'background' => '#ffffff',
        'text' => '#1a1a1a',
        'arrow' => '#333333',
        'arrowHover' => '#000000',
        'dot' => '#cccccc',
        'dotActive' => '#0066cc',
        'dotHover' => '#999999',
        'border' => '#e0e0e0',
        'shadow' => 'rgba(0, 0, 0, 0.1)',
        'shadowHover' => 'rgba(0, 0, 0, 0.15)',
        'cardBackground' => '#ffffff',
        'cardText' => '#1a1a1a',
        'cardContent' => '#666666',
        'link' => '#0066cc',
        'linkHover' => '#0052a3',
        'loadingSpinner' => '#0066cc',
    ];

    /**
     * Default dark theme colors
     */
    private const DEFAULT_DARK_COLORS = [
        'background' => '#1a1a1a',
        'text' => '#ffffff',
        'arrow' => '#ffffff',
        'arrowHover' => '#cccccc',
        'dot' => '#666666',
        'dotActive' => '#4a9eff',
        'dotHover' => '#999999',
        'border' => '#333333',
        'shadow' => 'rgba(0, 0, 0, 0.3)',
        'shadowHover' => 'rgba(0, 0, 0, 0.5)',
        'cardBackground' => '#2a2a2a',
        'cardText' => '#ffffff',
        'cardContent' => '#cccccc',
        'link' => '#4a9eff',
        'linkHover' => '#6bb5ff',
        'loadingSpinner' => '#4a9eff',
    ];

    public function __construct(
        string $mode = self::MODE_AUTO,
        ?array $lightColors = null,
        ?array $darkColors = null
    ) {
        $this->mode = $this->validateMode($mode);
        $this->lightColors = array_merge(self::DEFAULT_LIGHT_COLORS, $this->sanitizeColorMap($lightColors ?? [], self::DEFAULT_LIGHT_COLORS));
        $this->darkColors = array_merge(self::DEFAULT_DARK_COLORS, $this->sanitizeColorMap($darkColors ?? [], self::DEFAULT_DARK_COLORS));
    }

    /**
     * Garde uniquement les clés connues avec des valeurs couleur sûres (hex, rgb, rgba).
     */
    private function sanitizeColorMap(array $input, array $defaults): array
    {
        $out = [];
        foreach ($input as $key => $value) {
            if (!isset($defaults[$key]) || !is_string($value)) {
                continue;
            }
            $value = trim($value);
            if ($value !== '' && self::isSafeColorValue($value)) {
                $out[$key] = $value;
            }
        }
        return $out;
    }

    /**
     * Vérifie qu'une valeur est un format couleur CSS sûr (pas d'injection).
     */
    public static function isSafeColorValue(string $value): bool
    {
        $value = trim($value);
        if ($value === 'transparent' || $value === 'currentColor') {
            return true;
        }
        return preg_match(self::SAFE_COLOR_HEX, $value) === 1
            || preg_match(self::SAFE_COLOR_RGB, $value) === 1
            || preg_match(self::SAFE_COLOR_RGBA, $value) === 1;
    }

    /**
     * Validate theme mode
     */
    private function validateMode(string $mode): string
    {
        $validModes = [self::MODE_AUTO, self::MODE_LIGHT, self::MODE_DARK];
        if (!in_array($mode, $validModes, true)) {
            throw new \InvalidArgumentException(
                "Invalid theme mode: {$mode}. Must be one of: " . implode(', ', $validModes)
            );
        }
        return $mode;
    }

    /**
     * Get theme mode
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get light theme colors
     */
    public function getLightColors(): array
    {
        return $this->lightColors;
    }

    /**
     * Get dark theme colors
     */
    public function getDarkColors(): array
    {
        return $this->darkColors;
    }

    /**
     * Get color value for a specific key
     */
    public function getColor(string $key, bool $isDark = false): string
    {
        $colors = $isDark ? $this->darkColors : $this->lightColors;
        return $colors[$key] ?? ($isDark ? self::DEFAULT_DARK_COLORS[$key] ?? '' : self::DEFAULT_LIGHT_COLORS[$key] ?? '');
    }

    /**
     * Create theme from array (for options)
     */
    public static function fromArray(array $config): self
    {
        $mode = $config['theme'] ?? self::MODE_AUTO;
        $lightColors = $config['themeColors']['light'] ?? null;
        $darkColors = $config['themeColors']['dark'] ?? null;

        return new self($mode, $lightColors, $darkColors);
    }
}


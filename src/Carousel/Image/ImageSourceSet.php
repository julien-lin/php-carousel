<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Image;

/**
 * Represents a responsive image source set with multiple formats and sizes
 */
class ImageSourceSet
{
    private array $sources = [];
    private string $fallback;
    private array $formats = ['webp', 'avif', 'jpg'];
    private ?string $alt = null;

    public function __construct(string $fallback, ?string $alt = null)
    {
        $this->fallback = $fallback;
        $this->alt = $alt;
    }

    /**
     * Add a source with srcset
     * 
     * @param string $srcset Source set (e.g., "image-400w.webp 400w, image-800w.webp 800w")
     * @param string|null $media Media query (e.g., "(max-width: 400px)")
     * @param string|null $type MIME type (e.g., "image/webp")
     * @return self
     */
    public function addSource(string $srcset, ?string $media = null, ?string $type = null): self
    {
        $this->sources[] = [
            'srcset' => $srcset,
            'media' => $media,
            'type' => $type,
        ];
        return $this;
    }

    /**
     * Add multiple sources for different formats
     * 
     * @param array $sources Array of sources [format => srcset]
     * @param string|null $media Media query
     * @return self
     */
    public function addFormats(array $sources, ?string $media = null): self
    {
        foreach ($sources as $format => $srcset) {
            $type = $this->getMimeType($format);
            $this->addSource($srcset, $media, $type);
        }
        return $this;
    }

    /**
     * Set supported formats (order matters - first is preferred)
     * 
     * @param array $formats Array of format strings (e.g., ['webp', 'avif', 'jpg'])
     * @return self
     */
    public function setFormats(array $formats): self
    {
        $this->formats = $formats;
        return $this;
    }

    /**
     * Get MIME type for format
     */
    public static function getMimeType(string $format): string
    {
        return match (strtolower($format)) {
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/jpeg',
        };
    }

    /**
     * Render the <picture> element
     * 
     * @param bool $lazyLoad Enable lazy loading
     * @return string HTML string
     */
    public function render(bool $lazyLoad = true): string
    {
        $html = '<picture>';
        
        // Add sources (modern formats first, then fallback)
        foreach ($this->sources as $source) {
            $html .= '<source';
            
            if ($source['srcset']) {
                $html .= ' srcset="' . htmlspecialchars($source['srcset'], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"';
            }
            
            if ($source['media']) {
                $html .= ' media="' . htmlspecialchars($source['media'], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"';
            }
            
            if ($source['type']) {
                $html .= ' type="' . htmlspecialchars($source['type'], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"';
            }
            
            $html .= '>';
        }
        
        // Fallback image
        $html .= '<img';
        $html .= ' src="' . htmlspecialchars($this->fallback, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"';
        
        if ($this->alt !== null) {
            $html .= ' alt="' . htmlspecialchars($this->alt, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"';
        }
        
        if ($lazyLoad) {
            $html .= ' loading="lazy"';
        }
        
        $html .= '>';
        $html .= '</picture>';
        
        return $html;
    }

    /**
     * Get fallback image URL
     */
    public function getFallback(): string
    {
        return $this->fallback;
    }

    /**
     * Get all sources
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * Check if source set has sources
     */
    public function hasSources(): bool
    {
        return !empty($this->sources);
    }
}


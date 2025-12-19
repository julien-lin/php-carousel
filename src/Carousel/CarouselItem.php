<?php

declare(strict_types=1);

namespace JulienLinard\Carousel;

use JulienLinard\Carousel\Validator\IdSanitizer;
use JulienLinard\Carousel\Validator\UrlValidator;

/**
 * Represents a single item in a carousel
 */
class CarouselItem
{
    public function __construct(
        public string $id,
        public string $title = '',
        public string $content = '',
        public string $image = '',
        public string $link = '',
        public array $attributes = []
    ) {
        // Sanitize ID
        $this->id = IdSanitizer::sanitize($id);
        
        // Sanitize link URL
        if (!empty($link)) {
            $this->link = UrlValidator::sanitize($link);
        }
        
        // Sanitize attributes
        $this->attributes = $this->sanitizeAttributes($attributes);
    }
    
    /**
     * Sanitize custom attributes
     * 
     * @param array $attributes Attributes to sanitize
     * @return array Sanitized attributes
     */
    private function sanitizeAttributes(array $attributes): array
    {
        $sanitized = [];
        
        foreach ($attributes as $key => $value) {
            // Only allow safe attributes (class, data-*, aria-*)
            if (preg_match('/^(class|data-|aria-)/', $key)) {
                $sanitized[$key] = htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        
        return $sanitized;
    }

    /**
     * Create an item from an array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? uniqid('item_'),
            title: $data['title'] ?? '',
            content: $data['content'] ?? '',
            image: $data['image'] ?? '',
            link: $data['link'] ?? '',
            attributes: $data['attributes'] ?? []
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'link' => $this->link,
            'attributes' => $this->attributes,
        ];
    }
}


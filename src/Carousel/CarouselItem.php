<?php

declare(strict_types=1);

namespace JulienLinard\Carousel;

use JulienLinard\Carousel\Image\ImageSourceSet;
use JulienLinard\Carousel\Validator\IdSanitizer;
use JulienLinard\Carousel\Validator\UrlValidator;

/**
 * Represents a single item in a carousel
 */
class CarouselItem
{
    private ?ImageSourceSet $imageSourceSet = null;

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
    
    /** @var list<string> Whitelist stricte des attributs autorisés (XSS) */
    private const ALLOWED_ATTRIBUTES = [
        'class',
        'data-slide',
        'data-content',
        'data-id',
        'aria-label',
        'aria-describedby',
        'aria-current',
        'aria-hidden',
        'aria-roledescription',
        'aria-posinset',
        'aria-setsize',
    ];

    /**
     * Sanitize custom attributes (whitelist + reject dangerous patterns)
     *
     * @param array<string, mixed> $attributes Attributes to sanitize
     * @return array<string, string> Sanitized attributes
     */
    private function sanitizeAttributes(array $attributes): array
    {
        $sanitized = [];

        foreach ($attributes as $key => $value) {
            $key = (string) $key;

            if (!self::isAllowedAttribute($key)) {
                continue;
            }

            $value = (string) $value;
            if (self::isDangerousAttributeValue($value)) {
                continue;
            }

            $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $sanitized;
    }

    private static function isAllowedAttribute(string $key): bool
    {
        if (in_array($key, self::ALLOWED_ATTRIBUTES, true)) {
            return true;
        }
        return str_starts_with($key, 'aria-');
    }

    private static function isDangerousAttributeValue(string $value): bool
    {
        return preg_match('/^(on\w+|javascript\s*:|data\s*:\s*text\/html)/i', $value) === 1;
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
     * Set image source set for responsive images
     * 
     * @param ImageSourceSet $sourceSet Image source set
     * @return self
     */
    public function setImageSourceSet(ImageSourceSet $sourceSet): self
    {
        $this->imageSourceSet = $sourceSet;
        return $this;
    }

    /**
     * Get image source set
     * 
     * @return ImageSourceSet|null
     */
    public function getImageSourceSet(): ?ImageSourceSet
    {
        return $this->imageSourceSet;
    }

    /**
     * Check if item has responsive image source set
     * 
     * @return bool
     */
    public function hasImageSourceSet(): bool
    {
        return $this->imageSourceSet !== null;
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


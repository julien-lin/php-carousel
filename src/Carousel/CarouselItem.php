<?php

declare(strict_types=1);

namespace JulienLinard\Carousel;

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
    ) {}

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


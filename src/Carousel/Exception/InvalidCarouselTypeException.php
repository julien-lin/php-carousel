<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Exception;

/**
 * Exception thrown when an invalid carousel type is provided
 */
class InvalidCarouselTypeException extends CarouselException
{
    public function __construct(string $type, array $validTypes = [])
    {
        $message = "Invalid carousel type: '{$type}'";
        if (!empty($validTypes)) {
            $message .= ". Valid types are: " . implode(', ', $validTypes);
        }
        parent::__construct($message);
    }
}


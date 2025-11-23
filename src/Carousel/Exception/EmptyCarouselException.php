<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Exception;

/**
 * Exception thrown when trying to render an empty carousel
 */
class EmptyCarouselException extends CarouselException
{
    public function __construct()
    {
        parent::__construct("Cannot render an empty carousel. Please add at least one item.");
    }
}


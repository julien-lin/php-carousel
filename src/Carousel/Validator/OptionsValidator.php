<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Validator;

use InvalidArgumentException;

/**
 * Validates carousel options
 */
class OptionsValidator
{
    /**
     * Validate and sanitize carousel options
     * 
     * @param array $options Options to validate
     * @return array Validated options
     * @throws InvalidArgumentException If validation fails
     */
    public static function validate(array $options): array
    {
        $validated = [];
        
        // Autoplay interval
        if (isset($options['autoplayInterval'])) {
            $interval = (int) $options['autoplayInterval'];
            if ($interval < 1000 || $interval > 60000) {
                throw new InvalidArgumentException(
                    'autoplayInterval must be between 1000 and 60000 milliseconds'
                );
            }
            $validated['autoplayInterval'] = $interval;
        }
        
        // Transition duration
        if (isset($options['transitionDuration'])) {
            $duration = (int) $options['transitionDuration'];
            if ($duration < 0 || $duration > 5000) {
                throw new InvalidArgumentException(
                    'transitionDuration must be between 0 and 5000 milliseconds'
                );
            }
            $validated['transitionDuration'] = $duration;
        }
        
        // Items per slide
        foreach (['itemsPerSlide', 'itemsPerSlideDesktop', 'itemsPerSlideTablet', 'itemsPerSlideMobile'] as $key) {
            if (isset($options[$key])) {
                $value = (int) $options[$key];
                if ($value < 1 || $value > 10) {
                    throw new InvalidArgumentException(
                        "$key must be between 1 and 10"
                    );
                }
                $validated[$key] = $value;
            }
        }
        
        // Gap
        if (isset($options['gap'])) {
            $gap = (int) $options['gap'];
            if ($gap < 0 || $gap > 100) {
                throw new InvalidArgumentException(
                    'gap must be between 0 and 100 pixels'
                );
            }
            $validated['gap'] = $gap;
        }

        // Max items per carousel (DoS)
        if (isset($options['maxItems'])) {
            $maxItems = (int) $options['maxItems'];
            if ($maxItems < 1 || $maxItems > 10000) {
                throw new InvalidArgumentException(
                    'maxItems must be between 1 and 10000'
                );
            }
            $validated['maxItems'] = $maxItems;
        }
        
        // Transition type
        if (isset($options['transition'])) {
            $transition = $options['transition'];
            $allowedTransitions = ['slide', 'fade', 'cube'];
            if (!in_array($transition, $allowedTransitions, true)) {
                throw new InvalidArgumentException(
                    "transition must be one of: " . implode(', ', $allowedTransitions)
                );
            }
            $validated['transition'] = $transition;
        }
        
        // Boolean options
        foreach (['autoplay', 'loop', 'showArrows', 'showDots', 'showThumbnails', 
                   'responsive', 'lazyLoad', 'keyboardNavigation', 'touchSwipe'] as $key) {
            if (isset($options[$key])) {
                $validated[$key] = (bool) $options[$key];
            }
        }
        
        // Height and width (strings, but validate they're not empty)
        foreach (['height', 'width'] as $key) {
            if (isset($options[$key])) {
                $value = (string) $options[$key];
                if (strlen($value) > 50) {
                    throw new InvalidArgumentException(
                        "$key must not exceed 50 characters"
                    );
                }
                $validated[$key] = $value;
            }
        }
        
        return array_merge($options, $validated);
    }
}


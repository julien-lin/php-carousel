<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Validator;

/**
 * Sanitizes IDs for carousel and items
 */
class IdSanitizer
{
    private const MAX_LENGTH = 50;
    
    /**
     * Sanitize an ID
     * 
     * @param string $id The ID to sanitize
     * @return string Sanitized ID
     */
    public static function sanitize(string $id): string
    {
        // Remove all characters except alphanumeric, underscore, and hyphen
        $id = preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
        
        // Limit length
        $id = substr($id, 0, self::MAX_LENGTH);
        
        // Ensure it's not empty
        if (empty($id)) {
            $id = 'carousel_' . uniqid();
        }
        
        return $id;
    }
    
    /**
     * Validate an ID
     * 
     * @param string $id The ID to validate
     * @return bool True if valid, false otherwise
     */
    public static function isValid(string $id): bool
    {
        if (empty($id)) {
            return false;
        }
        
        if (strlen($id) > self::MAX_LENGTH) {
            return false;
        }
        
        // Check if contains only allowed characters
        return (bool) preg_match('/^[a-zA-Z0-9_-]+$/', $id);
    }
}


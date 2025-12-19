<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\JavaScript;

/**
 * Generates the CarouselAPI JavaScript code
 */
class CarouselAPI
{
    /**
     * Generate the CarouselAPI JavaScript code
     * 
     * @return string JavaScript code
     */
    public static function generate(): string
    {
        return <<<'JS'
window.CarouselAPI = (function() {
    'use strict';
    
    const instances = new Map();
    
    /**
     * Initialize a carousel
     * 
     * @param {string} id Carousel ID
     * @param {Object} options Carousel options
     * @returns {CarouselInstance|null} Carousel instance or null if not found
     */
    function init(id, options = {}) {
        const carouselEl = document.getElementById(`carousel-${id}`);
        if (!carouselEl) {
            console.warn(`Carousel #carousel-${id} not found`);
            return null;
        }
        
        // Return existing instance if already initialized
        if (instances.has(id)) {
            return instances.get(id);
        }
        
        // Check if carousel is already initialized by inline script
        if (window.carouselInstances && window.carouselInstances[id]) {
            // Wrap existing instance
            const existingInstance = window.carouselInstances[id];
            const instance = new CarouselInstance(id, carouselEl, options, existingInstance);
            instances.set(id, instance);
            return instance;
        }
        
        // Create new instance
        const instance = new CarouselInstance(id, carouselEl, options);
        instances.set(id, instance);
        return instance;
    }
    
    /**
     * Get carousel instance
     * 
     * @param {string} id Carousel ID
     * @returns {CarouselInstance|null} Carousel instance or null
     */
    function get(id) {
        return instances.get(id) || null;
    }
    
    /**
     * Destroy carousel instance
     * 
     * @param {string} id Carousel ID
     * @returns {boolean} True if destroyed, false if not found
     */
    function destroy(id) {
        const instance = instances.get(id);
        if (instance) {
            instance.destroy();
            instances.delete(id);
            return true;
        }
        return false;
    }
    
    /**
     * Auto-initialize all carousels on page
     * 
     * @returns {Array<CarouselInstance>} Array of initialized instances
     */
    function autoInit() {
        const initialized = [];
        document.querySelectorAll('[data-carousel-id]').forEach(el => {
            const id = el.getAttribute('data-carousel-id');
            if (id && !instances.has(id)) {
                const instance = init(id);
                if (instance) {
                    initialized.push(instance);
                }
            }
        });
        return initialized;
    }
    
    return {
        init,
        get,
        destroy,
        autoInit
    };
})();
JS;
    }
}


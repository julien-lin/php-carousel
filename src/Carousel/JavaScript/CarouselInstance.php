<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\JavaScript;

/**
 * Generates the CarouselInstance JavaScript class
 */
class CarouselInstance
{
    /**
     * Generate the CarouselInstance JavaScript class
     * 
     * @return string JavaScript code
     */
    public static function generate(): string
    {
        return <<<'JS'
/**
 * CarouselInstance class
 * Wraps carousel functionality with modern API
 */
class CarouselInstance {
    constructor(id, element, options = {}, existingInstance = null) {
        this.id = id;
        this.element = element;
        this.options = options;
        this.listeners = new Map();
        this.existingInstance = existingInstance;
        
        // If existing instance (from inline script), use it
        if (existingInstance) {
            this.currentIndex = existingInstance.getCurrentIndex ? existingInstance.getCurrentIndex() : 0;
            this.totalSlides = this.element.querySelectorAll('.carousel-slide').length;
            this._wrapExistingInstance();
        } else {
            this.currentIndex = 0;
            this.totalSlides = this.element.querySelectorAll('.carousel-slide').length;
            this._init();
        }
    }
    
    /**
     * Wrap existing instance from inline script
     */
    _wrapExistingInstance() {
        if (!this.existingInstance) return;
        
        // Wrap existing methods
        const originalGoTo = this.existingInstance.goToSlide;
        const originalNext = this.existingInstance.nextSlide;
        const originalPrev = this.existingInstance.prevSlide;
        const originalDestroy = this.existingInstance.destroy;
        
        // Override goToSlide to emit events
        if (originalGoTo) {
            this.existingInstance.goToSlide = (index) => {
                const previousIndex = this.currentIndex;
                originalGoTo.call(this.existingInstance, index);
                this.currentIndex = this.existingInstance.getCurrentIndex ? this.existingInstance.getCurrentIndex() : index;
                this.emit('slideChange', { index: this.currentIndex, previousIndex });
            };
        }
        
        // Override nextSlide
        if (originalNext) {
            this.existingInstance.nextSlide = () => {
                const previousIndex = this.currentIndex;
                originalNext.call(this.existingInstance);
                this.currentIndex = this.existingInstance.getCurrentIndex ? this.existingInstance.getCurrentIndex() : (this.currentIndex + 1) % this.totalSlides;
                this.emit('slideChange', { index: this.currentIndex, previousIndex });
            };
        }
        
        // Override prevSlide
        if (originalPrev) {
            this.existingInstance.prevSlide = () => {
                const previousIndex = this.currentIndex;
                originalPrev.call(this.existingInstance);
                this.currentIndex = this.existingInstance.getCurrentIndex ? this.existingInstance.getCurrentIndex() : (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
                this.emit('slideChange', { index: this.currentIndex, previousIndex });
            };
        }
        
        // Override destroy
        if (originalDestroy) {
            this.existingInstance.destroy = () => {
                originalDestroy.call(this.existingInstance);
                this.emit('destroy');
            };
        }
    }
    
    /**
     * Initialize new instance (if not using existing)
     */
    _init() {
        // This would be called if carousel is initialized programmatically
        // For now, we rely on existing inline script initialization
        // Future: could initialize here if needed
    }
    
    /**
     * Go to specific slide
     * 
     * @param {number} index Slide index (0-based)
     * @returns {void}
     */
    goTo(index) {
        if (this.existingInstance && this.existingInstance.goToSlide) {
            this.existingInstance.goToSlide(index);
        } else {
            const previousIndex = this.currentIndex;
            this.currentIndex = Math.max(0, Math.min(index, this.totalSlides - 1));
            this._updateCarousel();
            this.emit('slideChange', { index: this.currentIndex, previousIndex });
        }
    }
    
    /**
     * Go to next slide
     * 
     * @returns {void}
     */
    next() {
        if (this.existingInstance && this.existingInstance.nextSlide) {
            this.existingInstance.nextSlide();
        } else {
            const previousIndex = this.currentIndex;
            this.currentIndex = (this.currentIndex + 1) % this.totalSlides;
            this._updateCarousel();
            this.emit('slideChange', { index: this.currentIndex, previousIndex });
        }
    }
    
    /**
     * Go to previous slide
     * 
     * @returns {void}
     */
    prev() {
        if (this.existingInstance && this.existingInstance.prevSlide) {
            this.existingInstance.prevSlide();
        } else {
            const previousIndex = this.currentIndex;
            this.currentIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
            this._updateCarousel();
            this.emit('slideChange', { index: this.currentIndex, previousIndex });
        }
    }
    
    /**
     * Update carousel display (internal)
     */
    _updateCarousel() {
        const slides = Array.from(this.element.querySelectorAll('.carousel-slide'));
        const dots = Array.from(this.element.querySelectorAll('.carousel-dot'));
        const thumbnails = Array.from(this.element.querySelectorAll('.carousel-thumbnail'));
        const track = this.element.querySelector('.carousel-track');
        
        slides.forEach((slide, index) => {
            const isActive = index === this.currentIndex;
            slide.classList.toggle('active', isActive);
            slide.setAttribute('aria-hidden', !isActive);
            if (isActive) {
                slide.setAttribute('aria-current', 'true');
            } else {
                slide.removeAttribute('aria-current');
            }
        });
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
        });
        
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active', index === this.currentIndex);
        });
        
        if (track) {
            const slideWidth = slides[0]?.offsetWidth || 0;
            const gap = parseInt(getComputedStyle(track).gap) || 0;
            const translateX = -(this.currentIndex * (slideWidth + gap));
            track.style.transform = `translateX(${translateX}px)`;
        }
    }
    
    /**
     * Get current slide index
     * 
     * @returns {number} Current slide index (0-based)
     */
    getCurrentIndex() {
        if (this.existingInstance && this.existingInstance.getCurrentIndex) {
            return this.existingInstance.getCurrentIndex();
        }
        return this.currentIndex;
    }
    
    /**
     * Get total number of slides
     * 
     * @returns {number} Total slides
     */
    getTotalSlides() {
        return this.totalSlides;
    }
    
    /**
     * Start autoplay
     * 
     * @returns {void}
     */
    startAutoplay() {
        if (this.existingInstance && this.existingInstance.resetAutoplay) {
            this.existingInstance.resetAutoplay();
        }
        this.emit('autoplayStart');
    }
    
    /**
     * Stop autoplay
     * 
     * @returns {void}
     */
    stopAutoplay() {
        if (this.existingInstance && this.existingInstance.destroy) {
            // Pause autoplay by clearing timer
            const carouselEl = this.element;
            const autoplayTimer = carouselEl._autoplayTimer;
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                carouselEl._autoplayTimer = null;
            }
        }
        this.emit('autoplayStop');
    }
    
    /**
     * Add event listener
     * 
     * @param {string} event Event name
     * @param {Function} callback Callback function
     * @returns {void}
     */
    on(event, callback) {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, []);
        }
        this.listeners.get(event).push(callback);
    }
    
    /**
     * Remove event listener
     * 
     * @param {string} event Event name
     * @param {Function} callback Callback function
     * @returns {void}
     */
    off(event, callback) {
        const listeners = this.listeners.get(event);
        if (listeners) {
            const index = listeners.indexOf(callback);
            if (index > -1) {
                listeners.splice(index, 1);
            }
        }
    }
    
    /**
     * Emit event
     * 
     * @param {string} event Event name
     * @param {Object} data Event data
     * @returns {void}
     */
    emit(event, data = {}) {
        const listeners = this.listeners.get(event);
        if (listeners) {
            listeners.forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`Error in carousel event listener for ${event}:`, error);
                }
            });
        }
    }
    
    /**
     * Destroy instance
     * 
     * @returns {void}
     */
    destroy() {
        // Destroy existing instance if present
        if (this.existingInstance && this.existingInstance.destroy) {
            this.existingInstance.destroy();
        }
        
        // Clear all listeners
        this.listeners.clear();
        
        // Emit destroy event
        this.emit('destroy');
        
        // Clean up
        this.element = null;
        this.existingInstance = null;
    }
}
JS;
    }
}


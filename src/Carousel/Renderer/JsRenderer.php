<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Helper\JsMinifier;
use JulienLinard\Carousel\JavaScript\CarouselAPI;
use JulienLinard\Carousel\JavaScript\CarouselInstance;
use JulienLinard\Carousel\Translator\TranslatorInterface;

/**
 * JavaScript Renderer - renders only JavaScript code
 */
class JsRenderer extends AbstractRenderer
{
    /**
     * Render JavaScript code
     * 
     * @param Carousel $carousel The carousel to render
     * @return string JavaScript output
     */
    public function render(Carousel $carousel): string
    {
        // Initialize translator from carousel options
        $this->initializeTranslator($carousel);
        
        // Create render context
        $context = new RenderContext($carousel, $this->translator);
        
        return $this->renderJs($context);
    }

    /**
     * Render JavaScript code
     * 
     * @param RenderContext $context Render context
     * @return string JavaScript output
     */
    private function renderJs(RenderContext $context): string
    {
        $id = $context->getId();
        $options = $context->getOptions();
        
        // Only render JS once per carousel ID
        if (RenderCacheService::isRendered($id, 'js')) {
            return '';
        }
        RenderCacheService::markAsRendered($id, 'js');
        
        // Include CarouselAPI and CarouselInstance (only once globally)
        $apiIncluded = RenderCacheService::isApiRendered();
        if (!$apiIncluded) {
            RenderCacheService::markApiAsRendered();
            $js = '<script id="carousel-api">';
            $js .= CarouselAPI::generate();
            $js .= "\n";
            $js .= CarouselInstance::generate();
            $js .= '</script>';
            $js .= "\n";
        } else {
            $js = '';
        }
        
        $carouselScript = '<script id="carousel-script-' . $this->escape($id) . '">';
        $carouselScript .= '(function() {';
        $carouselScript .= 'const carousel = document.getElementById("carousel-' . $this->escape($id) . '");';
        $carouselScript .= 'if (!carousel) return;';
        
        $carouselScript .= $this->getCarouselJs($id, $context);
        
        $carouselScript .= '})();';
        $carouselScript .= '</script>';
        
        // Minify JS if option is enabled (only the carousel script, not the API)
        $minify = $options['minify'] ?? false;
        if ($minify) {
            // Extract JS content (between <script> tags) from carousel script only
            $jsContent = preg_replace('/<script[^>]*>/', '', $carouselScript);
            $jsContent = preg_replace('/<\/script>/', '', $jsContent);
            $minified = JsMinifier::minify($jsContent);
            $carouselScript = '<script id="carousel-script-' . $this->escape($id) . '">' . $minified . '</script>';
        }
        
        $js .= $carouselScript;
        
        return $js;
    }

    /**
     * Get carousel JavaScript
     * 
     * @param string $id Carousel ID
     * @param RenderContext $context Render context
     * @return string JavaScript output
     */
    private function getCarouselJs(string $id, RenderContext $context): string
    {
        $options = $context->getOptions();
        $autoplay = $options['autoplay'] ?? true ? 'true' : 'false';
        $autoplayInterval = $options['autoplayInterval'] ?? 5000;
        $loop = $options['loop'] ?? true ? 'true' : 'false';
        $transition = $options['transition'] ?? 'slide';
        $keyboardNav = $options['keyboardNavigation'] ?? true ? 'true' : 'false';
        $touchSwipe = $options['touchSwipe'] ?? true ? 'true' : 'false';
        
        // Get translations for JavaScript
        $translations = [
            'slide_of' => $this->translator->translate('slide_of', null, ['current' => '{current}', 'total' => '{total}']),
            'image_unavailable' => $this->translator->translate('image_unavailable'),
        ];
        
        // Escape for JavaScript
        $translationsJs = json_encode($translations, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        
        return <<<JS
const carouselId = "{$id}";
const carouselEl = carousel;
const track = carouselEl.querySelector('.carousel-track');
const slides = Array.from(carouselEl.querySelectorAll('.carousel-slide'));
const prevBtn = carouselEl.querySelector('.carousel-arrow-prev');
const nextBtn = carouselEl.querySelector('.carousel-arrow-next');
const dots = Array.from(carouselEl.querySelectorAll('.carousel-dot'));
const thumbnails = Array.from(carouselEl.querySelectorAll('.carousel-thumbnail'));

// Translations
const translations = {$translationsJs};

let currentIndex = 0;
let autoplayTimer = null;
let resizeTimer = null;
let resizeRAF = null;
let imageObserver = null;
const autoplay = {$autoplay};
const autoplayInterval = {$autoplayInterval};
const loop = {$loop};
const transition = "{$transition}";
const keyboardNav = {$keyboardNav};
const touchSwipe = {$touchSwipe};

// Store event handler references for cleanup
const handlePrevClick = () => prevSlide();
const handleNextClick = () => nextSlide();
const handleKeydown = (e) => {
    if (e.key === 'ArrowLeft') {
        prevSlide();
    } else if (e.key === 'ArrowRight') {
        nextSlide();
    }
};
const handleTouchStart = (e) => {
    touchStartX = e.changedTouches[0].screenX;
};
const handleTouchEnd = (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
};
const handleMouseEnter = () => {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
    }
};
const handleMouseLeave = () => {
    resetAutoplay();
};
const handleResize = () => {
    if (resizeRAF) return;
    resizeRAF = requestAnimationFrame(() => {
        updateCarousel();
        resizeRAF = null;
    });
};

let touchStartX = 0;
let touchEndX = 0;

function updateCarousel() {
    slides.forEach((slide, index) => {
        const isActive = index === currentIndex;
        slide.classList.toggle('active', isActive);
        slide.setAttribute('aria-hidden', !isActive);
        if (isActive) {
            slide.setAttribute('aria-current', 'true');
        } else {
            slide.removeAttribute('aria-current');
        }
    });
    
    if (dots.length > 0) {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }
    
    if (thumbnails.length > 0) {
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active', index === currentIndex);
        });
    }
    
    if (track) {
        if (transition === 'slide') {
            const slideWidth = slides[0]?.offsetWidth || 0;
            const gap = parseInt(getComputedStyle(track).gap) || 0;
            const translateX = -(currentIndex * (slideWidth + gap));
            track.style.transform = `translateX(\${translateX}px)`;
        }
        // For fade transition, opacity is handled by CSS
    }
    
    // Announce slide change to screen readers
    const announcement = carouselEl.querySelector('.carousel-announcement');
    if (announcement) {
        const slideText = translations.slide_of
            .replace('{current}', currentIndex + 1)
            .replace('{total}', slides.length);
        announcement.textContent = slideText;
    }
}

function goToSlide(index) {
    if (index < 0) {
        currentIndex = loop ? slides.length - 1 : 0;
    } else if (index >= slides.length) {
        currentIndex = loop ? 0 : slides.length - 1;
    } else {
        currentIndex = index;
    }
    updateCarousel();
    resetAutoplay();
}

function nextSlide() {
    goToSlide(currentIndex + 1);
}

function prevSlide() {
    goToSlide(currentIndex - 1);
}

function resetAutoplay() {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
    }
    if (autoplay) {
        autoplayTimer = setInterval(nextSlide, autoplayInterval);
    }
}

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            nextSlide();
        } else {
            prevSlide();
        }
    }
}

// Navigation buttons
if (prevBtn) {
    prevBtn.addEventListener('click', handlePrevClick);
}

if (nextBtn) {
    nextBtn.addEventListener('click', handleNextClick);
}

// Dots navigation - store handlers for cleanup
const dotHandlers = [];
dots.forEach((dot, index) => {
    const handler = () => goToSlide(index);
    dotHandlers.push({ dot, handler });
    dot.addEventListener('click', handler);
});

// Thumbnails navigation - store handlers for cleanup
const thumbnailHandlers = [];
thumbnails.forEach((thumb, index) => {
    const handler = () => goToSlide(index);
    thumbnailHandlers.push({ thumb, handler });
    thumb.addEventListener('click', handler);
});

// Keyboard navigation
if (keyboardNav) {
    carouselEl.addEventListener('keydown', handleKeydown);
    carouselEl.setAttribute('tabindex', '0');
}

// Touch swipe
if (touchSwipe) {
    carouselEl.addEventListener('touchstart', handleTouchStart);
    carouselEl.addEventListener('touchend', handleTouchEnd);
}

// Pause autoplay on hover
if (autoplay) {
    carouselEl.addEventListener('mouseenter', handleMouseEnter);
    carouselEl.addEventListener('mouseleave', handleMouseLeave);
}

// Lazy loading with Intersection Observer
if (typeof IntersectionObserver !== 'undefined') {
    imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            }
        });
    }, { 
        rootMargin: '50px',
        threshold: 0.01 
    });
    
    // Observe all images with data-src
    const lazyImages = carouselEl.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });
}

// Check for prefers-reduced-motion and disable autoplay if needed
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (prefersReducedMotion && autoplay) {
    autoplay = false;
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
}

// Image error handling
const placeholderImage = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iODAwIiBoZWlnaHQ9IjQwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBub24gZGlzcG9uaWJsZTwvdGV4dD48L3N2Zz4=';
const images = carouselEl.querySelectorAll('img');
const imageUnavailableText = translations.image_unavailable;
images.forEach(img => {
    img.addEventListener('error', function() {
        this.src = placeholderImage;
        this.alt = imageUnavailableText;
        this.setAttribute('aria-label', imageUnavailableText);
    });
});

// Loading indicator management
const loadingEl = carouselEl.querySelector('.carousel-loading');
let loadedCount = 0;
const totalImages = images.length;

if (totalImages === 0 && loadingEl) {
    loadingEl.classList.add('hidden');
} else {
    images.forEach(img => {
        if (img.complete) {
            loadedCount++;
            checkAllLoaded();
        } else {
            img.addEventListener('load', () => {
                loadedCount++;
                checkAllLoaded();
            });
            img.addEventListener('error', () => {
                loadedCount++;
                checkAllLoaded();
            });
        }
    });
}

function checkAllLoaded() {
    if (loadedCount >= totalImages && loadingEl) {
        loadingEl.classList.add('hidden');
        loadingEl.setAttribute('aria-hidden', 'true');
    }
}

// Initialize
updateCarousel();
resetAutoplay();

// Handle window resize with requestAnimationFrame
window.addEventListener('resize', handleResize);

// Cleanup function
function destroy() {
    // Clear timers
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
    if (resizeTimer) {
        clearTimeout(resizeTimer);
        resizeTimer = null;
    }
    if (resizeRAF) {
        cancelAnimationFrame(resizeRAF);
        resizeRAF = null;
    }
    
    // Remove event listeners
    if (prevBtn) {
        prevBtn.removeEventListener('click', handlePrevClick);
    }
    if (nextBtn) {
        nextBtn.removeEventListener('click', handleNextClick);
    }
    
    dotHandlers.forEach(({ dot, handler }) => {
        dot.removeEventListener('click', handler);
    });
    
    thumbnailHandlers.forEach(({ thumb, handler }) => {
        thumb.removeEventListener('click', handler);
    });
    
    if (keyboardNav) {
        carouselEl.removeEventListener('keydown', handleKeydown);
    }
    
    if (touchSwipe) {
        carouselEl.removeEventListener('touchstart', handleTouchStart);
        carouselEl.removeEventListener('touchend', handleTouchEnd);
    }
    
    if (autoplay) {
        carouselEl.removeEventListener('mouseenter', handleMouseEnter);
        carouselEl.removeEventListener('mouseleave', handleMouseLeave);
    }
    
    window.removeEventListener('resize', handleResize);
    
    // Unobserve images
    if (imageObserver) {
        const lazyImages = carouselEl.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            imageObserver.unobserve(img);
        });
        imageObserver.disconnect();
        imageObserver = null;
    }
}

// Expose destroy method and controls (for backward compatibility)
window.carouselInstances = window.carouselInstances || {};
window.carouselInstances[carouselId] = { 
    destroy, 
    goToSlide, 
    nextSlide, 
    prevSlide,
    getCurrentIndex: () => currentIndex,
    resetAutoplay: resetAutoplay
};

// Initialize via CarouselAPI if available
if (typeof window.CarouselAPI !== 'undefined') {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.CarouselAPI.init(carouselId);
        });
    } else {
        window.CarouselAPI.init(carouselId);
    }
}

JS;
    }
}


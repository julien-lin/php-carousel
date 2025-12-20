<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Renderer;

use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\Helper\CssMinifier;
use JulienLinard\Carousel\Theme\Theme;
use JulienLinard\Carousel\Translator\TranslatorInterface;

/**
 * CSS Renderer - renders only CSS styles
 */
class CssRenderer extends AbstractRenderer
{
    /**
     * Render CSS styles
     * 
     * @param Carousel $carousel The carousel to render
     * @return string CSS output
     */
    public function render(Carousel $carousel): string
    {
        // Initialize translator from carousel options
        $this->initializeTranslator($carousel);
        
        // Create render context
        $context = new RenderContext($carousel, $this->translator);
        
        return $this->renderCss($context);
    }

    /**
     * Render CSS styles
     * 
     * @param RenderContext $context Render context
     * @return string CSS output
     */
    private function renderCss(RenderContext $context): string
    {
        $id = $context->getId();
        $options = $context->getOptions();
        $type = $context->getType();
        
        // Only render CSS once per carousel ID
        if (RenderCacheService::isRendered($id, 'html')) {
            return '';
        }
        RenderCacheService::markAsRendered($id, 'html');
        
        $cssId = '#carousel-' . $id;
        $gap = $options['gap'] ?? 16;
        $transitionDuration = ($options['transitionDuration'] ?? 500) . 'ms';
        
        // Check if theme is enabled
        $themeMode = $options['theme'] ?? 'auto';
        $hasCustomColors = isset($options['themeColors']) && is_array($options['themeColors']);
        $themeEnabled = ($themeMode !== 'auto') || $hasCustomColors;
        
        $css = '<style id="carousel-style-' . $this->escape($id) . '">';
        
        // Theme CSS variables (only if theme is enabled)
        if ($themeEnabled) {
            $css .= $this->getThemeCss($cssId, $context);
        }
        
        // Base styles (with or without theme variables)
        $css .= $this->getBaseCss($cssId, $gap, $transitionDuration, $themeEnabled);
        
        // Type-specific styles
        switch ($type) {
            case Carousel::TYPE_IMAGE:
                $css .= $this->getImageCss($cssId, $context, $themeEnabled);
                break;
            case Carousel::TYPE_CARD:
                $css .= $this->getCardCss($cssId, $context, $themeEnabled);
                break;
            case Carousel::TYPE_TESTIMONIAL:
                $css .= $this->getTestimonialCss($cssId, $themeEnabled);
                break;
            case Carousel::TYPE_GALLERY:
                $css .= $this->getGalleryCss($cssId, $themeEnabled);
                break;
            case Carousel::TYPE_INFINITE:
                // Infinite carousel uses card CSS with multiple items
                $css .= $this->getCardCss($cssId, $context, $themeEnabled);
                break;
        }
        
        // Custom transitions and animations
        $css .= $this->getCustomTransitionCss($cssId, $context);
        $css .= $this->getCustomAnimationsCss($cssId, $context);
        
        // Responsive styles
        if ($options['responsive'] ?? true) {
            $css .= $this->getResponsiveCss($cssId, $context);
        }
        
        $css .= '</style>';
        
        // Minify CSS if option is enabled
        $minify = $options['minify'] ?? false;
        if ($minify) {
            // Extract CSS content (between <style> tags)
            $cssContent = preg_replace('/<style[^>]*>/', '', $css);
            $cssContent = preg_replace('/<\/style>/', '', $cssContent);
            $minified = CssMinifier::minify($cssContent);
            $css = '<style id="carousel-style-' . $this->escape($id) . '">' . $minified . '</style>';
        }
        
        return $css;
    }

    /**
     * Get theme CSS variables
     * 
     * @param string $cssId CSS selector ID
     * @param RenderContext $context Render context
     * @return string CSS output
     */
    private function getThemeCss(string $cssId, RenderContext $context): string
    {
        $options = $context->getOptions();
        
        // Check if theme is explicitly set (not default 'auto') or custom colors provided
        $themeMode = $options['theme'] ?? 'auto';
        $hasCustomColors = isset($options['themeColors']) && is_array($options['themeColors']);
        
        // If theme is default 'auto' and no custom colors, don't generate theme CSS
        // This maintains backward compatibility - CSS will use direct values, not variables
        if ($themeMode === 'auto' && !$hasCustomColors) {
            return '';
        }
        
        $theme = Theme::fromArray($options);
        $mode = $theme->getMode();
        $lightColors = $theme->getLightColors();
        $darkColors = $theme->getDarkColors();
        
        // Map color keys to CSS variable names
        $varMap = [
            'background' => 'background',
            'text' => 'text',
            'arrow' => 'arrow',
            'arrowHover' => 'arrow-hover',
            'dot' => 'dot',
            'dotActive' => 'dot-active',
            'dotHover' => 'dot-hover',
            'border' => 'border',
            'shadow' => 'shadow',
            'shadowHover' => 'shadow-hover',
            'cardBackground' => 'card-background',
            'cardText' => 'card-text',
            'cardContent' => 'card-content',
            'link' => 'link',
            'linkHover' => 'link-hover',
            'loadingSpinner' => 'loading-spinner',
        ];
        
        $css = "{$cssId} {\n";
        
        // Generate CSS variables for light theme (default)
        foreach ($varMap as $key => $varName) {
            $value = $lightColors[$key] ?? '';
            if ($value) {
                $css .= "    --carousel-{$varName}: {$value};\n";
            }
        }
        
        $css .= "}\n\n";
        
        // Add dark theme support based on mode
        if ($mode === Theme::MODE_DARK) {
            // Dark mode: always use dark colors
            $css .= "{$cssId}[data-theme=\"dark\"],\n";
            $css .= "{$cssId} {\n";
            foreach ($varMap as $key => $varName) {
                $value = $darkColors[$key] ?? '';
                if ($value) {
                    $css .= "    --carousel-{$varName}: {$value};\n";
                }
            }
            $css .= "}\n\n";
        } elseif ($mode === Theme::MODE_AUTO) {
            // Auto mode: use prefers-color-scheme media query
            $css .= "@media (prefers-color-scheme: dark) {\n";
            $css .= "    {$cssId}[data-theme=\"auto\"],\n";
            $css .= "    {$cssId}:not([data-theme]) {\n";
            foreach ($varMap as $key => $varName) {
                $value = $darkColors[$key] ?? '';
                if ($value) {
                    $css .= "        --carousel-{$varName}: {$value};\n";
                }
            }
            $css .= "    }\n";
            $css .= "}\n\n";
            
            // Explicit dark theme attribute
            $css .= "{$cssId}[data-theme=\"dark\"] {\n";
            foreach ($varMap as $key => $varName) {
                $value = $darkColors[$key] ?? '';
                if ($value) {
                    $css .= "    --carousel-{$varName}: {$value};\n";
                }
            }
            $css .= "}\n\n";
        }
        
        // Explicit light theme (for auto and light modes)
        if ($mode === Theme::MODE_AUTO || $mode === Theme::MODE_LIGHT) {
            $css .= "{$cssId}[data-theme=\"light\"] {\n";
            foreach ($varMap as $key => $varName) {
                $value = $lightColors[$key] ?? '';
                if ($value) {
                    $css .= "    --carousel-{$varName}: {$value};\n";
                }
            }
            $css .= "}\n\n";
        }
        
        return $css;
    }

    /**
     * Get CSS value with or without theme variable
     * 
     * @param string $varName CSS variable name
     * @param string $defaultValue Default value
     * @param bool $themeEnabled Whether theme is enabled
     * @return string CSS value
     */
    private function getCssValue(string $varName, string $defaultValue, bool $themeEnabled): string
    {
        return $themeEnabled ? "var(--carousel-{$varName}, {$defaultValue})" : $defaultValue;
    }

    /**
     * Get base CSS
     * 
     * @param string $cssId CSS selector ID
     * @param int $gap Gap between items
     * @param string $transitionDuration Transition duration
     * @param bool $themeEnabled Whether theme variables are enabled
     * @return string CSS output
     */
    private function getBaseCss(string $cssId, int $gap, string $transitionDuration, bool $themeEnabled = false): string
    {
        $dotBg = $this->getCssValue('dot', 'rgba(0, 0, 0, 0.2)', $themeEnabled);
        $dotHover = $this->getCssValue('dot-hover', 'rgba(0, 0, 0, 0.4)', $themeEnabled);
        $dotActive = $this->getCssValue('dot-active', 'rgba(0, 0, 0, 0.8)', $themeEnabled);
        $spinnerColor = $this->getCssValue('loading-spinner', '#0066cc', $themeEnabled);
        
        return <<<CSS
{$cssId} {
    position: relative;
    width: 100%;
    margin: 0 auto;
}

{$cssId} .carousel-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

{$cssId}[data-carousel-transition="fade"] .carousel-wrapper {
    min-height: 300px;
}

{$cssId}[data-carousel-type="image"] .carousel-wrapper {
    min-height: 300px;
}

{$cssId} .carousel-track {
    display: flex;
    transition: transform {$transitionDuration} cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform;
}

/* Screen reader only */
{$cssId} .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Loading indicator */
{$cssId} .carousel-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 100;
    pointer-events: none;
}

{$cssId} .carousel-loading.hidden {
    display: none;
}

{$cssId} .carousel-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-top-color: {$spinnerColor};
    border-radius: 50%;
    animation: carousel-spin 0.8s linear infinite;
}

@keyframes carousel-spin {
    to { transform: rotate(360deg); }
}

/* Respect prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
    {$cssId} .carousel-track,
    {$cssId} .carousel-slide,
    {$cssId} .carousel-spinner {
        transition: none !important;
        animation: none !important;
    }
}

{$cssId} .carousel-slide {
    flex: 0 0 100%;
    min-width: 0;
    position: relative;
}

{$cssId}[data-carousel-type="image"] .carousel-slide {
    min-height: 300px;
}

{$cssId}[data-carousel-transition="fade"] .carousel-track {
    position: relative;
}

{$cssId}[data-carousel-transition="fade"] .carousel-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    opacity: 0;
    transition: opacity {$transitionDuration} ease-in-out;
    z-index: 1;
}

{$cssId}[data-carousel-transition="fade"] .carousel-slide.active {
    opacity: 1;
    z-index: 2;
    position: relative;
}

{$cssId}[data-carousel-transition="slide"] .carousel-slide {
    opacity: 1;
}

{$cssId} .carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    color: #333;
}

{$cssId} .carousel-arrow:hover {
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transform: translateY(-50%) scale(1.1);
}

{$cssId} .carousel-arrow:active {
    transform: translateY(-50%) scale(0.95);
}

{$cssId} .carousel-arrow-prev {
    left: 16px;
}

{$cssId} .carousel-arrow-next {
    right: 16px;
}

{$cssId} .carousel-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
    padding: 0;
}

{$cssId} .carousel-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: none;
    background: {$dotBg};
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
}

{$cssId} .carousel-dot:hover {
    background: {$dotHover};
    transform: scale(1.2);
}

{$cssId} .carousel-dot.active {
    background: {$dotActive};
    width: 24px;
    border-radius: 6px;
}

CSS;
    }

    /**
     * Get image carousel CSS
     * 
     * @param string $cssId CSS selector ID
     * @param RenderContext $context Render context
     * @param bool $themeEnabled Whether theme variables are enabled
     * @return string CSS output
     */
    private function getImageCss(string $cssId, RenderContext $context, bool $themeEnabled = false): string
    {
        $options = $context->getOptions();
        $height = $options['height'] ?? 'auto';
        $minHeight = ($height === 'auto') ? '400px' : $height;
        
        return <<<CSS
{$cssId} .carousel-image-link {
    display: block;
    position: relative;
    width: 100%;
    height: 100%;
}

{$cssId} .carousel-image {
    width: 100%;
    height: {$height};
    display: block;
    object-fit: cover;
    max-width: 100%;
}

{$cssId}[data-carousel-type="image"] .carousel-wrapper {
    min-height: {$minHeight};
}

{$cssId} .carousel-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    color: white;
    padding: 32px 24px 24px;
}

{$cssId} .carousel-title {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 600;
}

{$cssId} .carousel-content {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
}

CSS;
    }

    /**
     * Get card carousel CSS
     * 
     * @param string $cssId CSS selector ID
     * @param RenderContext $context Render context
     * @return string CSS output
     */
    private function getCardCss(string $cssId, RenderContext $context, bool $themeEnabled = false): string
    {
        $options = $context->getOptions();
        $itemsPerSlide = $options['itemsPerSlide'] ?? 3;
        $slideWidth = 100 / $itemsPerSlide;
        $gap = $options['gap'] ?? 16;
        
        $cardBg = $this->getCssValue('card-background', 'white', $themeEnabled);
        $cardShadow = $this->getCssValue('shadow', 'rgba(0, 0, 0, 0.1)', $themeEnabled);
        $cardShadowHover = $this->getCssValue('shadow-hover', 'rgba(0, 0, 0, 0.15)', $themeEnabled);
        $cardText = $this->getCssValue('card-text', '#1a1a1a', $themeEnabled);
        $cardContent = $this->getCssValue('card-content', '#666', $themeEnabled);
        $cardLink = $this->getCssValue('link', '#0066cc', $themeEnabled);
        $cardLinkHover = $this->getCssValue('link-hover', '#0052a3', $themeEnabled);
        
        return <<<CSS
{$cssId} .carousel-track {
    gap: {$gap}px;
}

{$cssId} .carousel-slide {
    flex: 0 0 calc({$slideWidth}% - {$gap}px);
}

{$cssId} .carousel-card {
    background: {$cardBg};
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px {$cardShadow};
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

{$cssId} .carousel-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px {$cardShadowHover};
}

{$cssId} .carousel-card-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

{$cssId} .carousel-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

{$cssId} .carousel-card:hover .carousel-card-image img {
    transform: scale(1.05);
}

{$cssId} .carousel-card-body {
    padding: 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

{$cssId} .carousel-card-title {
    margin: 0 0 12px 0;
    font-size: 20px;
    font-weight: 600;
    color: {$cardText};
}

{$cssId} .carousel-card-content {
    margin: 0 0 16px 0;
    color: {$cardContent};
    line-height: 1.6;
    flex: 1;
}

{$cssId} .carousel-card-link {
    color: {$cardLink};
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

{$cssId} .carousel-card-link:hover {
    color: {$cardLinkHover};
    text-decoration: underline;
}

CSS;
    }

    /**
     * Get testimonial carousel CSS
     * 
     * @param string $cssId CSS selector ID
     * @param bool $themeEnabled Whether theme variables are enabled
     * @return string CSS output
     */
    private function getTestimonialCss(string $cssId, bool $themeEnabled = false): string
    {
        return <<<CSS
{$cssId} .carousel-testimonial {
    text-align: center;
    padding: 48px 24px;
    max-width: 800px;
    margin: 0 auto;
}

{$cssId} .carousel-testimonial-quote {
    margin: 0 0 32px 0;
    font-size: 20px;
    line-height: 1.8;
    color: #333;
    font-style: italic;
}

{$cssId} .carousel-testimonial-quote p {
    margin: 0;
}

{$cssId} .carousel-testimonial-author {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
}

{$cssId} .carousel-testimonial-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
}

{$cssId} .carousel-testimonial-name {
    font-weight: 600;
    font-style: normal;
    color: #1a1a1a;
}

CSS;
    }

    /**
     * Get gallery carousel CSS
     * 
     * @param string $cssId CSS selector ID
     * @param bool $themeEnabled Whether theme variables are enabled
     * @return string CSS output
     */
    private function getGalleryCss(string $cssId, bool $themeEnabled = false): string
    {
        return <<<CSS
{$cssId} .carousel-gallery-item {
    position: relative;
    width: 100%;
}

{$cssId} .carousel-gallery-image {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
    max-height: 600px;
    margin: 0 auto;
}

{$cssId} .carousel-gallery-caption {
    padding: 16px;
    text-align: center;
    background: rgba(255, 255, 255, 0.95);
}

{$cssId} .carousel-gallery-title {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 600;
}

{$cssId} .carousel-gallery-content {
    margin: 0;
    color: #666;
    font-size: 14px;
}

{$cssId} .carousel-thumbnails {
    display: flex;
    gap: 8px;
    margin-top: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

{$cssId} .carousel-thumbnail {
    border: 2px solid transparent;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    padding: 0;
    background: none;
    transition: all 0.3s ease;
    opacity: 0.6;
}

{$cssId} .carousel-thumbnail:hover {
    opacity: 0.8;
    transform: scale(1.05);
}

{$cssId} .carousel-thumbnail.active {
    border-color: #0066cc;
    opacity: 1;
}

{$cssId} .carousel-thumbnail img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    display: block;
}

CSS;
    }

    /**
     * Get custom transition CSS
     * 
     * @param string $cssId CSS selector ID
     * @param RenderContext $context Render context
     * @return string CSS output
     */
    private function getCustomTransitionCss(string $cssId, RenderContext $context): string
    {
        $options = $context->getOptions();
        $customTransition = $options['customTransition'] ?? null;
        
        if (!$customTransition || !is_array($customTransition)) {
            return '';
        }
        
        $duration = ($customTransition['duration'] ?? 500) . 'ms';
        $timingFunction = $customTransition['timingFunction'] ?? 'cubic-bezier(0.4, 0, 0.2, 1)';
        $properties = $customTransition['properties'] ?? ['transform'];
        
        $transitionValue = implode(', ', array_map(function($prop) use ($duration, $timingFunction) {
            return "{$prop} {$duration} {$timingFunction}";
        }, $properties));
        
        return <<<CSS
{$cssId}[data-carousel-transition="custom"] .carousel-track {
    transition: {$transitionValue};
}

{$cssId}[data-carousel-transition="custom"] .carousel-slide {
    transition: {$transitionValue};
}

CSS;
    }

    /**
     * Get custom animations CSS
     * 
     * @param string $cssId CSS selector ID
     * @param RenderContext $context Render context
     * @return string CSS output
     */
    private function getCustomAnimationsCss(string $cssId, RenderContext $context): string
    {
        $options = $context->getOptions();
        $animations = $options['animations'] ?? [];
        
        if (empty($animations) || !is_array($animations)) {
            return '';
        }
        
        $css = '';
        
        // Generate CSS for each animation
        foreach ($animations as $name => $value) {
            // If value is a string (e.g., "slideInFromRight 0.5s ease-out")
            if (is_string($value)) {
                // Add animation class
                $css .= "{$cssId} .carousel-animation-{$name} {\n";
                $css .= "    animation: {$value};\n";
                $css .= "}\n\n";
            } elseif (is_array($value)) {
                // If value is an array with keyframes definition
                if (isset($value['keyframes'])) {
                    // Generate @keyframes
                    $keyframesName = $value['keyframes']['name'] ?? "carousel-{$name}";
                    $keyframes = $value['keyframes']['steps'] ?? [];
                    
                    $css .= "@keyframes {$keyframesName} {\n";
                    foreach ($keyframes as $step => $properties) {
                        $stepPercent = is_numeric($step) ? "{$step}%" : $step;
                        $css .= "    {$stepPercent} {\n";
                        foreach ($properties as $prop => $val) {
                            $css .= "        {$prop}: {$val};\n";
                        }
                        $css .= "    }\n";
                    }
                    $css .= "}\n\n";
                    
                    // Add animation class
                    $duration = $value['duration'] ?? '0.5s';
                    $timingFunction = $value['timingFunction'] ?? 'ease';
                    $delay = isset($value['delay']) ? " {$value['delay']}" : '';
                    $iterationCount = isset($value['iterationCount']) ? " {$value['iterationCount']}" : '';
                    $direction = isset($value['direction']) ? " {$value['direction']}" : '';
                    
                    $css .= "{$cssId} .carousel-animation-{$name} {\n";
                    $css .= "    animation: {$keyframesName} {$duration} {$timingFunction}{$delay}{$iterationCount}{$direction};\n";
                    $css .= "}\n\n";
                } else {
                    // Simple animation definition
                    $duration = $value['duration'] ?? '0.5s';
                    $timingFunction = $value['timingFunction'] ?? 'ease';
                    $delay = isset($value['delay']) ? " {$value['delay']}" : '';
                    $iterationCount = isset($value['iterationCount']) ? " {$value['iterationCount']}" : '';
                    $direction = isset($value['direction']) ? " {$value['direction']}" : '';
                    $keyframesName = $value['name'] ?? "carousel-{$name}";
                    
                    $css .= "{$cssId} .carousel-animation-{$name} {\n";
                    $css .= "    animation: {$keyframesName} {$duration} {$timingFunction}{$delay}{$iterationCount}{$direction};\n";
                    $css .= "}\n\n";
                }
            }
        }
        
        return $css;
    }

    /**
     * Get responsive CSS
     * 
     * @param string $cssId CSS selector ID
     * @param RenderContext $context Render context
     * @return string CSS output
     */
    private function getResponsiveCss(string $cssId, RenderContext $context): string
    {
        $options = $context->getOptions();
        $desktopItems = $options['itemsPerSlideDesktop'] ?? $options['itemsPerSlide'] ?? 1;
        $tabletItems = $options['itemsPerSlideTablet'] ?? $options['itemsPerSlide'] ?? 1;
        $mobileItems = $options['itemsPerSlideMobile'] ?? 1;
        $gap = $options['gap'] ?? 16;
        
        $desktopWidth = 100 / $desktopItems;
        $tabletWidth = 100 / $tabletItems;
        $mobileWidth = 100 / $mobileItems;
        
        return <<<CSS
@media (max-width: 768px) {
    {$cssId} .carousel-slide {
        flex: 0 0 calc({$mobileWidth}% - {$gap}px);
    }
    
    {$cssId} .carousel-arrow {
        width: 40px;
        height: 40px;
    }
    
    {$cssId} .carousel-arrow-prev {
        left: 8px;
    }
    
    {$cssId} .carousel-arrow-next {
        right: 8px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    {$cssId} .carousel-slide {
        flex: 0 0 calc({$tabletWidth}% - {$gap}px);
    }
}

@media (min-width: 1025px) {
    {$cssId} .carousel-slide {
        flex: 0 0 calc({$desktopWidth}% - {$gap}px);
    }
}

CSS;
    }
}


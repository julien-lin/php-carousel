# Performance Optimization

## Overview

The PHP Carousel library includes comprehensive performance optimizations to ensure fast loading times and optimal user experience.

## Minification

### CSS Minification

The CSS minifier (`CssMinifier`) performs aggressive optimization:

- ✅ Removes comments (single-line and multi-line)
- ✅ Removes unnecessary whitespace
- ✅ Removes units from zero values (`0px` → `0`)
- ✅ Shortens color values (`#ffffff` → `#fff`)
- ✅ Removes leading zeros in decimals (`0.5` → `.5`)
- ✅ Preserves strings and URLs
- ✅ Preserves `calc()` expressions

**Usage:**

```php
use JulienLinard\Carousel\Helper\CssMinifier;

$css = '.test { color: red; }';
$minified = CssMinifier::minify($css);
// Result: '.test{color:red}'
```

### JavaScript Minification

The JavaScript minifier (`JsMinifier`) performs similar optimizations:

- ✅ Removes comments (single-line and multi-line)
- ✅ Removes unnecessary whitespace
- ✅ Removes trailing semicolons before closing braces
- ✅ Preserves strings and regex patterns
- ✅ Optimizes operator spacing

**Usage:**

```php
use JulienLinard\Carousel\Helper\JsMinifier;

$js = 'var test = "value";';
$minified = JsMinifier::minify($js);
// Result: 'var test="value"'
```

**Enable minification in carousel options:**

```php
$carousel = Carousel::image('gallery', $images, [
    'minify' => true, // Minifies both CSS and JS
]);
```

## Performance Optimizer

The `PerformanceOptimizer` class provides utilities for advanced performance optimization:

### Critical CSS Extraction

Extract above-the-fold CSS for faster initial render:

```php
use JulienLinard\Carousel\Performance\PerformanceOptimizer;

$fullCss = $carousel->renderCss();
$criticalCss = PerformanceOptimizer::extractCriticalCss($fullCss, [
    '#carousel-gallery',
    '.carousel-container',
    '.carousel-wrapper',
]);
```

### Image Preloading

Preload critical images for faster display:

```php
$preloadLinks = PerformanceOptimizer::generatePreloadLinks($carousel);
// Returns: <link rel="preload" as="image" href="...">
```

### Resource Hints

Generate DNS prefetch and preconnect hints:

```php
$hints = PerformanceOptimizer::generateResourceHints([
    'cdn.example.com',
    'api.example.com',
]);
```

### Bundle Size Analysis

Calculate and analyze bundle sizes:

```php
$sizes = PerformanceOptimizer::calculateBundleSize(
    $carousel->renderHtml(),
    $carousel->renderCss(),
    $carousel->renderJs()
);

// Returns: ['html' => 1234, 'css' => 5678, 'js' => 9012, 'total' => 15924]

$recommendations = PerformanceOptimizer::getPerformanceRecommendations($sizes);
// Returns array of recommendations with severity levels
```

## Caching

### Render Cache Service

The `RenderCacheService` ensures CSS and JS are rendered only once per carousel:

- ✅ Prevents duplicate CSS/JS output
- ✅ Global API script rendered once
- ✅ Memory-based cache (fast, no I/O)

**Automatic:** The cache is managed automatically by the renderer.

## Lazy Loading

Images are lazy-loaded by default using Intersection Observer:

- ✅ Images load only when visible
- ✅ Reduces initial page load time
- ✅ Respects `prefers-reduced-motion`
- ✅ Fallback for browsers without Intersection Observer

## Virtualization

For carousels with 50+ items, virtualization is automatically enabled:

- ✅ Only visible slides are rendered
- ✅ Configurable buffer zone
- ✅ Reduces DOM size
- ✅ Improves scroll performance

**Configuration:**

```php
$carousel = Carousel::image('gallery', $manyImages, [
    'virtualization' => true,
    'virtualizationThreshold' => 50,
    'virtualizationBuffer' => 3,
]);
```

## Best Practices

1. **Enable minification in production:**
   ```php
   $carousel = Carousel::image('gallery', $images, [
       'minify' => true,
   ]);
   ```

2. **Use critical CSS for above-the-fold content:**
   ```php
   $criticalCss = PerformanceOptimizer::extractCriticalCss($fullCss);
   // Inline in <head>
   echo '<style>' . $criticalCss . '</style>';
   // Load full CSS asynchronously
   ```

3. **Preload first 3 images:**
   ```php
   $preloadLinks = PerformanceOptimizer::generatePreloadLinks($carousel);
   echo $preloadLinks; // In <head>
   ```

4. **Monitor bundle sizes:**
   ```php
   $sizes = PerformanceOptimizer::calculateBundleSize(...);
   if ($sizes['total'] > 200000) {
       // Consider optimization
   }
   ```

5. **Use CDN for images:**
   - Serve images from CDN
   - Use responsive images with `srcset`
   - Optimize image formats (WebP, AVIF)

## Performance Metrics

Target performance metrics:

- ✅ CSS bundle: < 50KB (minified)
- ✅ JS bundle: < 100KB (minified)
- ✅ Total bundle: < 200KB
- ✅ First Contentful Paint: < 1.5s
- ✅ Time to Interactive: < 3s

## Future Enhancements

- Tree-shaking for JavaScript
- CSS critical path extraction (automatic)
- Service Worker for offline caching
- Image optimization service integration (Cloudinary, Imgix)


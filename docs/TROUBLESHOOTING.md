# Troubleshooting Guide

Common issues and solutions when using PHP Carousel.

## Carousel Not Displaying

### Issue: Carousel HTML is empty

**Symptoms:**
- No HTML output
- Empty container

**Solutions:**
1. Check if items are added:
```php
$carousel = Carousel::image('test', []);
$carousel->addItem(['image' => 'test.jpg']);
echo $carousel->render();
```

2. Check cache:
```php
use JulienLinard\Carousel\Renderer\RenderCacheService;
RenderCacheService::clear();
```

3. Verify carousel ID is valid:
```php
$id = $carousel->getId();
echo $id; // Should not be empty
```

### Issue: CSS not loading

**Symptoms:**
- HTML renders but no styling
- Carousel appears unstyled

**Solutions:**
1. Check if CSS is rendered:
```php
$css = $carousel->renderCss();
echo $css; // Should contain <style> tag
```

2. Verify CSS is in `<head>`:
```html
<head>
    <?php echo $carousel->renderCss(); ?>
</head>
<body>
    <?php echo $carousel->renderHtml(); ?>
    <?php echo $carousel->renderJs(); ?>
</body>
```

3. Check for CSS conflicts:
```css
/* Ensure no global CSS overrides carousel styles */
#carousel-my-carousel {
    /* Your styles */
}
```

### Issue: JavaScript not working

**Symptoms:**
- Carousel displays but doesn't respond to clicks
- Navigation buttons don't work

**Solutions:**
1. Check if JavaScript is rendered:
```php
$js = $carousel->renderJs();
echo $js; // Should contain <script> tag
```

2. Verify JavaScript is before `</body>`:
```html
<body>
    <?php echo $carousel->renderHtml(); ?>
    <?php echo $carousel->renderJs(); ?>
</body>
```

3. Check browser console for errors:
```javascript
// Open browser console (F12)
// Look for JavaScript errors
```

4. Verify carousel ID matches:
```php
$id = $carousel->getId();
// JavaScript should reference: carousel-{$id}
```

## Styling Issues

### Issue: Carousel too wide/narrow

**Symptoms:**
- Carousel doesn't fit container
- Overflow issues

**Solutions:**
1. Set container width:
```css
#carousel-my-carousel {
    max-width: 1200px;
    margin: 0 auto;
}
```

2. Use responsive options:
```php
$carousel = Carousel::image('test', $images, [
    'itemsPerSlide' => 1,
    'itemsPerSlideDesktop' => 3,
]);
```

### Issue: Images not displaying

**Symptoms:**
- Broken image icons
- Placeholder images

**Solutions:**
1. Verify image URLs:
```php
foreach ($images as $image) {
    if (!filter_var($image, FILTER_VALIDATE_URL) && !file_exists($image)) {
        // Invalid image URL
    }
}
```

2. Check image permissions:
```php
// Ensure images are readable
if (file_exists($image) && !is_readable($image)) {
    chmod($image, 0644);
}
```

3. Use absolute URLs:
```php
$baseUrl = 'https://example.com';
$images = array_map(function($img) use ($baseUrl) {
    return $baseUrl . '/' . ltrim($img, '/');
}, $images);
```

### Issue: Custom styles not applying

**Symptoms:**
- CSS overrides not working
- Styles ignored

**Solutions:**
1. Increase CSS specificity:
```css
/* Bad */
.carousel-arrow {
    background: red;
}

/* Good */
#carousel-my-carousel .carousel-arrow {
    background: red;
}
```

2. Use `!important` (if necessary):
```css
#carousel-my-carousel .carousel-arrow {
    background: red !important;
}
```

3. Check CSS order:
```html
<!-- Carousel CSS first -->
<?php echo $carousel->renderCss(); ?>

<!-- Your custom CSS after -->
<style>
    /* Your overrides */
</style>
```

## Performance Issues

### Issue: Slow page load

**Symptoms:**
- Page takes long to load
- Carousel delays rendering

**Solutions:**
1. Enable minification:
```php
$carousel = Carousel::image('test', $images, [
    'minify' => true,
]);
```

2. Use virtualization for large carousels:
```php
$carousel = Carousel::image('test', $manyImages, [
    'virtualization' => true,
    'virtualizationThreshold' => 50,
]);
```

3. Optimize images:
- Compress images
- Use WebP format
- Serve from CDN

4. Use critical CSS:
```php
use JulienLinard\Carousel\Performance\PerformanceOptimizer;

$criticalCss = PerformanceOptimizer::extractCriticalCss($fullCss);
// Inline in <head>
```

### Issue: Memory issues with many items

**Symptoms:**
- PHP memory limit exceeded
- Slow rendering

**Solutions:**
1. Increase PHP memory limit:
```php
ini_set('memory_limit', '256M');
```

2. Use pagination instead of one large carousel:
```php
// Split into multiple carousels
$chunks = array_chunk($items, 20);
foreach ($chunks as $index => $chunk) {
    $carousel = Carousel::image("carousel-{$index}", $chunk);
    echo $carousel->render();
}
```

3. Enable virtualization:
```php
$carousel = Carousel::image('test', $items, [
    'virtualization' => true,
]);
```

## JavaScript Issues

### Issue: CarouselAPI not available

**Symptoms:**
- `window.CarouselAPI is undefined`
- Programmatic control doesn't work

**Solutions:**
1. Verify API script is included:
```php
$js = $carousel->renderJs();
// Should contain: window.CarouselAPI
```

2. Wait for DOM ready:
```javascript
document.addEventListener('DOMContentLoaded', function() {
    const carousel = window.CarouselAPI.get('my-carousel');
});
```

3. Check script order:
```html
<!-- Carousel HTML -->
<?php echo $carousel->renderHtml(); ?>

<!-- Carousel JavaScript (includes API) -->
<?php echo $carousel->renderJs(); ?>

<!-- Your custom JavaScript -->
<script>
    const carousel = window.CarouselAPI.get('my-carousel');
</script>
```

### Issue: Events not firing

**Symptoms:**
- Event listeners not called
- No console logs

**Solutions:**
1. Verify event name:
```javascript
// Correct
carousel.on('slideChange', (data) => {
    console.log(data);
});

// Incorrect
carousel.on('slidechange', (data) => { // lowercase
    console.log(data);
});
```

2. Check if carousel is initialized:
```javascript
const carousel = window.CarouselAPI.get('my-carousel');
if (!carousel) {
    console.error('Carousel not found');
}
```

3. Verify event is supported:
- `slideChange` - Fires when slide changes
- `autoplayStart` - Fires when autoplay starts
- `autoplayStop` - Fires when autoplay stops
- `destroy` - Fires when carousel is destroyed

## Accessibility Issues

### Issue: Screen reader not announcing

**Symptoms:**
- Screen reader doesn't announce slide changes
- No live region updates

**Solutions:**
1. Verify announcement element exists:
```html
<!-- Should be in HTML -->
<div class="sr-only carousel-announcement" aria-live="polite"></div>
```

2. Check JavaScript updates:
```javascript
// JavaScript should update announcement element
const announcement = carouselEl.querySelector('.carousel-announcement');
announcement.textContent = 'Slide X of Y';
```

3. Test with screen reader:
- NVDA (Windows)
- VoiceOver (macOS/iOS)

### Issue: Keyboard navigation not working

**Symptoms:**
- Arrow keys don't navigate
- Tab doesn't focus carousel

**Solutions:**
1. Verify keyboard navigation is enabled:
```php
$carousel = Carousel::image('test', $images, [
    'keyboardNav' => true, // Default is true
]);
```

2. Check tabindex:
```html
<!-- Carousel should have tabindex="0" -->
<div id="carousel-test" tabindex="0">
```

3. Verify focus styles:
```css
#carousel-test:focus-visible {
    outline: 3px solid #333;
}
```

## Integration Issues

### Issue: Twig/Blade integration not working

**Symptoms:**
- Extension not found
- Template error

**Solutions:**
1. Install required dependencies:
```bash
# For Twig
composer require twig/twig

# For Blade
composer require illuminate/support
```

2. Register extension:
```php
// Twig
$twig->addExtension(new \JulienLinard\Carousel\Twig\CarouselExtension());

// Blade
// Already registered if using Laravel
```

3. Check template syntax:
```twig
{# Twig #}
{{ carousel('my-carousel', images) }}
```

```blade
{{-- Blade --}}
@carousel('my-carousel', $images)
```

### Issue: Multiple carousels conflict

**Symptoms:**
- Only one carousel works
- Styles conflict

**Solutions:**
1. Use unique IDs:
```php
$carousel1 = Carousel::image('carousel-1', $images1);
$carousel2 = Carousel::image('carousel-2', $images2);
```

2. Clear cache between renders:
```php
RenderCacheService::clear();
$carousel1 = Carousel::image('carousel-1', $images1);
echo $carousel1->render();

RenderCacheService::clear();
$carousel2 = Carousel::image('carousel-2', $images2);
echo $carousel2->render();
```

3. Verify CSS scoping:
```css
/* Each carousel has unique ID */
#carousel-carousel-1 { /* Styles */ }
#carousel-carousel-2 { /* Styles */ }
```

## Getting Help

If you're still experiencing issues:

1. **Check Documentation:**
   - [API Reference](API.md)
   - [Examples](EXEMPLES_UTILISATION.md)
   - [Best Practices](BEST_PRACTICES.md)

2. **Enable Debug Mode:**
```php
// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check carousel output
var_dump($carousel->renderHtml());
var_dump($carousel->renderCss());
var_dump($carousel->renderJs());
```

3. **Check Browser Console:**
   - Open Developer Tools (F12)
   - Check Console for errors
   - Check Network tab for failed requests

4. **Report Issues:**
   - GitHub Issues: https://github.com/julien-lin/php-carousel/issues
   - Include: PHP version, error messages, code example


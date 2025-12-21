# Best Practices Guide

This guide covers best practices for using PHP Carousel in production.

## Performance

### 1. Enable Minification in Production

Always enable minification in production to reduce bundle size:

```php
$carousel = Carousel::image('gallery', $images, [
    'minify' => true,
]);
```

### 2. Use Critical CSS

For above-the-fold content, extract critical CSS:

```php
use JulienLinard\Carousel\Performance\PerformanceOptimizer;

$fullCss = $carousel->renderCss();
$criticalCss = PerformanceOptimizer::extractCriticalCss($fullCss);

// Inline critical CSS in <head>
echo '<style>' . $criticalCss . '</style>';

// Load full CSS asynchronously
echo '<link rel="preload" href="carousel.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
```

### 3. Preload Critical Images

Preload the first 3 images for faster display:

```php
$preloadLinks = PerformanceOptimizer::generatePreloadLinks($carousel);
echo $preloadLinks; // In <head>
```

### 4. Use Virtualization for Large Carousels

For carousels with 50+ items, enable virtualization:

```php
$carousel = Carousel::image('large-gallery', $manyImages, [
    'virtualization' => true,
    'virtualizationThreshold' => 50,
    'virtualizationBuffer' => 3,
]);
```

### 5. Optimize Images

- Use responsive images with `srcset`
- Optimize image formats (WebP, AVIF)
- Serve images from CDN
- Compress images before upload

## Accessibility

### 1. Provide Descriptive Labels

Always provide meaningful labels:

```php
$carousel = Carousel::image('product-gallery', $images, [
    'ariaLabel' => 'Product images gallery',
]);
```

### 2. Ensure Keyboard Accessibility

All carousels are keyboard accessible by default. Ensure:
- No custom CSS removes focus indicators
- Tab order is logical
- All interactive elements are reachable

### 3. Test with Screen Readers

Test carousels with:
- NVDA (Windows)
- JAWS (Windows)
- VoiceOver (macOS/iOS)
- TalkBack (Android)

### 4. Verify Color Contrast

Use the `AccessibilityEnhancer` to verify contrast:

```php
use JulienLinard\Carousel\Accessibility\AccessibilityEnhancer;

$meetsAAA = AccessibilityEnhancer::meetsWCAGAAA('#000000', '#ffffff');
```

## Security

### 1. Sanitize User Input

The library automatically sanitizes:
- Carousel IDs
- URLs
- Attributes

But always validate user input before passing to the carousel:

```php
// Validate image URLs
$images = array_filter($userImages, function($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
});

$carousel = Carousel::image('gallery', $images);
```

### 2. Use HTTPS for Images

Always use HTTPS for external images:

```php
$images = array_map(function($url) {
    return str_replace('http://', 'https://', $url);
}, $images);
```

### 3. Validate Item Data

Validate item data before adding:

```php
foreach ($items as $item) {
    if (!isset($item['id']) || !isset($item['image'])) {
        continue; // Skip invalid items
    }
    $carousel->addItem($item);
}
```

## Code Organization

### 1. Use Factory Methods

Prefer factory methods for cleaner code:

```php
// Good
$carousel = Carousel::image('gallery', $images);

// Less good
$carousel = new Carousel('gallery', Carousel::TYPE_IMAGE);
foreach ($images as $image) {
    $carousel->addItem(['image' => $image]);
}
```

### 2. Separate Rendering Logic

Separate carousel creation from rendering:

```php
// In controller/service
$carousel = Carousel::image('gallery', $images, $options);

// In view/template
echo $carousel->render();
```

### 3. Cache Carousel Instances

Cache carousel instances if data doesn't change frequently:

```php
$cacheKey = 'carousel-gallery-' . md5(serialize($images));
$carousel = $cache->get($cacheKey, function() use ($images) {
    return Carousel::image('gallery', $images);
});
```

## SEO

### 1. Use SSR for Initial Render

Use `renderStatic()` for SEO-friendly HTML:

```php
$staticHtml = $carousel->renderStatic();
// Cache this HTML for CDN
echo $staticHtml;

// Add JavaScript later for interactivity
$fullHtml = $carousel->hydrate($staticHtml);
```

### 2. Provide Alt Text

Always provide alt text for images:

```php
$carousel = Carousel::image('gallery', [
    ['image' => 'photo1.jpg', 'alt' => 'Product photo 1'],
    ['image' => 'photo2.jpg', 'alt' => 'Product photo 2'],
]);
```

### 3. Use Semantic HTML

The library uses semantic HTML by default. Don't override with non-semantic elements.

## Error Handling

### 1. Handle Missing Images

The library provides placeholders for broken images. You can also validate:

```php
$validImages = [];
foreach ($images as $image) {
    if (file_exists($image) || filter_var($image, FILTER_VALIDATE_URL)) {
        $validImages[] = $image;
    }
}

if (empty($validImages)) {
    // Show fallback message
    echo '<p>No images available</p>';
} else {
    $carousel = Carousel::image('gallery', $validImages);
    echo $carousel->render();
}
```

### 2. Validate Options

Validate options before creating carousel:

```php
$options = [
    'autoplay' => (bool)($request->get('autoplay') ?? false),
    'autoplayInterval' => max(1000, min(10000, (int)$request->get('interval', 5000))),
];

$carousel = Carousel::image('gallery', $images, $options);
```

## Testing

### 1. Test All Carousel Types

Test all carousel types you use:

```php
// Unit tests
public function testImageCarousel(): void {
    $carousel = Carousel::image('test', ['image1.jpg']);
    $this->assertNotEmpty($carousel->render());
}
```

### 2. Test Responsive Behavior

Test carousel on different screen sizes:

```php
// E2E tests
public function testCarouselResponsive(): void {
    // Test mobile, tablet, desktop
}
```

### 3. Test Accessibility

Run accessibility tests:

```bash
./vendor/bin/phpunit tests/AccessibilityTest.php
./vendor/bin/phpunit tests/AccessibilityAAATest.php
```

## Maintenance

### 1. Keep Library Updated

Regularly update the library:

```bash
composer update julienlinard/php-carousel
```

### 2. Monitor Bundle Sizes

Monitor bundle sizes:

```php
$sizes = PerformanceOptimizer::calculateBundleSize(
    $carousel->renderHtml(),
    $carousel->renderCss(),
    $carousel->renderJs()
);

if ($sizes['total'] > 200000) {
    // Consider optimization
}
```

### 3. Review Analytics

If using analytics, regularly review reports:

```php
$report = $analytics->getReport('gallery');
// Analyze: total_impressions, ctr, most_viewed_slide
```

## Common Mistakes

### ❌ Don't: Render Multiple Times

```php
// Bad
echo $carousel->render();
echo $carousel->render(); // Duplicate output
```

### ✅ Do: Render Once

```php
// Good
echo $carousel->render();
```

### ❌ Don't: Override Critical CSS

```php
// Bad - removes focus indicators
.carousel-arrow:focus {
    outline: none;
}
```

### ✅ Do: Enhance, Don't Remove

```php
// Good - enhances focus indicators
.carousel-arrow:focus-visible {
    outline: 3px solid #333;
}
```

### ❌ Don't: Use Inline Styles

```php
// Bad
$carousel->setOptions(['style' => 'custom: value']);
```

### ✅ Do: Use CSS Classes

```php
// Good
$carousel->setOptions(['class' => 'custom-carousel']);
```

## Resources

- [API Documentation](API.md)
- [Accessibility Guide](ACCESSIBILITY.md)
- [Performance Guide](PERFORMANCE.md)
- [Examples](EXEMPLES_UTILISATION.md)


# Video Tutorials (Scripts)

Scripts for creating video tutorials about PHP Carousel.

## Tutorial 1: Getting Started (5 minutes)

### Introduction (30 seconds)

"Welcome to PHP Carousel! In this tutorial, we'll create your first carousel in just a few minutes. PHP Carousel is a modern, performant carousel library with zero external dependencies."

### Installation (1 minute)

"First, let's install PHP Carousel using Composer."

**Script:**
```bash
composer require julienlinard/php-carousel
```

"PHP Carousel requires PHP 8.2 or higher. The core library has zero dependencies, but if you want Twig or Blade integration, you'll need to install those separately."

### Basic Example (2 minutes)

"Let's create a simple image carousel."

**Code:**
```php
<?php
require_once 'vendor/autoload.php';
use JulienLinard\Carousel\Carousel;

$carousel = Carousel::image('my-carousel', [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
    'https://example.com/image3.jpg',
]);

echo $carousel->render();
?>
```

"Here's what's happening:
1. We import the Carousel class
2. We use the `image()` factory method to create an image carousel
3. We pass an array of image URLs
4. We call `render()` to output HTML, CSS, and JavaScript

That's it! The carousel is fully functional with navigation arrows, dots, and keyboard support."

### Customization (1.5 minutes)

"Let's customize the carousel with options."

**Code:**
```php
$carousel = Carousel::image('my-carousel', $images, [
    'autoplay' => true,
    'autoplayInterval' => 3000,
    'showArrows' => true,
    'showDots' => true,
    'transition' => 'fade',
]);
```

"Options include:
- `autoplay`: Enable automatic slide progression
- `autoplayInterval`: Time between slides in milliseconds
- `showArrows`: Display navigation arrows
- `showDots`: Display dot indicators
- `transition`: 'slide' or 'fade' animation"

### Conclusion (30 seconds)

"That's it! You now have a fully functional carousel. Check out the documentation for more advanced features like themes, analytics, and A/B testing. Thanks for watching!"

---

## Tutorial 2: Advanced Features (10 minutes)

### Introduction (30 seconds)

"In this tutorial, we'll explore advanced features of PHP Carousel including themes, analytics, A/B testing, and server-side rendering."

### Themes (2 minutes)

"PHP Carousel supports dark and light themes with automatic system preference detection."

**Code:**
```php
$carousel = Carousel::image('themed-carousel', $images, [
    'theme' => 'dark',
    'themeColors' => [
        'dark' => [
            'background' => '#1a1a1a',
            'text' => '#ffffff',
            'arrow' => '#ffffff',
            'dotActive' => '#4a9eff',
        ],
    ],
]);
```

"Themes use CSS variables, making customization easy. You can also use 'auto' mode to detect system preferences."

### Analytics (2 minutes)

"Track carousel interactions with built-in analytics."

**Code:**
```php
use JulienLinard\Carousel\Analytics\FileAnalytics;

$analytics = new FileAnalytics('/var/log/carousel');
$carousel = Carousel::image('analytics-carousel', $images, [
    'analytics' => true,
    'analyticsProvider' => $analytics,
]);

// Later, get report
$report = $analytics->getReport('analytics-carousel');
// Returns: total_impressions, total_clicks, ctr, most_viewed_slide
```

"Analytics automatically tracks impressions, clicks, and interactions. You can generate detailed reports."

### A/B Testing (2 minutes)

"Test different carousel variants with built-in A/B testing."

**Code:**
```php
use JulienLinard\Carousel\ABTesting\ABTest;

$carouselA = Carousel::image('variant-a', $imagesA, [
    'autoplayInterval' => 3000,
]);

$carouselB = Carousel::image('variant-b', $imagesB, [
    'autoplayInterval' => 5000,
]);

$test = new ABTest('hero-test', [
    'variant_a' => ['carousel' => $carouselA, 'weight' => 50],
    'variant_b' => ['carousel' => $carouselB, 'weight' => 50],
]);

$selectedCarousel = $test->getCarousel();
echo $selectedCarousel->render();
```

"A/B testing supports cookie-based persistence for consistent user experience."

### Server-Side Rendering (2 minutes)

"Generate static HTML for SEO and CDN caching."

**Code:**
```php
$carousel = Carousel::image('ssr-carousel', $images);

// Generate static HTML (no JavaScript)
$staticHtml = $carousel->renderStatic();
// Perfect for CDN caching

// Later, add JavaScript for interactivity
$fullHtml = $carousel->hydrate($staticHtml);
```

"SSR provides perfect SEO, fast initial load, and CDN cacheability."

### Performance Optimization (1.5 minutes)

"Optimize performance with minification and critical CSS."

**Code:**
```php
use JulienLinard\Carousel\Performance\PerformanceOptimizer;

$carousel = Carousel::image('optimized', $images, [
    'minify' => true,
]);

// Extract critical CSS
$criticalCss = PerformanceOptimizer::extractCriticalCss($fullCss);
// Inline in <head>

// Preload images
$preloadLinks = PerformanceOptimizer::generatePreloadLinks($carousel);
```

"Performance tools help reduce bundle size and improve load times."

### Conclusion (30 seconds)

"These advanced features make PHP Carousel production-ready. Check the documentation for more details. Thanks for watching!"

---

## Tutorial 3: Integration with Frameworks (8 minutes)

### Introduction (30 seconds)

"In this tutorial, we'll integrate PHP Carousel with popular PHP frameworks: Symfony, Laravel, and Twig."

### Symfony Integration (2 minutes)

"In Symfony, create a service or use directly in controllers."

**Code:**
```php
// src/Controller/CarouselController.php
namespace App\Controller;

use JulienLinard\Carousel\Carousel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarouselController extends AbstractController
{
    public function index()
    {
        $carousel = Carousel::image('gallery', $images);
        return $this->render('carousel/index.html.twig', [
            'carousel' => $carousel,
        ]);
    }
}
```

**Twig Template:**
```twig
{{ carousel('gallery', images) }}
```

### Laravel Integration (2 minutes)

"In Laravel, use in controllers or Blade templates."

**Code:**
```php
// app/Http/Controllers/CarouselController.php
use JulienLinard\Carousel\Carousel;

public function index()
{
    $carousel = Carousel::image('gallery', $images);
    return view('carousel.index', ['carousel' => $carousel]);
}
```

**Blade Template:**
```blade
@carousel('gallery', $images)
```

### Twig Extension (2 minutes)

"Use the Twig extension for cleaner templates."

**Code:**
```php
// Register extension
$twig->addExtension(new \JulienLinard\Carousel\Twig\CarouselExtension());
```

**Twig Template:**
```twig
{{ carousel('gallery', images, {
    'autoplay': true,
    'showArrows': true
}) }}
```

### Custom Service (1.5 minutes)

"Create a custom service for reusability."

**Code:**
```php
// src/Service/CarouselService.php
namespace App\Service;

use JulienLinard\Carousel\Carousel;

class CarouselService
{
    public function createImageCarousel(string $id, array $images, array $options = []): Carousel
    {
        return Carousel::image($id, $images, array_merge([
            'autoplay' => true,
            'autoplayInterval' => 5000,
        ], $options));
    }
}
```

### Conclusion (30 seconds)

"PHP Carousel integrates seamlessly with any PHP framework. Check the integration guides for more details. Thanks for watching!"

---

## Tutorial 4: Accessibility (6 minutes)

### Introduction (30 seconds)

"PHP Carousel is WCAG 2.1 AAA compliant. In this tutorial, we'll explore accessibility features."

### ARIA Attributes (2 minutes)

"All carousels include proper ARIA attributes."

**HTML Output:**
```html
<div role="region" aria-label="Carousel">
    <div role="group" aria-roledescription="slide" aria-label="Slide 1 of 5">
        <!-- Slide content -->
    </div>
</div>
```

"ARIA attributes provide context for screen readers."

### Keyboard Navigation (1.5 minutes)

"Full keyboard support is built-in."

**Keys:**
- Arrow Left/Right: Navigate slides
- Tab: Focus carousel
- Enter/Space: Activate focused element
- Escape: Stop autoplay

"All interactive elements are keyboard accessible."

### Screen Reader Support (1.5 minutes)

"Screen readers announce slide changes automatically."

**JavaScript:**
```javascript
// Announcement element updates automatically
<div class="sr-only carousel-announcement" aria-live="polite">
    Slide 2 of 5
</div>
```

"Live regions provide real-time updates to screen readers."

### Focus Indicators (1 minute)

"High-contrast focus indicators meet WCAG AAA standards."

**CSS:**
```css
.carousel-arrow:focus-visible {
    outline: 3px solid #333;
    outline-offset: 2px;
}
```

"Focus indicators are highly visible and meet contrast requirements."

### Conclusion (30 seconds)

"PHP Carousel is fully accessible out of the box. Test with screen readers to verify. Thanks for watching!"

---

## Tutorial 5: Performance Optimization (7 minutes)

### Introduction (30 seconds)

"In this tutorial, we'll optimize PHP Carousel for maximum performance."

### Minification (1.5 minutes)

"Enable minification in production."

**Code:**
```php
$carousel = Carousel::image('optimized', $images, [
    'minify' => true,
]);
```

"Minification reduces CSS and JavaScript size by 30-50%."

### Critical CSS (2 minutes)

"Extract critical CSS for above-the-fold content."

**Code:**
```php
use JulienLinard\Carousel\Performance\PerformanceOptimizer;

$fullCss = $carousel->renderCss();
$criticalCss = PerformanceOptimizer::extractCriticalCss($fullCss);

// Inline in <head>
echo '<style>' . $criticalCss . '</style>';

// Load full CSS asynchronously
echo '<link rel="preload" href="carousel.css" as="style">';
```

"Critical CSS improves First Contentful Paint."

### Image Preloading (1.5 minutes)

"Preload critical images."

**Code:**
```php
$preloadLinks = PerformanceOptimizer::generatePreloadLinks($carousel);
echo $preloadLinks; // In <head>
```

"Preloading reduces image load time."

### Virtualization (1.5 minutes)

"Enable virtualization for large carousels."

**Code:**
```php
$carousel = Carousel::image('large-gallery', $manyImages, [
    'virtualization' => true,
    'virtualizationThreshold' => 50,
]);
```

"Virtualization improves performance for carousels with 50+ items."

### Bundle Size Analysis (30 seconds)

"Monitor bundle sizes."

**Code:**
```php
$sizes = PerformanceOptimizer::calculateBundleSize(
    $carousel->renderHtml(),
    $carousel->renderCss(),
    $carousel->renderJs()
);
```

"Keep total bundle size under 200KB."

### Conclusion (30 seconds)

"These optimizations ensure fast load times and smooth performance. Check the performance guide for more tips. Thanks for watching!"

---

## Production Checklist

Before recording, ensure:

- [ ] Code examples are tested and working
- [ ] Screenshots/demos are prepared
- [ ] Audio is clear and professional
- [ ] Video quality is HD (1080p minimum)
- [ ] Subtitles/captions are included
- [ ] Links to documentation are provided
- [ ] Code is syntax-highlighted
- [ ] Examples are realistic and practical

## Video Hosting

Recommended platforms:
- YouTube (public tutorials)
- Vimeo (professional hosting)
- Self-hosted (for private/internal use)

## Promotion

After creating videos:
1. Add to README.md
2. Share on social media
3. Include in documentation
4. Submit to PHP community sites


# CodePen Examples

Interactive examples for PHP Carousel (ready to paste into CodePen or similar platforms).

## Example 1: Basic Image Carousel

**HTML:**
```html
<div id="carousel-container"></div>
```

**PHP (Server-side):**
```php
<?php
require_once 'vendor/autoload.php';
use JulienLinard\Carousel\Carousel;

$carousel = Carousel::image('basic-carousel', [
    'https://via.placeholder.com/800x400/0066CC/FFFFFF?text=Slide+1',
    'https://via.placeholder.com/800x400/CC0066/FFFFFF?text=Slide+2',
    'https://via.placeholder.com/800x400/66CC00/FFFFFF?text=Slide+3',
], [
    'showArrows' => true,
    'showDots' => true,
    'autoplay' => true,
    'autoplayInterval' => 3000,
]);

echo $carousel->render();
?>
```

**CodePen Link Template:**
```
https://codepen.io/your-username/pen/XXXXXX
```

## Example 2: Card Carousel with Custom Styling

**HTML:**
```html
<div id="carousel-container"></div>
```

**PHP:**
```php
<?php
$carousel = Carousel::card('product-carousel', [
    [
        'id' => '1',
        'title' => 'Product 1',
        'content' => 'Description of product 1',
        'image' => 'https://via.placeholder.com/300x200',
        'link' => '/product/1',
    ],
    [
        'id' => '2',
        'title' => 'Product 2',
        'content' => 'Description of product 2',
        'image' => 'https://via.placeholder.com/300x200',
        'link' => '/product/2',
    ],
], [
    'itemsPerSlide' => 3,
    'itemsPerSlideMobile' => 1,
    'itemsPerSlideTablet' => 2,
    'itemsPerSlideDesktop' => 4,
]);

echo $carousel->render();
?>
```

**Custom CSS:**
```css
#carousel-product-carousel .carousel-card {
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#carousel-product-carousel .carousel-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}
```

## Example 3: Gallery with Thumbnails

**PHP:**
```php
<?php
$carousel = Carousel::gallery('photo-gallery', [
    'https://via.placeholder.com/1200x800/0066CC/FFFFFF?text=Photo+1',
    'https://via.placeholder.com/1200x800/CC0066/FFFFFF?text=Photo+2',
    'https://via.placeholder.com/1200x800/66CC00/FFFFFF?text=Photo+3',
], [
    'showThumbnails' => true,
    'transition' => 'fade',
]);

echo $carousel->render();
?>
```

## Example 4: Dark Theme Carousel

**PHP:**
```php
<?php
$carousel = Carousel::image('dark-carousel', $images, [
    'theme' => 'dark',
    'themeColors' => [
        'light' => [
            'background' => '#ffffff',
            'text' => '#1a1a1a',
        ],
        'dark' => [
            'background' => '#1a1a1a',
            'text' => '#ffffff',
            'arrow' => '#ffffff',
            'dotActive' => '#4a9eff',
        ],
    ],
]);

echo $carousel->render();
?>
```

## Example 5: Programmatic Control

**HTML:**
```html
<div id="carousel-container"></div>
<button onclick="prevSlide()">Previous</button>
<button onclick="nextSlide()">Next</button>
<button onclick="goToSlide(2)">Go to Slide 3</button>
```

**PHP:**
```php
<?php
$carousel = Carousel::image('controlled-carousel', $images);
echo $carousel->render();
?>
```

**JavaScript:**
```javascript
const carousel = window.CarouselAPI.get('controlled-carousel');

function prevSlide() {
    carousel.prev();
}

function nextSlide() {
    carousel.next();
}

function goToSlide(index) {
    carousel.goTo(index);
}

// Listen to events
carousel.on('slideChange', (data) => {
    console.log('Current slide:', data.index);
});
```

## Example 6: Analytics Integration

**PHP:**
```php
<?php
use JulienLinard\Carousel\Analytics\FileAnalytics;

$analytics = new FileAnalytics('/tmp/carousel-analytics');
$carousel = Carousel::image('analytics-carousel', $images, [
    'analytics' => true,
    'analyticsProvider' => $analytics,
]);

echo $carousel->render();

// Later, get report
$report = $analytics->getReport('analytics-carousel');
print_r($report);
?>
```

## Example 7: A/B Testing

**PHP:**
```php
<?php
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
?>
```

## Example 8: SSR (Server-Side Rendering)

**PHP:**
```php
<?php
$carousel = Carousel::image('ssr-carousel', $images);

// Generate static HTML (for CDN caching)
$staticHtml = $carousel->renderStatic();
file_put_contents('carousel-static.html', $staticHtml);

// Later, hydrate with JavaScript
$fullHtml = $carousel->hydrate($staticHtml);
echo $fullHtml;
?>
```

## Example 9: Custom Animations

**PHP:**
```php
<?php
$carousel = Carousel::image('animated-carousel', $images, [
    'animations' => [
        'fadeIn' => [
            'keyframes' => [
                'name' => 'fade-in',
                'steps' => [
                    '0%' => ['opacity' => '0', 'transform' => 'translateY(20px)'],
                    '100%' => ['opacity' => '1', 'transform' => 'translateY(0)'],
                ],
            ],
            'duration' => '0.6s',
            'timingFunction' => 'ease-out',
        ],
    ],
]);

echo $carousel->render();
?>
```

## Example 10: Multiple Carousels

**PHP:**
```php
<?php
$heroCarousel = Carousel::heroBanner('hero', $banners);
$productCarousel = Carousel::productShowcase('products', $products);
$testimonialCarousel = Carousel::testimonialSlider('testimonials', $testimonials);

echo $heroCarousel->render();
echo $productCarousel->render();
echo $testimonialCarousel->render();
?>
```

## Creating CodePen Examples

1. **Create HTML section**: Paste the rendered HTML output
2. **Create CSS section**: Add any custom CSS
3. **Create JS section**: Add any custom JavaScript
4. **Share the link**: Share the CodePen URL

## Note

Since CodePen doesn't support PHP execution, you'll need to:
1. Run PHP locally to generate HTML/CSS/JS
2. Copy the output to CodePen
3. Or use a PHP CodePen alternative (like PHPFiddle)

For live examples, consider creating a demo page on your website.


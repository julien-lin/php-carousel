# PHP Carousel

[🇫🇷 Read in French](README.fr.md) | [🇬🇧 Read in English](README.md)

## 💝 Support the project

If this bundle is useful to you, consider [becoming a sponsor](https://github.com/sponsors/julien-lin) to support the development and maintenance of this open source project.

---

A modern and performant carousel library for PHP with beautiful designs. Pure CSS/JS native implementation with **zero external dependencies**.

## 🚀 Installation

```bash
composer require julienlinard/php-carousel
```

**Requirements**: PHP 8.2 or higher

### Optional Dependencies

The core library has **zero external dependencies**. However, if you want to use Twig or Blade integrations, you need to install the corresponding packages:

**For Twig integration:**
```bash
composer require twig/twig
```

**For Blade integration (Laravel):**
```bash
composer require illuminate/support
```

> **Note**: These dependencies are optional. The core carousel functionality works without them. They are only needed if you use the Twig or Blade extensions.

## ⚡ Quick Start

### Simple Image Carousel

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Carousel\Carousel;

// Create an image carousel
$carousel = Carousel::image('my-carousel', [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
    'https://example.com/image3.jpg',
]);

// Render the carousel
echo $carousel->render();
```

### Card Carousel

```php
use JulienLinard\Carousel\Carousel;

$carousel = Carousel::card('products', [
    [
        'id' => '1',
        'title' => 'Product 1',
        'content' => 'Description of product 1',
        'image' => 'https://example.com/product1.jpg',
        'link' => '/product/1',
    ],
    [
        'id' => '2',
        'title' => 'Product 2',
        'content' => 'Description of product 2',
        'image' => 'https://example.com/product2.jpg',
        'link' => '/product/2',
    ],
], [
    'itemsPerSlide' => 3,
    'itemsPerSlideMobile' => 1,
]);

echo $carousel->render();
```

### Testimonial Carousel

```php
use JulienLinard\Carousel\Carousel;

$carousel = Carousel::testimonial('testimonials', [
    [
        'id' => '1',
        'title' => 'John Doe',
        'content' => 'This product changed my life! Highly recommended.',
        'image' => 'https://example.com/avatar1.jpg',
    ],
    [
        'id' => '2',
        'title' => 'Jane Smith',
        'content' => 'Amazing quality and excellent customer service.',
        'image' => 'https://example.com/avatar2.jpg',
    ],
], [
    'transition' => 'fade',
    'autoplayInterval' => 6000,
]);

echo $carousel->render();
```

## 🎨 Visual Demos

See the carousels in action! Each type is fully customizable and responsive with smooth animations.

### Image Carousel
Perfect for hero banners and image galleries with smooth slide transitions.

![Image Carousel Demo](docs/images/image-carousel.gif)

### Card Carousel
Ideal for product listings, blog posts, or feature showcases. Displays multiple items per slide.

![Card Carousel Demo](docs/images/card-carousel.gif)

### Testimonial Carousel
Beautiful fade transitions for customer reviews and testimonials.

![Testimonial Carousel Demo](docs/images/testimonial-carousel.gif)

### Gallery Carousel
Advanced gallery with thumbnail navigation for easy browsing.

![Gallery Carousel Demo](docs/images/gallery-carousel.gif)

## 📋 Features

- ✅ **Zero Dependencies** - Pure CSS/JS native implementation
- ✅ **Multiple Types** - Image, Card, Testimonial, Gallery, Infinite carousels
- ✅ **Static Factory Methods** - `infiniteCarousel()`, `heroBanner()`, `productShowcase()`, `testimonialSlider()`
- ✅ **Twig & Blade Integration** - Ready-to-use extensions for popular templating engines
- ✅ **Fully Responsive** - Mobile, tablet, and desktop optimized
- ✅ **Touch Swipe** - Native touch gestures support
- ✅ **Keyboard Navigation** - Accessible keyboard controls
- ✅ **Autoplay** - Configurable autoplay with pause on hover
- ✅ **Smooth Animations** - CSS transitions and transforms
- ✅ **Lazy Loading** - Built-in image lazy loading with Intersection Observer
- ✅ **Customizable** - Extensive configuration options
- ✅ **WCAG 2.1 AA Compliant** - Full accessibility support (ARIA, screen readers, prefers-reduced-motion)
- ✅ **Security** - XSS prevention, URL validation, input sanitization
- ✅ **Performance** - Modular renderer architecture, optimized JavaScript, CSS/JS minification, virtualization for large carousels
- ✅ **Themes** - Dark/Light mode support with automatic system preference detection
- ✅ **Virtualization** - Automatic performance optimization for carousels with 50+ items
- ✅ **Server-Side Rendering (SSR)** - Static HTML generation for SEO and CDN caching
- ✅ **Error Handling** - Image error placeholders, loading indicators

## 📖 Documentation

### Carousel Types

#### Image Carousel

Perfect for image galleries and hero banners.

![Image Carousel Example](docs/images/image-carousel.gif)

```php
$carousel = Carousel::image('gallery', [
    'image1.jpg',
    'image2.jpg',
    'image3.jpg',
], [
    'height' => '500px',
    'showDots' => true,
    'showArrows' => true,
]);
```

#### Card Carousel

Ideal for product listings, blog posts, or feature cards.

![Card Carousel Example](docs/images/card-carousel.gif)

```php
$carousel = Carousel::card('products', $products, [
    'itemsPerSlide' => 3,
    'itemsPerSlideDesktop' => 3,
    'itemsPerSlideTablet' => 2,
    'itemsPerSlideMobile' => 1,
    'gap' => 24,
]);
```

#### Testimonial Carousel

Perfect for customer reviews and testimonials.

![Testimonial Carousel Example](docs/images/testimonial-carousel.gif)

```php
$carousel = Carousel::testimonial('reviews', $testimonials, [
    'transition' => 'fade',
    'autoplayInterval' => 7000,
]);
```

#### Gallery Carousel

Advanced gallery with thumbnails navigation.

![Gallery Carousel Example](docs/images/gallery-carousel.gif)

```php
$carousel = Carousel::gallery('photo-gallery', $images, [
    'showThumbnails' => true,
    'itemsPerSlide' => 1,
]);
```

### Configuration Options

```php
$carousel = new Carousel('my-carousel', Carousel::TYPE_IMAGE, [
    // Autoplay
    'autoplay' => true,                    // Enable/disable autoplay
    'autoplayInterval' => 5000,             // Autoplay interval in milliseconds
    
    // Navigation
    'showArrows' => true,                  // Show navigation arrows
    'showDots' => true,                    // Show dot indicators
    'showThumbnails' => false,            // Show thumbnails (gallery only)
    
    // Layout
    'itemsPerSlide' => 1,                  // Number of items per slide
    'itemsPerSlideDesktop' => 1,           // Desktop items per slide
    'itemsPerSlideTablet' => 1,            // Tablet items per slide
    'itemsPerSlideMobile' => 1,            // Mobile items per slide
    'gap' => 16,                           // Gap between items (px)
    
    // Animation
    'transition' => 'slide',               // 'slide', 'fade', 'cube'
    'transitionDuration' => 500,           // Transition duration (ms)
    
    // Behavior
    'loop' => true,                        // Loop through slides
    'responsive' => true,                  // Enable responsive behavior
    'lazyLoad' => true,                    // Enable lazy loading
    'keyboardNavigation' => true,          // Enable keyboard navigation
    'touchSwipe' => true,                  // Enable touch swipe
    
    // Styling
    'height' => 'auto',                    // Carousel height
    'width' => '100%',                     // Carousel width
]);
```

### Advanced Usage

#### Custom Items

```php
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;

$carousel = new Carousel('custom', Carousel::TYPE_CARD);

$carousel->addItem(new CarouselItem(
    id: 'item1',
    title: 'Custom Item',
    content: 'This is a custom carousel item',
    image: 'https://example.com/image.jpg',
    link: '/item/1',
    attributes: ['class' => 'custom-class']
));

$carousel->addItem([
    'id' => 'item2',
    'title' => 'Another Item',
    'content' => 'Added from array',
    'image' => 'https://example.com/image2.jpg',
]);

echo $carousel->render();
```

#### Separate HTML, CSS, and JS

```php
// Render only HTML
echo $carousel->renderHtml();

// Render only CSS (in <head>)
echo $carousel->renderCss();

// Render only JavaScript (before </body>)
echo $carousel->renderJs();
```

#### Dark/Light Theme Support

```php
// Auto theme (respects system preference)
$carousel = Carousel::image('gallery', $images, [
    'theme' => 'auto', // Automatically switches based on prefers-color-scheme
]);

// Light theme
$carousel = Carousel::card('products', $products, [
    'theme' => 'light',
]);

// Dark theme
$carousel = Carousel::image('hero', $banners, [
    'theme' => 'dark',
]);

// Custom theme colors
$carousel = Carousel::card('custom', $items, [
    'theme' => 'light',
    'themeColors' => [
        'light' => [
            'background' => '#ffffff',
            'text' => '#000000',
            'cardBackground' => '#f5f5f5',
        ],
        'dark' => [
            'background' => '#1a1a1a',
            'text' => '#ffffff',
            'cardBackground' => '#2a2a2a',
        ],
    ],
]);
```

#### Virtualization for Large Carousels

```php
// Enable virtualization for performance with many items
$carousel = Carousel::image('large-gallery', $manyImages, [
    'virtualization' => true,
    'virtualizationBuffer' => 5, // Show 5 slides on each side
]);

// Auto-enable when items exceed threshold (default: 50)
$carousel = Carousel::gallery('photo-gallery', $manyPhotos, [
    'virtualizationThreshold' => 30, // Enable at 30 items instead of 50
]);
```

#### Custom Transitions and Animations

```php
// Custom transition
$carousel = Carousel::image('custom', $images, [
    'customTransition' => [
        'duration' => 600,
        'timingFunction' => 'cubic-bezier(0.4, 0, 0.2, 1)',
        'properties' => ['transform', 'opacity'],
    ],
]);

// Custom animations (simple)
$carousel = Carousel::card('animated', $cards, [
    'animations' => [
        'slideIn' => 'slideInFromRight 0.5s ease-out',
        'slideOut' => 'slideOutToLeft 0.5s ease-in',
    ],
]);

// Custom animations (with keyframes)
$carousel = Carousel::image('keyframes', $images, [
    'animations' => [
        'fadeIn' => [
            'keyframes' => [
                'name' => 'carousel-fade-in',
                'steps' => [
                    '0%' => ['opacity' => '0'],
                    '100%' => ['opacity' => '1'],
                ],
            ],
            'duration' => '0.5s',
            'timingFunction' => 'ease-out',
        ],
    ],
]);
```

#### Export/Import Configuration

```php
// Export carousel configuration
$carousel = Carousel::image('gallery', $images, [
    'autoplay' => true,
    'theme' => 'dark',
]);
$config = $carousel->exportConfig();

// Save to file
file_put_contents('carousel-config.json', json_encode($config, JSON_PRETTY_PRINT));

// Load and restore from file
$savedConfig = json_decode(file_get_contents('carousel-config.json'), true);
$restoredCarousel = Carousel::fromConfig($savedConfig);
```

#### Server-Side Rendering (SSR)

```php
// Generate static HTML (perfect for SSR, caching, CDN)
$carousel = Carousel::image('gallery', $images);
$staticHtml = $carousel->renderStatic();
// This HTML can be cached, served via CDN, indexed by search engines

// Add JavaScript for interactivity (progressive enhancement)
$fullHtml = $carousel->hydrate($staticHtml);
// Or load JavaScript asynchronously on the client side
```

**SSR Benefits:**
- ✅ Perfect SEO (content in HTML)
- ✅ Fast initial load (no JavaScript required)
- ✅ CDN cacheable
- ✅ Progressive enhancement (add JS when needed)

#### Multiple Carousels on Same Page

```php
$carousel1 = Carousel::image('carousel-1', $images1);
$carousel2 = Carousel::card('carousel-2', $cards);

// Each carousel has unique IDs and styles
echo $carousel1->render();
echo $carousel2->render();
```

## 🎨 Styling

The carousel uses pure CSS with no external dependencies. All styles are scoped to the carousel container to avoid conflicts.

### Custom Styling

You can override styles using CSS:

```css
#carousel-my-carousel .carousel-arrow {
    background: #your-color;
}

#carousel-my-carousel .carousel-dot.active {
    background: #your-color;
}
```

## 📚 API Reference

### Carousel Class

#### Static Factory Methods

- `Carousel::image(string $id, array $images, array $options = []): self` - Image carousel
- `Carousel::card(string $id, array $cards, array $options = []): self` - Card carousel
- `Carousel::testimonial(string $id, array $testimonials, array $options = []): self` - Testimonial carousel
- `Carousel::gallery(string $id, array $images, array $options = []): self` - Gallery carousel
- `Carousel::infiniteCarousel(string $id, array $images, array $options = []): self` - Infinite scrolling carousel
- `Carousel::heroBanner(string $id, array $banners, array $options = []): self` - Hero banner carousel
- `Carousel::productShowcase(string $id, array $products, array $options = []): self` - Product showcase carousel
- `Carousel::testimonialSlider(string $id, array $testimonials, array $options = []): self` - Testimonial slider

#### Instance Methods

- `addItem(CarouselItem|array $item): self` - Add a single item
- `addItems(array $items): self` - Add multiple items
- `setOptions(array $options): self` - Set carousel options
- `getOption(string $key, mixed $default = null): mixed` - Get an option value
- `render(): string` - Render complete carousel (HTML + CSS + JS)
- `renderHtml(): string` - Render only HTML
- `renderCss(): string` - Render only CSS
- `renderJs(): string` - Render only JavaScript
- `renderStatic(): string` - Render static HTML with CSS (SSR, no JS)
- `hydrate(string $staticHtml): string` - Add JavaScript to static HTML
- `getId(): string` - Get carousel ID
- `getType(): string` - Get carousel type
- `getItems(): array` - Get all items
- `getOptions(): array` - Get all options
- `exportConfig(): array` - Export configuration to array
- `fromConfig(array $config): self` - Create carousel from configuration (static)

### CarouselItem Class

#### Constructor

```php
new CarouselItem(
    string $id,
    string $title = '',
    string $content = '',
    string $image = '',
    string $link = '',
    array $attributes = []
)
```

#### Static Methods

- `CarouselItem::fromArray(array $data): self` - Create from array

#### Instance Methods

- `toArray(): array` - Convert to array

## 💡 Examples

### Example 1: Product Carousel

```php
<?php

use JulienLinard\Carousel\Carousel;

$products = [
    [
        'id' => '1',
        'title' => 'Premium Headphones',
        'content' => 'High-quality sound with noise cancellation',
        'image' => '/images/headphones.jpg',
        'link' => '/products/headphones',
    ],
    [
        'id' => '2',
        'title' => 'Wireless Mouse',
        'content' => 'Ergonomic design with long battery life',
        'image' => '/images/mouse.jpg',
        'link' => '/products/mouse',
    ],
    // ... more products
];

$carousel = Carousel::card('products', $products, [
    'itemsPerSlide' => 4,
    'itemsPerSlideDesktop' => 4,
    'itemsPerSlideTablet' => 2,
    'itemsPerSlideMobile' => 1,
    'gap' => 20,
    'autoplay' => true,
    'autoplayInterval' => 4000,
]);

echo $carousel->render();
```

### Example 2: Hero Banner Carousel

```php
<?php

use JulienLinard\Carousel\Carousel;

$banners = [
    [
        'id' => 'banner1',
        'title' => 'Welcome to Our Store',
        'content' => 'Discover amazing products',
        'image' => '/images/banner1.jpg',
        'link' => '/shop',
    ],
    [
        'id' => 'banner2',
        'title' => 'Summer Sale',
        'content' => 'Up to 50% off on selected items',
        'image' => '/images/banner2.jpg',
        'link' => '/sale',
    ],
];

$carousel = Carousel::image('hero', $banners, [
    'height' => '600px',
    'autoplay' => true,
    'autoplayInterval' => 5000,
    'transition' => 'fade',
]);

echo $carousel->render();
```

### Example 3: Customer Testimonials

```php
<?php

use JulienLinard\Carousel\Carousel;

$testimonials = [
    [
        'id' => '1',
        'title' => 'Sarah Johnson',
        'content' => 'The best service I\'ve ever experienced. Highly recommend!',
        'image' => '/avatars/sarah.jpg',
    ],
    [
        'id' => '2',
        'title' => 'Michael Chen',
        'content' => 'Outstanding quality and fast delivery. Will order again!',
        'image' => '/avatars/michael.jpg',
    ],
];

$carousel = Carousel::testimonial('testimonials', $testimonials, [
    'transition' => 'fade',
    'autoplayInterval' => 6000,
    'showDots' => true,
]);

echo $carousel->render();
```

## 🔌 Integrations

### Twig Integration

See [INTEGRATION_TWIG.md](docs/INTEGRATION_TWIG.md) for complete documentation.

```twig
{# Simple usage #}
{{ carousel_infinite('products', images)|raw }}

{# With options #}
{{ carousel_hero('banner', banners, {
    'height': '700px',
    'autoplayInterval': 4000
})|raw }}
```

### Blade Integration (Laravel)

See [INTEGRATION_BLADE.md](docs/INTEGRATION_BLADE.md) for complete documentation.

```blade
{{-- Directives --}}
@carousel_infinite('products', $images)
@carousel_hero('banner', $banners, ['height' => '700px'])

{{-- Helpers --}}
{!! carousel_infinite('products', $images)->render() !!}
```

## 🧪 Tests

```bash
composer test
```

**Test Coverage:**
- ✅ 60 tests, 200 assertions
- ✅ Security tests (XSS prevention, URL validation, input sanitization)
- ✅ Accessibility tests (ARIA attributes, screen readers, prefers-reduced-motion)
- ✅ Integration tests (Twig, Blade)
- ✅ Functional tests (all carousel types and methods)

## 📚 Additional Documentation

- **[API Reference](docs/API.md)** - Complete API documentation
- **[Twig Integration](docs/INTEGRATION_TWIG.md)** - Twig extension guide
- **[Blade Integration](docs/INTEGRATION_BLADE.md)** - Laravel Blade guide
- **[Usage Examples](docs/EXEMPLES_UTILISATION.md)** - More examples

## 📝 License

MIT License - See the LICENSE file for more details.

## 🤝 Contributing

Contributions are welcome! Feel free to open an issue or a pull request.

## 📧 Support

For any questions or issues, please open an issue on GitHub.

## 💝 Support the project

If this bundle is useful to you, consider [becoming a sponsor](https://github.com/sponsors/julien-lin) to support the development and maintenance of this open source project.

---

**Developed with ❤️ by Julien Linard**


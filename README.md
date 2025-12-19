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
- ✅ **Multiple Types** - Image, Card, Testimonial, Gallery carousels
- ✅ **Fully Responsive** - Mobile, tablet, and desktop optimized
- ✅ **Touch Swipe** - Native touch gestures support
- ✅ **Keyboard Navigation** - Accessible keyboard controls
- ✅ **Autoplay** - Configurable autoplay with pause on hover
- ✅ **Smooth Animations** - CSS transitions and transforms
- ✅ **Lazy Loading** - Built-in image lazy loading
- ✅ **Customizable** - Extensive configuration options
- ✅ **Accessible** - ARIA labels and semantic HTML

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

- `Carousel::image(string $id, array $images, array $options = []): self`
- `Carousel::card(string $id, array $cards, array $options = []): self`
- `Carousel::testimonial(string $id, array $testimonials, array $options = []): self`
- `Carousel::gallery(string $id, array $images, array $options = []): self`

#### Instance Methods

- `addItem(CarouselItem|array $item): self` - Add a single item
- `addItems(array $items): self` - Add multiple items
- `setOptions(array $options): self` - Set carousel options
- `getOption(string $key, mixed $default = null): mixed` - Get an option value
- `render(): string` - Render complete carousel (HTML + CSS + JS)
- `renderHtml(): string` - Render only HTML
- `renderCss(): string` - Render only CSS
- `renderJs(): string` - Render only JavaScript
- `getId(): string` - Get carousel ID
- `getType(): string` - Get carousel type
- `getItems(): array` - Get all items
- `getOptions(): array` - Get all options

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

## 🧪 Tests

```bash
composer test
```

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


# API Documentation - PHP Carousel

Complete API reference for PHP Carousel library.

---

## Table of Contents

- [Carousel Class](#carousel-class)
- [CarouselItem Class](#carouselitem-class)
- [Static Factory Methods](#static-factory-methods)
- [Options Reference](#options-reference)
- [Exceptions](#exceptions)
- [Validators](#validators)

---

## Carousel Class

### Constants

```php
Carousel::TYPE_IMAGE       // Image carousel
Carousel::TYPE_CARD        // Card carousel
Carousel::TYPE_TESTIMONIAL // Testimonial carousel
Carousel::TYPE_GALLERY     // Gallery carousel with thumbnails
Carousel::TYPE_SIMPLE      // Simple carousel
Carousel::TYPE_INFINITE    // Infinite scrolling carousel
```

### Constructor

```php
public function __construct(
    string $id,
    string $type = self::TYPE_IMAGE,
    array $options = []
)
```

**Parameters:**
- `$id` (string): Unique carousel identifier (will be sanitized)
- `$type` (string): Carousel type (one of the TYPE_* constants)
- `$options` (array): Carousel options (see [Options Reference](#options-reference))

**Throws:**
- `InvalidCarouselTypeException` if type is invalid
- `InvalidArgumentException` if options are invalid

**Example:**
```php
$carousel = new Carousel('my-carousel', Carousel::TYPE_IMAGE, [
    'autoplay' => true,
    'autoplayInterval' => 5000,
]);
```

---

### Public Methods

#### `addItem(CarouselItem|array $item): self`

Add a single item to the carousel.

**Parameters:**
- `$item` (CarouselItem|array): Item to add (can be CarouselItem instance or array)

**Returns:** `self` (fluent interface)

**Throws:**
- `RuntimeException` if maximum 100 items limit is reached

**Example:**
```php
$carousel->addItem([
    'id' => '1',
    'title' => 'Product 1',
    'image' => 'product1.jpg',
    'link' => '/product/1',
]);

// Or with CarouselItem
$item = new CarouselItem('1', 'Product 1', 'Description', 'product1.jpg', '/product/1');
$carousel->addItem($item);
```

---

#### `addItems(array $items): self`

Add multiple items at once.

**Parameters:**
- `$items` (array): Array of items (each can be CarouselItem or array)

**Returns:** `self` (fluent interface)

**Example:**
```php
$carousel->addItems([
    ['id' => '1', 'title' => 'Product 1', 'image' => 'product1.jpg'],
    ['id' => '2', 'title' => 'Product 2', 'image' => 'product2.jpg'],
]);
```

---

#### `setOptions(array $options): self`

Update carousel options.

**Parameters:**
- `$options` (array): Options to set (see [Options Reference](#options-reference))

**Returns:** `self` (fluent interface)

**Throws:**
- `InvalidArgumentException` if options are invalid

**Example:**
```php
$carousel->setOptions([
    'autoplay' => false,
    'gap' => 20,
]);
```

---

#### `getOption(string $key, mixed $default = null): mixed`

Get a specific option value.

**Parameters:**
- `$key` (string): Option key
- `$default` (mixed): Default value if option doesn't exist

**Returns:** `mixed` - Option value or default

**Example:**
```php
$autoplay = $carousel->getOption('autoplay', true);
$gap = $carousel->getOption('gap', 16);
```

---

#### `render(): string`

Render complete carousel (HTML + CSS + JS).

**Returns:** `string` - Complete HTML output

**Throws:**
- `EmptyCarouselException` if carousel has no items

**Example:**
```php
echo $carousel->render();
```

---

#### `renderHtml(): string`

Render only the HTML structure (without CSS/JS).

**Returns:** `string` - HTML structure only

**Throws:**
- `EmptyCarouselException` if carousel has no items

**Example:**
```php
// In your template
echo $carousel->renderHtml();
```

---

#### `renderCss(): string`

Render only the CSS styles.

**Returns:** `string` - CSS styles only

**Example:**
```php
// In <head>
echo $carousel->renderCss();
```

---

#### `renderJs(): string`

Render only the JavaScript code.

**Returns:** `string` - JavaScript code only

**Example:**
```php
// Before </body>
echo $carousel->renderJs();
```

---

#### `getId(): string`

Get the carousel ID (sanitized).

**Returns:** `string` - Carousel ID

---

#### `getType(): string`

Get the carousel type.

**Returns:** `string` - Carousel type

---

#### `getItems(): array`

Get all carousel items.

**Returns:** `array` - Array of CarouselItem instances

---

#### `getOptions(): array`

Get all carousel options.

**Returns:** `array` - All options (merged with defaults)

---

## Static Factory Methods

### `image(string $id, array $images, array $options = []): self`

Create an image carousel.

**Parameters:**
- `$id` (string): Unique carousel identifier
- `$images` (array): Array of image URLs (strings) or CarouselItem arrays
- `$options` (array): Optional carousel options

**Returns:** `self` - Carousel instance

**Example:**
```php
$carousel = Carousel::image('gallery', [
    'image1.jpg',
    'image2.jpg',
    'image3.jpg',
]);
```

---

### `card(string $id, array $cards, array $options = []): self`

Create a card carousel.

**Parameters:**
- `$id` (string): Unique carousel identifier
- `$cards` (array): Array of card data (arrays or CarouselItem instances)
- `$options` (array): Optional carousel options

**Returns:** `self` - Carousel instance

**Example:**
```php
$carousel = Carousel::card('products', [
    [
        'id' => '1',
        'title' => 'Product 1',
        'content' => 'Description',
        'image' => 'product1.jpg',
        'link' => '/product/1',
    ],
]);
```

---

### `testimonial(string $id, array $testimonials, array $options = []): self`

Create a testimonial carousel.

**Parameters:**
- `$id` (string): Unique carousel identifier
- `$testimonials` (array): Array of testimonial data
- `$options` (array): Optional carousel options

**Returns:** `self` - Carousel instance

**Example:**
```php
$carousel = Carousel::testimonial('reviews', [
    [
        'id' => '1',
        'title' => 'John Doe',
        'content' => 'Great product!',
        'image' => 'avatar.jpg',
    ],
]);
```

---

### `gallery(string $id, array $images, array $options = []): self`

Create a gallery carousel with thumbnails.

**Parameters:**
- `$id` (string): Unique carousel identifier
- `$images` (array): Array of image URLs or CarouselItem arrays
- `$options` (array): Optional carousel options

**Returns:** `self` - Carousel instance

**Example:**
```php
$carousel = Carousel::gallery('photo-gallery', [
    'photo1.jpg',
    'photo2.jpg',
], [
    'showThumbnails' => true,
]);
```

---

### `infiniteCarousel(string $id, array $images, array $options = []): self`

Create an infinite scrolling carousel.

**Parameters:**
- `$id` (string): Unique carousel identifier
- `$images` (array): Array of image URLs or CarouselItem arrays
- `$options` (array): Optional carousel options

**Returns:** `self` - Carousel instance

**Example:**
```php
$carousel = Carousel::infiniteCarousel('partners', [
    'logo1.png',
    'logo2.png',
    'logo3.png',
], [
    'itemsPerSlide' => 5,
    'autoplayInterval' => 2000,
]);
```

---

### `heroBanner(string $id, array $banners, array $options = []): self`

Create a hero banner carousel (full-width, large images).

**Parameters:**
- `$id` (string): Unique carousel identifier
- `$banners` (array): Array of banner data
- `$options` (array): Optional carousel options

**Returns:** `self` - Carousel instance

**Example:**
```php
$carousel = Carousel::heroBanner('homepage-hero', [
    [
        'id' => 'banner1',
        'title' => 'Welcome',
        'image' => 'banner1.jpg',
        'link' => '/promo1',
    ],
], [
    'height' => '700px',
]);
```

---

### `productShowcase(string $id, array $products, array $options = []): self`

Create a product showcase carousel (optimized for e-commerce).

**Parameters:**
- `$id` (string): Unique carousel identifier
- `$products` (array): Array of product data
- `$options` (array): Optional carousel options

**Returns:** `self` - Carousel instance

**Example:**
```php
$carousel = Carousel::productShowcase('featured-products', [
    [
        'id' => '1',
        'title' => 'Product 1',
        'content' => 'Description',
        'image' => 'product1.jpg',
        'link' => '/product/1',
    ],
], [
    'itemsPerSlide' => 4,
]);
```

---

### `testimonialSlider(string $id, array $testimonials, array $options = []): self`

Create a testimonial slider (alias for testimonial with optimized defaults).

**Parameters:**
- `$id` (string): Unique carousel identifier
- `$testimonials` (array): Array of testimonial data
- `$options` (array): Optional carousel options

**Returns:** `self` - Carousel instance

**Example:**
```php
$carousel = Carousel::testimonialSlider('reviews', [
    [
        'id' => '1',
        'title' => 'John Doe',
        'content' => 'Great product!',
    ],
]);
```

---

## CarouselItem Class

### Constructor

```php
public function __construct(
    string $id,
    string $title = '',
    string $content = '',
    string $image = '',
    string $link = '',
    array $attributes = []
)
```

**Parameters:**
- `$id` (string): Item identifier (will be sanitized)
- `$title` (string): Item title
- `$content` (string): Item content/description
- `$image` (string): Image URL
- `$link` (string): Link URL (will be validated and sanitized)
- `$attributes` (array): Custom attributes (only `class`, `data-*`, `aria-*` allowed)

**Example:**
```php
$item = new CarouselItem(
    id: '1',
    title: 'Product 1',
    content: 'Description',
    image: 'product1.jpg',
    link: '/product/1',
    attributes: ['class' => 'featured']
);
```

---

### Static Methods

#### `fromArray(array $data): self`

Create a CarouselItem from an array.

**Parameters:**
- `$data` (array): Item data with keys: `id`, `title`, `content`, `image`, `link`, `attributes`

**Returns:** `self` - CarouselItem instance

**Example:**
```php
$item = CarouselItem::fromArray([
    'id' => '1',
    'title' => 'Product 1',
    'image' => 'product1.jpg',
    'link' => '/product/1',
]);
```

---

#### `toArray(): array`

Convert CarouselItem to array.

**Returns:** `array` - Item data as array

**Example:**
```php
$array = $item->toArray();
```

---

## Options Reference

### Display Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `itemsPerSlide` | int | 1 | Number of items per slide |
| `itemsPerSlideDesktop` | int | 1 | Items per slide on desktop |
| `itemsPerSlideTablet` | int | 1 | Items per slide on tablet |
| `itemsPerSlideMobile` | int | 1 | Items per slide on mobile |
| `gap` | int | 16 | Gap between items in pixels |
| `height` | string | 'auto' | Carousel height |
| `width` | string | '100%' | Carousel width |
| `showArrows` | bool | true | Show navigation arrows |
| `showDots` | bool | true | Show dots navigation |
| `showThumbnails` | bool | false | Show thumbnails (gallery only) |

### Behavior Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `autoplay` | bool | true | Enable autoplay |
| `autoplayInterval` | int | 5000 | Autoplay interval in milliseconds (1000-60000) |
| `loop` | bool | true | Enable infinite loop |
| `transition` | string | 'slide' | Transition type: 'slide', 'fade', 'cube' |
| `transitionDuration` | int | 500 | Transition duration in milliseconds (0-5000) |
| `keyboardNavigation` | bool | true | Enable keyboard navigation (arrow keys) |
| `touchSwipe` | bool | true | Enable touch swipe gestures |
| `lazyLoad` | bool | true | Enable lazy loading for images |
| `responsive` | bool | true | Enable responsive design |
| `minify` | bool | false | Minify CSS and JavaScript output |

---

## Exceptions

### `EmptyCarouselException`

Thrown when trying to render a carousel with no items.

```php
use JulienLinard\Carousel\Exception\EmptyCarouselException;

try {
    $carousel->render();
} catch (EmptyCarouselException $e) {
    echo "Carousel is empty";
}
```

---

### `InvalidCarouselTypeException`

Thrown when an invalid carousel type is provided.

```php
use JulienLinard\Carousel\Exception\InvalidCarouselTypeException;

try {
    new Carousel('test', 'invalid_type');
} catch (InvalidCarouselTypeException $e) {
    echo "Invalid type: " . $e->getMessage();
}
```

---

### `CarouselException`

Base exception class for all carousel exceptions.

---

## Validators

### `UrlValidator`

Validates and sanitizes URLs.

**Methods:**
- `sanitize(string $url): string` - Sanitize URL (returns '#' if invalid)
- `isSafe(string $url): bool` - Check if URL is safe

**Example:**
```php
use JulienLinard\Carousel\Validator\UrlValidator;

$safeUrl = UrlValidator::sanitize('https://example.com');
$isSafe = UrlValidator::isSafe('javascript:alert(1)'); // false
```

---

### `OptionsValidator`

Validates carousel options.

**Methods:**
- `validate(array $options): array` - Validate and return validated options

**Throws:**
- `InvalidArgumentException` if options are invalid

**Example:**
```php
use JulienLinard\Carousel\Validator\OptionsValidator;

$validated = OptionsValidator::validate([
    'autoplayInterval' => 5000,
    'itemsPerSlide' => 3,
]);
```

---

### `IdSanitizer`

Sanitizes IDs.

**Methods:**
- `sanitize(string $id): string` - Sanitize ID (alphanumeric, hyphens, underscores only, max 50 chars)
- `isValid(string $id): bool` - Check if ID is valid

**Example:**
```php
use JulienLinard\Carousel\Validator\IdSanitizer;

$cleanId = IdSanitizer::sanitize('my-carousel_123');
```

---

## JavaScript API

When a carousel is rendered, it exposes a global API:

```javascript
// Access carousel instance
const carousel = window.carouselInstances['carousel-id'];

// Available methods
carousel.destroy();           // Cleanup and remove event listeners
carousel.goToSlide(2);        // Go to specific slide (0-indexed)
carousel.nextSlide();         // Go to next slide
carousel.prevSlide();         // Go to previous slide
carousel.getCurrentIndex();   // Get current slide index
```

---

## Best Practices

### 1. Use unique IDs

```php
// ✅ Good
$carousel1 = Carousel::image('homepage-hero', $images);
$carousel2 = Carousel::image('products-grid', $products);

// ❌ Bad
$carousel1 = Carousel::image('carousel', $images);
$carousel2 = Carousel::image('carousel', $products); // ID conflict
```

### 2. Separate CSS/JS for multiple carousels

```php
// In <head>
echo $carousel1->renderCss();
echo $carousel2->renderCss();

// In body
echo $carousel1->renderHtml();
echo $carousel2->renderHtml();

// Before </body>
echo $carousel1->renderJs();
echo $carousel2->renderJs();
```

### 3. Use lazy loading for performance

```php
$carousel = Carousel::image('gallery', $images, [
    'lazyLoad' => true, // Enabled by default
]);
```

### 4. Enable minification in production

```php
$carousel = Carousel::image('gallery', $images, [
    'minify' => true, // Minify CSS/JS output
]);
```

---

## Support

- **Issues**: https://github.com/julien-lin/php-carousel/issues
- **Documentation**: See `DOCUMENTATION/` folder
- **Examples**: See `DOCUMENTATION/EXEMPLES_UTILISATION.md`


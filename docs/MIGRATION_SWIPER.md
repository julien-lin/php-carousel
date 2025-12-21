# Migration Guide: Swiper → PHP Carousel

This guide helps you migrate from Swiper.js to PHP Carousel.

## Overview

**Swiper.js** is a popular JavaScript carousel library. **PHP Carousel** is a PHP-native solution that generates HTML, CSS, and JavaScript without external dependencies.

## Key Differences

| Feature | Swiper.js | PHP Carousel |
|---------|-----------|--------------|
| **Language** | JavaScript | PHP |
| **Dependencies** | External JS library | Zero dependencies |
| **Rendering** | Client-side | Server-side |
| **SEO** | Requires SSR setup | Native SSR support |
| **Bundle Size** | ~50KB minified | Generated on-demand |
| **Styling** | CSS framework | Pure CSS, scoped |

## Migration Steps

### 1. Installation

**Swiper.js:**
```bash
npm install swiper
```

**PHP Carousel:**
```bash
composer require julienlinard/php-carousel
```

### 2. Basic Carousel

**Swiper.js:**
```html
<div class="swiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide">Slide 1</div>
        <div class="swiper-slide">Slide 2</div>
        <div class="swiper-slide">Slide 3</div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>

<script>
import Swiper from 'swiper';
new Swiper('.swiper', {
    slidesPerView: 1,
    spaceBetween: 30,
    pagination: { el: '.swiper-pagination' },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
});
</script>
```

**PHP Carousel:**
```php
use JulienLinard\Carousel\Carousel;

$carousel = Carousel::image('my-carousel', [
    'image1.jpg',
    'image2.jpg',
    'image3.jpg',
], [
    'showArrows' => true,
    'showDots' => true,
    'gap' => 30,
]);

echo $carousel->render();
```

### 3. Options Mapping

| Swiper Option | PHP Carousel Option | Notes |
|---------------|---------------------|-------|
| `slidesPerView` | `itemsPerSlide` | Same functionality |
| `spaceBetween` | `gap` | Same functionality |
| `loop` | `loop` | Same functionality |
| `autoplay` | `autoplay` | Same functionality |
| `autoplay.delay` | `autoplayInterval` | Milliseconds |
| `pagination` | `showDots` | Boolean |
| `navigation` | `showArrows` | Boolean |
| `effect` | `transition` | `'slide'` or `'fade'` |
| `keyboard` | `keyboardNav` | Same functionality |
| `touch` | `touchSwipe` | Same functionality |

### 4. Responsive Breakpoints

**Swiper.js:**
```javascript
new Swiper('.swiper', {
    slidesPerView: 1,
    breakpoints: {
        640: { slidesPerView: 2 },
        768: { slidesPerView: 3 },
        1024: { slidesPerView: 4 },
    },
});
```

**PHP Carousel:**
```php
$carousel = Carousel::image('my-carousel', $images, [
    'itemsPerSlide' => 1,
    'itemsPerSlideMobile' => 1,
    'itemsPerSlideTablet' => 2,
    'itemsPerSlideDesktop' => 3,
]);
```

### 5. Custom Styling

**Swiper.js:**
```css
.swiper {
    /* Custom styles */
}
.swiper-slide {
    /* Custom styles */
}
```

**PHP Carousel:**
```css
#carousel-my-carousel {
    /* Custom styles */
}
#carousel-my-carousel .carousel-slide {
    /* Custom styles */
}
```

### 6. Events

**Swiper.js:**
```javascript
swiper.on('slideChange', function () {
    console.log('Slide changed');
});
```

**PHP Carousel:**
```javascript
const carousel = window.CarouselAPI.get('my-carousel');
carousel.on('slideChange', (data) => {
    console.log('Slide changed', data);
});
```

### 7. Programmatic Control

**Swiper.js:**
```javascript
swiper.slideNext();
swiper.slidePrev();
swiper.slideTo(2);
```

**PHP Carousel:**
```javascript
const carousel = window.CarouselAPI.get('my-carousel');
carousel.next();
carousel.prev();
carousel.goTo(2);
```

## Advanced Features

### Virtual Slides (Large Lists)

**Swiper.js:**
```javascript
new Swiper('.swiper', {
    virtual: {
        slides: largeArray,
    },
});
```

**PHP Carousel:**
```php
$carousel = Carousel::image('my-carousel', $largeArray, [
    'virtualization' => true,
    'virtualizationThreshold' => 50,
]);
```

### Lazy Loading

**Swiper.js:**
```javascript
new Swiper('.swiper', {
    lazy: true,
});
```

**PHP Carousel:**
```php
// Lazy loading is enabled by default
$carousel = Carousel::image('my-carousel', $images);
```

### Thumbnails

**Swiper.js:**
```javascript
// Requires separate swiper instance for thumbnails
```

**PHP Carousel:**
```php
$carousel = Carousel::gallery('my-gallery', $images, [
    'showThumbnails' => true,
]);
```

## Migration Checklist

- [ ] Install PHP Carousel via Composer
- [ ] Replace Swiper HTML structure with PHP Carousel
- [ ] Map Swiper options to PHP Carousel options
- [ ] Update responsive breakpoints
- [ ] Update custom CSS selectors
- [ ] Replace JavaScript event handlers
- [ ] Update programmatic control code
- [ ] Test all carousel functionality
- [ ] Remove Swiper.js dependency
- [ ] Verify SEO (if applicable)

## Benefits of Migration

1. **Zero Dependencies**: No external JavaScript library
2. **Better SEO**: Server-side rendering by default
3. **Smaller Bundle**: No client-side library to download
4. **PHP Integration**: Native PHP API
5. **Type Safety**: PHP type hints and validation
6. **Accessibility**: WCAG 2.1 AAA compliant out of the box

## Common Issues

### Issue: Swiper styles conflict

**Solution:** PHP Carousel uses scoped CSS, so there are no conflicts.

### Issue: Custom Swiper plugins

**Solution:** PHP Carousel has built-in features (analytics, A/B testing) that may replace custom plugins.

### Issue: Dynamic content loading

**Solution:** Use PHP Carousel's `addItem()` or `addItems()` methods to dynamically add content.

## Need Help?

- Check [API Documentation](API.md)
- See [Examples](EXEMPLES_UTILISATION.md)
- Review [Best Practices](BEST_PRACTICES.md)


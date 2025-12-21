# End-to-End Testing

## Overview

The PHP Carousel library includes comprehensive E2E tests that verify the generated HTML, CSS, and JavaScript work correctly when rendered in a browser-like environment.

## Test Structure

### Base Class: `E2ETestBase`

All E2E tests extend `E2ETestBase`, which provides:

- **HTML file generation**: Creates temporary HTML files with carousel markup
- **Cleanup**: Automatically removes generated files after tests
- **Helper methods**: Assertions for HTML validation, CSS presence, JavaScript presence

### Test Suite: `CarouselE2ETest`

The main E2E test suite includes:

1. **Basic Rendering**: Verifies HTML structure, CSS, and JavaScript are present
2. **Navigation**: Tests arrow buttons and dots navigation
3. **Autoplay**: Verifies autoplay configuration
4. **Theme Support**: Tests dark/light theme application
5. **Analytics**: Verifies analytics tracking code injection
6. **Card Carousel**: Tests card carousel rendering
7. **Testimonial Carousel**: Tests testimonial carousel rendering
8. **Gallery Carousel**: Tests gallery carousel with thumbnails
9. **Multiple Carousels**: Tests multiple carousels on the same page
10. **Virtualization**: Tests virtualization for large carousels
11. **Custom Animations**: Tests custom CSS animations
12. **Accessibility**: Verifies ARIA attributes and accessibility features

## Running E2E Tests

### Run all E2E tests:

```bash
./vendor/bin/phpunit tests/E2E/
```

### Run specific test:

```bash
./vendor/bin/phpunit tests/E2E/CarouselE2ETest.php::testBasicCarouselRendering
```

### Run with testdox output:

```bash
./vendor/bin/phpunit tests/E2E/ --testdox
```

## Test Coverage

The E2E tests verify:

- ✅ HTML structure and semantic markup
- ✅ CSS presence and theme variables
- ✅ JavaScript initialization and API
- ✅ Accessibility attributes (ARIA, roles, labels)
- ✅ All carousel types (image, card, testimonial, gallery)
- ✅ Configuration options (autoplay, theme, analytics, virtualization)
- ✅ Multiple carousels on the same page

## Future Enhancements

For true browser-based E2E testing with Playwright:

1. Install Playwright PHP:
```bash
composer require --dev playwright-php/playwright
npx playwright install
```

2. Create browser-based tests:
```php
use Playwright\Playwright;

$playwright = Playwright::create();
$browser = $playwright->chromium()->launch();
$page = $browser->newPage();
$page->goto('http://localhost/test-carousel.html');
$page->click('.carousel-arrow-next');
```

## Notes

- Current E2E tests generate static HTML files and verify markup
- For interactive testing, consider using Playwright or similar tools
- Generated HTML files are automatically cleaned up after tests


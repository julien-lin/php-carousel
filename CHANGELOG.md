# Changelog

All notable changes to this project will be documented in this file.

## [1.1.0] - 2024-12-XX

### Added
- **E2E Tests**: Comprehensive end-to-end tests (12 tests, 86 assertions)
- **Performance Optimization**: Enhanced CSS/JS minification, PerformanceOptimizer class
- **Accessibility AAA**: WCAG 2.1 AAA compliance with enhanced focus styles and ARIA attributes
- **Advanced Documentation**: Migration guides, best practices, troubleshooting, CodePen examples, video tutorial scripts
- **AccessibilityEnhancer**: Utility class for contrast calculation and enhanced ARIA attributes

### Enhanced
- **CSS Minification**: Improved compression (removes zero units, shortens colors, preserves strings)
- **JavaScript Minification**: Better whitespace removal and string preservation
- **Focus Styles**: Enhanced focus-visible styles with 3px outline and offset (WCAG AAA)
- **ARIA Attributes**: Added aria-posinset, aria-setsize, aria-describedby for better screen reader support

### Documentation
- Added `docs/MIGRATION_SWIPER.md` - Migration guide from Swiper.js
- Added `docs/BEST_PRACTICES.md` - Production-ready guidelines
- Added `docs/CODEPEN_EXAMPLES.md` - Interactive examples
- Added `docs/VIDEO_TUTORIALS.md` - Video tutorial scripts
- Added `docs/TROUBLESHOOTING.md` - Common issues and solutions
- Added `docs/ACCESSIBILITY.md` - WCAG 2.1 AAA compliance guide
- Added `docs/PERFORMANCE.md` - Performance optimization guide
- Added `docs/E2E_TESTING.md` - End-to-end testing guide

### Fixed
- CSS migration tests normalization for accessibility enhancements
- Improved CSS minifier regex for zero units removal

---

## [1.0.1] - Previous version

## [3.0.0] - 2024-12-XX

### Added
- **Modular Renderer Architecture:**
  - `CompositeRenderer` - Combines HTML, CSS, and JS renderers
  - `HtmlRenderer` - Renders only HTML structure
  - `CssRenderer` - Renders only CSS styles
  - `JsRenderer` - Renders only JavaScript code
  - `RenderCacheService` - Centralized caching service
  - `RenderContext` - Context sharing between renderers
  - `AbstractRenderer` - Base class for all renderers

### Changed
- **Renderer Architecture Migration:**
  - Migrated from monolithic `CarouselRenderer` to modular architecture
  - `Carousel` class now uses `CompositeRenderer` internally
  - Improved separation of concerns (HTML, CSS, JS)
  - Better testability with independent renderers
  - Enhanced maintainability and extensibility

- **Deprecated:**
  - `CarouselRenderer` class is now deprecated (marked with `@deprecated`)
  - Kept only for backward compatibility in migration tests
  - Should not be used in new code

### Performance
- Optimized caching per renderer type
- Improved code organization and maintainability
- Better separation of concerns

### Testing
- Added migration tests to verify identical output (26 tests)
- All 206 tests passing with 523 assertions
- Complete test coverage for new renderer architecture

## [2.0.0] - 2024-12-XX

### Added
- **New Static Factory Methods:**
  - `Carousel::infiniteCarousel()` - Infinite scrolling carousel
  - `Carousel::heroBanner()` - Hero banner carousel with optimized defaults
  - `Carousel::productShowcase()` - Product showcase carousel for e-commerce
  - `Carousel::testimonialSlider()` - Testimonial slider with optimized defaults

- **Twig Integration:**
  - `CarouselExtension` with 8 Twig functions
  - `CarouselRuntime` for runtime functions
  - Full documentation and examples

- **Blade Integration (Laravel):**
  - `CarouselServiceProvider` with auto-discovery
  - 8 Blade directives (`@carousel_*`)
  - 8 helper functions
  - Full documentation and examples

- **Accessibility (WCAG 2.1 AA):**
  - Complete ARIA attributes (`aria-live`, `aria-current`, `aria-hidden`, `aria-label`)
  - Screen reader announcements
  - Support for `prefers-reduced-motion`
  - Semantic HTML with proper roles
  - Keyboard navigation improvements

- **Security Enhancements:**
  - XSS prevention with `ENT_QUOTES | ENT_HTML5`
  - URL validation and sanitization (`UrlValidator`)
  - Options validation with limits (`OptionsValidator`)
  - ID sanitization (`IdSanitizer`)
  - Maximum 100 items limit (DoS protection)
  - Attribute sanitization (only safe attributes allowed)

- **Performance Optimizations:**
  - Modular renderer architecture (replaced singleton pattern in 3.0.0)
  - Real lazy loading with Intersection Observer
  - JavaScript optimization (requestAnimationFrame, cleanup)
  - CSS/JS minification support
  - Event listener cleanup with `destroy()` method

- **UX/UI Improvements:**
  - Loading indicator with spinner
  - Image error handling with placeholder
  - Automatic loading indicator hiding
  - Better error messages

- **Documentation:**
  - Complete API documentation (`docs/API.md`)
  - Twig integration guide (`DOCUMENTATION/INTEGRATION_TWIG.md`)
  - Blade integration guide (`DOCUMENTATION/INTEGRATION_BLADE.md`)
  - Usage examples (`DOCUMENTATION/EXEMPLES_UTILISATION.md`)

- **Testing:**
  - 60 tests, 200 assertions
  - Security tests (17 tests)
  - Accessibility tests (15 tests)
  - Integration tests (Twig, Blade)
  - All tests passing

### Changed
- PHP requirement: `>=8.0` → `>=8.2` (required for PHPUnit 12.5)
- Improved HTML structure with better semantic markup
- Enhanced JavaScript with cleanup methods
- Better CSS organization and scoping

### Fixed
- Improved image display in carousels
- Added min-height to carousel wrapper and slides for image carousels
- Use height option from carousel options for image height
- Added max-width: 100% to images for better responsiveness
- Fixed CSS to ensure images are always visible even when not loaded

## [1.0.1] - 2024-12-XX

### Fixed
- Improved image display in carousels
- Added min-height to carousel wrapper and slides for image carousels
- Use height option from carousel options for image height
- Added max-width: 100% to images for better responsiveness
- Fixed CSS to ensure images are always visible even when not loaded

## [1.0.0] - 2024-12-XX

### Added
- Initial release
- Image carousel type
- Card carousel type
- Testimonial carousel type
- Gallery carousel type with thumbnails
- Responsive design support
- Touch swipe gestures
- Keyboard navigation
- Autoplay with pause on hover
- Multiple transition types (slide, fade)
- Lazy loading support
- Zero external dependencies
- Full bilingual documentation (EN/FR)


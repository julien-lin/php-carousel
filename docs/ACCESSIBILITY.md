# Accessibility (WCAG 2.1 AAA)

## Overview

The PHP Carousel library is designed to meet **WCAG 2.1 AAA** accessibility standards, ensuring that all users, including those using assistive technologies, can fully interact with carousels.

## ARIA Attributes

### Carousel Container

- `role="region"` - Identifies the carousel as a landmark region
- `aria-label` - Descriptive label for the carousel
- `aria-roledescription="carousel"` - Describes the type of widget
- `aria-describedby` - References description element
- `tabindex="0"` - Makes carousel keyboard focusable

### Slides

- `role="group"` - Groups slide content
- `aria-roledescription="slide"` - Describes the slide role
- `aria-label="Slide X of Y"` - Provides slide position information
- `aria-hidden` - Hides inactive slides from screen readers
- `aria-current="true"` - Indicates the current slide
- `aria-posinset` - Position in set (1-based)
- `aria-setsize` - Total number of slides

### Navigation Buttons

- `aria-label` - Descriptive label (e.g., "Previous slide, currently on slide 2 of 5")
- `aria-controls` - References the carousel track
- `aria-disabled` - Indicates if button is disabled (when loop is off)

### Dot Navigation

- `role="tablist"` - Container for tab navigation
- `aria-label="Slide navigation"` - Describes the navigation
- `aria-orientation="horizontal"` - Indicates horizontal layout
- `role="tab"` - Individual tab (dot)
- `aria-label="Go to slide X of Y"` - Descriptive label
- `aria-selected` - Indicates selected tab
- `aria-controls` - References the slide
- `tabindex` - Focus management (0 for active, -1 for inactive)

## Keyboard Navigation

### Supported Keys

- **Arrow Left** - Navigate to previous slide
- **Arrow Right** - Navigate to next slide
- **Escape** - Stop autoplay (if enabled)
- **Tab** - Navigate between interactive elements
- **Enter/Space** - Activate focused element

### Focus Management

- All interactive elements are keyboard accessible
- Focus order follows logical document flow
- Focus indicators are highly visible (3px outline with offset)
- Focus is trapped within carousel when navigating

## Screen Reader Support

### Live Regions

- `aria-live="polite"` - Announces slide changes without interrupting
- `aria-atomic="true"` - Announces entire slide change
- Screen reader announcement element updates on slide change

### Announcements

When slides change, screen readers announce:
- "Slide X of Y"

This is updated dynamically via JavaScript in the `.carousel-announcement` element.

## Visual Accessibility

### Focus Indicators

Enhanced focus styles meet WCAG AAA requirements:

```css
/* 3px outline with 2px offset for high visibility */
.carousel-arrow:focus-visible {
    outline: 3px solid #333;
    outline-offset: 2px;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 1), 0 0 0 5px #333;
}
```

### Color Contrast

The library includes tools to verify color contrast:

```php
use JulienLinard\Carousel\Accessibility\AccessibilityEnhancer;

// Check WCAG AA compliance (4.5:1 for normal text)
$meetsAA = AccessibilityEnhancer::meetsWCAGAA('#000000', '#ffffff');

// Check WCAG AAA compliance (7:1 for normal text)
$meetsAAA = AccessibilityEnhancer::meetsWCAGAAA('#000000', '#ffffff');
```

**Default colors meet WCAG AAA:**
- Text on background: 21:1 (black on white)
- Active dot: 4.5:1 minimum
- Buttons: High contrast with visible focus

### Reduced Motion

The carousel respects `prefers-reduced-motion`:

- **CSS**: Disables transitions and animations
- **JavaScript**: Disables autoplay when reduced motion is preferred

```css
@media (prefers-reduced-motion: reduce) {
    .carousel-track,
    .carousel-slide,
    .carousel-spinner {
        transition: none !important;
        animation: none !important;
    }
}
```

## Loading States

- Loading indicator has `role="status"`
- `aria-label="Loading carousel"` provides context
- `aria-hidden="true"` when loading is complete
- Screen readers announce loading state

## Error Handling

- Broken images are replaced with accessible placeholders
- `aria-label` updated to "Image unavailable"
- Placeholder includes descriptive text

## Best Practices

### 1. Provide Descriptive Labels

Always provide meaningful labels for carousels:

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
- **NVDA** (Windows, free)
- **JAWS** (Windows, commercial)
- **VoiceOver** (macOS/iOS, built-in)
- **TalkBack** (Android, built-in)

### 4. Verify Color Contrast

Use the `AccessibilityEnhancer` to verify contrast:

```php
$ratio = AccessibilityEnhancer::calculateContrastRatio('#000000', '#ffffff');
// Returns: 21.0 (excellent contrast)
```

### 5. Respect User Preferences

The carousel automatically:
- Respects `prefers-reduced-motion`
- Provides high-contrast focus indicators
- Supports keyboard-only navigation

## Accessibility Testing

### Automated Testing

Run accessibility tests:

```bash
./vendor/bin/phpunit tests/AccessibilityTest.php
./vendor/bin/phpunit tests/AccessibilityAAATest.php
```

### Manual Testing Checklist

- [ ] All interactive elements are keyboard accessible
- [ ] Focus indicators are visible
- [ ] Screen reader announces slide changes
- [ ] Color contrast meets WCAG AAA (7:1)
- [ ] Reduced motion is respected
- [ ] All images have alt text
- [ ] ARIA attributes are correct
- [ ] Tab order is logical

## WCAG 2.1 AAA Compliance

The library meets or exceeds:

- ✅ **1.1.1 Non-text Content** - All images have alt text or placeholders
- ✅ **1.3.1 Info and Relationships** - Semantic HTML and ARIA
- ✅ **1.4.3 Contrast (Minimum)** - AA level (4.5:1)
- ✅ **1.4.6 Contrast (Enhanced)** - AAA level (7:1) for default colors
- ✅ **2.1.1 Keyboard** - All functionality keyboard accessible
- ✅ **2.1.2 No Keyboard Trap** - Focus can move in and out
- ✅ **2.4.3 Focus Order** - Logical tab order
- ✅ **2.4.7 Focus Visible** - High-contrast focus indicators
- ✅ **3.2.1 On Focus** - No context changes on focus
- ✅ **4.1.2 Name, Role, Value** - Proper ARIA implementation
- ✅ **4.1.3 Status Messages** - Live regions for announcements

## Resources

- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)
- [ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)
- [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)


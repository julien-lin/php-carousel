# Intégration Blade - PHP Carousel

Ce guide explique comment intégrer et utiliser PHP Carousel avec Laravel Blade.

---

## Installation

### 1. Installer le package

```bash
composer require julienlinard/php-carousel
```

### 2. Enregistrer le Service Provider

Dans `config/app.php`, ajoutez le Service Provider :

```php
<?php

return [
    // ...
    'providers' => [
        // ...
        JulienLinard\Carousel\Blade\CarouselServiceProvider::class,
    ],
];
```

**Note** : Si vous utilisez Laravel 5.5+, l'auto-discovery est activé automatiquement.

---

## Utilisation

### Directives Blade disponibles

- `@carousel(id, images, options)` - Carousel d'images
- `@carousel_image(id, images, options)` - Carousel d'images
- `@carousel_card(id, cards, options)` - Carousel de cartes
- `@carousel_infinite(id, images, options)` - Carousel infini
- `@carousel_hero(id, banners, options)` - Hero banner
- `@carousel_products(id, products, options)` - Showcase produits
- `@carousel_testimonial(id, testimonials, options)` - Témoignages
- `@carousel_gallery(id, images, options)` - Galerie

### Helper functions disponibles

Toutes les directives ont aussi leurs équivalents en fonctions helper :

- `carousel(id, type, items, options)`
- `carousel_image(id, images, options)`
- `carousel_card(id, cards, options)`
- `carousel_infinite(id, images, options)`
- `carousel_hero(id, banners, options)`
- `carousel_products(id, products, options)`
- `carousel_testimonial(id, testimonials, options)`
- `carousel_gallery(id, images, options)`

---

## Exemples

### Carousel d'images simple

```blade
{{-- Avec des URLs d'images simples --}}
@carousel_image('my-carousel', [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
    'https://example.com/image3.jpg'
])
```

### Carousel infini

```blade
@php
    $images = [
        'https://example.com/img1.jpg',
        'https://example.com/img2.jpg',
        'https://example.com/img3.jpg',
        'https://example.com/img4.jpg'
    ];
@endphp

@carousel_infinite('products', $images)
```

### Hero banner avec options

```blade
@php
    $banners = [
        [
            'id' => 'banner1',
            'title' => 'Banner 1',
            'image' => 'https://example.com/banner1.jpg',
            'link' => '/promo1'
        ],
        [
            'id' => 'banner2',
            'title' => 'Banner 2',
            'image' => 'https://example.com/banner2.jpg',
            'link' => '/promo2'
        ]
    ];
@endphp

@carousel_hero('hero-banner', $banners, ['height' => '700px', 'autoplayInterval' => 4000])
```

### Carousel de produits (e-commerce)

```blade
@php
    $products = [
        [
            'id' => '1',
            'title' => 'Product 1',
            'content' => 'Description du produit 1',
            'image' => 'https://example.com/product1.jpg',
            'link' => '/product/1'
        ],
        [
            'id' => '2',
            'title' => 'Product 2',
            'content' => 'Description du produit 2',
            'image' => 'https://example.com/product2.jpg',
            'link' => '/product/2'
        ]
    ];
@endphp

@carousel_products('products', $products, [
    'itemsPerSlide' => 4,
    'gap' => 20,
    'autoplay' => false
])
```

### Utilisation avec les helpers

```blade
{{-- Avec les fonctions helper --}}
{!! carousel_infinite('products', $images)->render() !!}

{{-- Avec options --}}
{!! carousel_hero('banner', $banners, [
    'height' => '700px',
    'autoplayInterval' => 4000
])->render() !!}
```

### Séparer HTML, CSS et JS

Pour un meilleur contrôle, vous pouvez séparer le rendu :

```blade
{{-- Dans <head> --}}
<head>
    @php
        $carousel = carousel_hero('banner', $banners);
    @endphp
    {!! $carousel->renderCss() !!}
</head>

{{-- Dans le body --}}
<body>
    {!! $carousel->renderHtml() !!}
</body>

{{-- Avant </body> --}}
    {!! $carousel->renderJs() !!}
</body>
```

### Carousel avec variables du contrôleur

```blade
{{-- Dans votre contrôleur --}}
{{-- 
public function index()
{
    $images = [
        'https://example.com/img1.jpg',
        'https://example.com/img2.jpg'
    ];
    return view('home', compact('images'));
}
--}}

{{-- Dans la vue --}}
@carousel_infinite('products', $images, [
    'autoplay' => true,
    'autoplayInterval' => 3000,
    'itemsPerSlide' => 3
])
```

### Carousel de témoignages

```blade
@php
    $testimonials = [
        [
            'id' => '1',
            'title' => 'Jean Dupont',
            'content' => 'Excellent produit, je recommande !',
            'image' => 'https://example.com/avatar1.jpg'
        ],
        [
            'id' => '2',
            'title' => 'Marie Martin',
            'content' => 'Service client au top !',
            'image' => 'https://example.com/avatar2.jpg'
        ]
    ];
@endphp

@carousel_testimonial('testimonials', $testimonials, [
    'transition' => 'fade',
    'autoplayInterval' => 6000
])
```

---

## Options disponibles

Toutes les options standard sont disponibles :

```blade
@carousel_image('my-carousel', $images, [
    'autoplay' => true,              // Lecture automatique
    'autoplayInterval' => 5000,      // Intervalle en ms
    'loop' => true,                  // Boucle infinie
    'transition' => 'slide',         // slide, fade, cube
    'transitionDuration' => 500,     // Durée transition en ms
    'showArrows' => true,            // Afficher les flèches
    'showDots' => true,              // Afficher les points
    'keyboardNavigation' => true,    // Navigation clavier
    'touchSwipe' => true,           // Swipe tactile
    'itemsPerSlide' => 1,           // Items par slide
    'itemsPerSlideDesktop' => 4,     // Desktop
    'itemsPerSlideTablet' => 3,     // Tablet
    'itemsPerSlideMobile' => 1,     // Mobile
    'gap' => 10,                    // Espacement entre items
    'height' => '400px',            // Hauteur du carousel
    'lazyLoad' => true,             // Lazy loading
    'minify' => false               // Minification CSS/JS
])
```

---

## Bonnes pratiques

### 1. Utiliser des IDs uniques

Chaque carousel doit avoir un ID unique :

```blade
{{-- ✅ Bon --}}
@carousel_image('homepage-hero', $images)
@carousel_image('products-grid', $products)

{{-- ❌ Éviter --}}
@carousel_image('carousel', $images)
@carousel_image('carousel', $products)  {{-- ID dupliqué --}}
```

### 2. Séparer CSS/JS pour les performances

Pour plusieurs carousels sur la même page :

```blade
{{-- Dans <head> --}}
@php
    $carousel1 = carousel_hero('hero', $banners);
    $carousel2 = carousel_products('products', $products);
@endphp
{!! $carousel1->renderCss() !!}
{!! $carousel2->renderCss() !!}

{{-- Dans le body --}}
{!! $carousel1->renderHtml() !!}
{!! $carousel2->renderHtml() !!}

{{-- Avant </body> --}}
{!! $carousel1->renderJs() !!}
{!! $carousel2->renderJs() !!}
```

### 3. Utiliser le lazy loading

Par défaut activé, mais vous pouvez le désactiver :

```blade
@carousel_image('my-carousel', $images, [
    'lazyLoad' => false  // Désactiver pour les images critiques
])
```

### 4. Minification en production

```blade
@carousel_image('my-carousel', $images, [
    'minify' => true  // Activer en production
])
```

### 5. Utiliser les variables du contrôleur

```blade
{{-- Dans le contrôleur --}}
public function index()
{
    $products = Product::latest()->take(10)->get()->map(function ($product) {
        return [
            'id' => $product->id,
            'title' => $product->name,
            'content' => $product->description,
            'image' => $product->image_url,
            'link' => route('products.show', $product)
        ];
    })->toArray();
    
    return view('home', compact('products'));
}

{{-- Dans la vue --}}
@carousel_products('featured-products', $products)
```

---

## Intégration avec Laravel

### Auto-discovery (Laravel 5.5+)

Le Service Provider est automatiquement découvert. Aucune configuration nécessaire.

### Configuration manuelle (Laravel < 5.5)

Dans `config/app.php` :

```php
'providers' => [
    // ...
    JulienLinard\Carousel\Blade\CarouselServiceProvider::class,
],
```

### Utilisation dans les layouts

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }}</title>
    @stack('styles')
</head>
<body>
    @yield('content')
    
    @stack('scripts')
</body>
</html>
```

```blade
{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@push('styles')
    @php
        $carousel = carousel_hero('hero', $banners);
    @endphp
    {!! $carousel->renderCss() !!}
@endpush

@section('content')
    {!! $carousel->renderHtml() !!}
@endsection

@push('scripts')
    {!! $carousel->renderJs() !!}
@endpush
```

---

## Dépannage

### Le Service Provider n'est pas chargé

Vérifiez que le Service Provider est bien enregistré dans `config/app.php` ou que l'auto-discovery est activé.

### Erreur "Class not found"

Vérifiez l'autoload dans `composer.json` :

```json
{
    "autoload": {
        "psr-4": {
            "JulienLinard\\Carousel\\": "src/"
        }
    }
}
```

Puis exécutez :

```bash
composer dump-autoload
```

### Le carousel ne s'affiche pas

1. Vérifiez que vous utilisez `{!! !!}` pour éviter l'échappement HTML
2. Vérifiez que le CSS et JS sont bien chargés
3. Inspectez la console JavaScript pour les erreurs
4. Vérifiez que les directives sont bien utilisées (pas de `@` manquant)

### Les helpers ne fonctionnent pas

Assurez-vous que le Service Provider est bien chargé. Vous pouvez vérifier avec :

```php
// Dans tinker
php artisan tinker
>>> function_exists('carousel_image')
=> true
```

---

## Support

Pour plus d'informations :
- Documentation complète : [README.md](../README.md)
- Issues : https://github.com/julien-lin/php-carousel/issues


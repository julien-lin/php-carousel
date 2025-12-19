# Exemples d'Utilisation - PHP Carousel

## Nouvelles Méthodes Statiques

### 1. Infinite Carousel

Carousel infini avec défilement continu, idéal pour les logos partenaires, produits, etc.

#### PHP pur
```php
<?php

use JulienLinard\Carousel\Carousel;

$images = [
    'https://example.com/logo1.png',
    'https://example.com/logo2.png',
    'https://example.com/logo3.png',
    'https://example.com/logo4.png',
    'https://example.com/logo5.png',
];

$carousel = Carousel::infiniteCarousel('partners', $images, [
    'itemsPerSlide' => 5,
    'itemsPerSlideDesktop' => 5,
    'itemsPerSlideTablet' => 3,
    'itemsPerSlideMobile' => 2,
    'autoplayInterval' => 2000,
    'gap' => 30,
]);

echo $carousel->render();
```

#### Avec Twig
```twig
{# Simple #}
{{ carousel_infinite('partners', partnerLogos).render()|raw }}

{# Avec options #}
{% set carousel = carousel_infinite('products', productImages, {
    'itemsPerSlide': 4,
    'autoplayInterval': 3000,
    'gap': 20
}) %}
{{ carousel.render()|raw }}

{# Séparer HTML, CSS, JS #}
{# Dans <head> #}
{{ carousel.renderCss()|raw }}

{# Dans le body #}
{{ carousel.renderHtml()|raw }}

{# Avant </body> #}
{{ carousel.renderJs()|raw }}
```

#### Avec Blade
```blade
{{-- Directive --}}
@carousel_infinite('partners', $partnerLogos)

{{-- Helper avec options --}}
{!! carousel_infinite('products', $productImages, [
    'itemsPerSlide' => 4,
    'autoplayInterval' => 3000,
    'gap' => 20
])->render() !!}

{{-- Séparer HTML, CSS, JS --}}
{{-- Dans <head> --}}
{!! $carousel->renderCss() !!}

{{-- Dans le body --}}
{!! $carousel->renderHtml() !!}

{{-- Avant </body> --}}
{!! $carousel->renderJs() !!}
```

### 2. Hero Banner

Bannière hero full-width pour la page d'accueil.

#### PHP pur
```php
<?php

use JulienLinard\Carousel\Carousel;

$banners = [
    [
        'id' => 'banner1',
        'title' => 'Bienvenue sur notre site',
        'content' => 'Découvrez nos produits exceptionnels',
        'image' => '/images/banner1.jpg',
        'link' => '/shop',
    ],
    [
        'id' => 'banner2',
        'title' => 'Soldes d\'été',
        'content' => 'Jusqu\'à 50% de réduction',
        'image' => '/images/banner2.jpg',
        'link' => '/sale',
    ],
    [
        'id' => 'banner3',
        'title' => 'Nouvelle collection',
        'content' => 'Découvrez nos nouveautés',
        'image' => '/images/banner3.jpg',
        'link' => '/new',
    ],
];

$carousel = Carousel::heroBanner('home-hero', $banners, [
    'height' => '700px',
    'autoplayInterval' => 5000,
    'transition' => 'fade',
    'showDots' => true,
    'showArrows' => true,
]);

echo $carousel->render();
```

#### Avec Twig
```twig
{{ carousel_hero('home-hero', banners, {
    'height': '700px',
    'autoplayInterval': 5000,
    'transition': 'fade'
}).render()|raw }}
```

#### Avec Blade
```blade
@carousel_hero('home-hero', $banners, ['height' => '700px', 'autoplayInterval' => 5000])
```

### 3. Product Showcase

Carousel optimisé pour l'affichage de produits e-commerce.

#### PHP pur
```php
<?php

use JulienLinard\Carousel\Carousel;

$products = [
    [
        'id' => '1',
        'title' => 'Casque Premium',
        'content' => 'Son haute qualité avec annulation de bruit active',
        'image' => '/images/products/headphones.jpg',
        'link' => '/products/headphones',
    ],
    [
        'id' => '2',
        'title' => 'Souris Sans Fil',
        'content' => 'Design ergonomique, autonomie 6 mois',
        'image' => '/images/products/mouse.jpg',
        'link' => '/products/mouse',
    ],
    [
        'id' => '3',
        'title' => 'Clavier Mécanique',
        'content' => 'Switches Cherry MX, rétroéclairage RGB',
        'image' => '/images/products/keyboard.jpg',
        'link' => '/products/keyboard',
    ],
    // ... plus de produits
];

$carousel = Carousel::productShowcase('featured-products', $products, [
    'itemsPerSlide' => 4,
    'itemsPerSlideDesktop' => 4,
    'itemsPerSlideTablet' => 3,
    'itemsPerSlideMobile' => 2,
    'gap' => 24,
    'autoplay' => false, // Pas d'autoplay pour les produits
    'showArrows' => true,
    'showDots' => false,
]);

echo $carousel->render();
```

#### Avec Twig
```twig
{{ carousel_products('featured-products', products, {
    'itemsPerSlide': 4,
    'gap': 24,
    'autoplay': false
}).render()|raw }}
```

#### Avec Blade
```blade
@carousel_products('featured-products', $products, [
    'itemsPerSlide' => 4,
    'gap' => 24,
    'autoplay' => false
])
```

### 4. Testimonial Slider

Carousel de témoignages avec transition fade.

#### PHP pur
```php
<?php

use JulienLinard\Carousel\Carousel;

$testimonials = [
    [
        'id' => '1',
        'title' => 'Sarah Johnson',
        'content' => 'Service exceptionnel ! Je recommande vivement cette entreprise.',
        'image' => '/avatars/sarah.jpg',
    ],
    [
        'id' => '2',
        'title' => 'Michael Chen',
        'content' => 'Qualité irréprochable et livraison rapide. Parfait !',
        'image' => '/avatars/michael.jpg',
    ],
    [
        'id' => '3',
        'title' => 'Emma Wilson',
        'content' => 'Très satisfaite de mon achat. Je reviendrai certainement.',
        'image' => '/avatars/emma.jpg',
    ],
];

$carousel = Carousel::testimonialSlider('customer-reviews', $testimonials, [
    'transition' => 'fade',
    'autoplayInterval' => 6000,
    'showDots' => true,
    'showArrows' => false,
]);

echo $carousel->render();
```

#### Avec Twig
```twig
{{ carousel_testimonial('customer-reviews', testimonials, {
    'autoplayInterval': 6000
}).render()|raw }}
```

#### Avec Blade
```blade
@carousel_testimonial('customer-reviews', $testimonials, ['autoplayInterval' => 6000])
```

---

## Cas d'usage avancés

### Multiple carousels sur une page

#### PHP pur
```php
<?php

use JulienLinard\Carousel\Carousel;

// Hero banner
$hero = Carousel::heroBanner('hero', $heroBanners);

// Products carousel
$products = Carousel::productShowcase('products', $productList);

// Partners infinite carousel
$partners = Carousel::infiniteCarousel('partners', $partnerLogos);

// Testimonials
$testimonials = Carousel::testimonialSlider('reviews', $customerReviews);

// Rendu
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $hero->renderCss(); ?>
    <?php echo $products->renderCss(); ?>
    <?php echo $partners->renderCss(); ?>
    <?php echo $testimonials->renderCss(); ?>
</head>
<body>
    <?php echo $hero->renderHtml(); ?>
    <?php echo $products->renderHtml(); ?>
    <?php echo $partners->renderHtml(); ?>
    <?php echo $testimonials->renderHtml(); ?>
    
    <?php echo $hero->renderJs(); ?>
    <?php echo $products->renderJs(); ?>
    <?php echo $partners->renderJs(); ?>
    <?php echo $testimonials->renderJs(); ?>
</body>
</html>
```

#### Avec Twig
```twig
<!DOCTYPE html>
<html>
<head>
    {{ carousel_hero('hero', heroBanners).renderCss()|raw }}
    {{ carousel_products('products', products).renderCss()|raw }}
    {{ carousel_infinite('partners', partners).renderCss()|raw }}
    {{ carousel_testimonial('reviews', reviews).renderCss()|raw }}
</head>
<body>
    {{ carousel_hero('hero', heroBanners).renderHtml()|raw }}
    {{ carousel_products('products', products).renderHtml()|raw }}
    {{ carousel_infinite('partners', partners).renderHtml()|raw }}
    {{ carousel_testimonial('reviews', reviews).renderHtml()|raw }}
    
    {{ carousel_hero('hero', heroBanners).renderJs()|raw }}
    {{ carousel_products('products', products).renderJs()|raw }}
    {{ carousel_infinite('partners', partners).renderJs()|raw }}
    {{ carousel_testimonial('reviews', reviews).renderJs()|raw }}
</body>
</html>
```

#### Avec Blade
```blade
<!DOCTYPE html>
<html>
<head>
    {!! carousel_hero('hero', $heroBanners)->renderCss() !!}
    {!! carousel_products('products', $products)->renderCss() !!}
    {!! carousel_infinite('partners', $partners)->renderCss() !!}
    {!! carousel_testimonial('reviews', $reviews)->renderCss() !!}
</head>
<body>
    {!! carousel_hero('hero', $heroBanners)->renderHtml() !!}
    {!! carousel_products('products', $products)->renderHtml() !!}
    {!! carousel_infinite('partners', $partners)->renderHtml() !!}
    {!! carousel_testimonial('reviews', $reviews)->renderHtml() !!}
    
    {!! carousel_hero('hero', $heroBanners)->renderJs() !!}
    {!! carousel_products('products', $products)->renderJs() !!}
    {!! carousel_infinite('partners', $partners)->renderJs() !!}
    {!! carousel_testimonial('reviews', $reviews)->renderJs() !!}
</body>
</html>
```

### Carousel avec données dynamiques (Symfony)

#### Controller
```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use JulienLinard\Carousel\Carousel;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        // Récupérer les données depuis la base
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findFeatured();
        
        // Transformer en format carousel
        $carouselData = array_map(function($product) {
            return [
                'id' => $product->getId(),
                'title' => $product->getName(),
                'content' => $product->getDescription(),
                'image' => $product->getImageUrl(),
                'link' => $this->generateUrl('product_show', ['id' => $product->getId()]),
            ];
        }, $products);
        
        // Créer le carousel
        $carousel = Carousel::productShowcase('featured-products', $carouselData, [
            'itemsPerSlide' => 4,
            'gap' => 20,
        ]);
        
        return $this->render('home/index.html.twig', [
            'carousel' => $carousel,
        ]);
    }
}
```

#### Template Twig
```twig
{% extends 'base.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    {{ carousel.renderCss()|raw }}
{% endblock %}

{% block body %}
    <div class="container">
        <h1>Produits vedettes</h1>
        {{ carousel.renderHtml()|raw }}
    </div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    {{ carousel.renderJs()|raw }}
{% endblock %}
```

### Carousel avec données dynamiques (Laravel)

#### Controller
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JulienLinard\Carousel\Carousel;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('featured', true)
            ->limit(12)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->name,
                    'content' => $product->description,
                    'image' => $product->image_url,
                    'link' => route('products.show', $product->id),
                ];
            })
            ->toArray();
        
        $carousel = Carousel::productShowcase('featured-products', $products, [
            'itemsPerSlide' => 4,
            'gap' => 20,
        ]);
        
        return view('home.index', compact('carousel'));
    }
}
```

#### Template Blade
```blade
@extends('layouts.app')

@section('styles')
    {!! $carousel->renderCss() !!}
@endsection

@section('content')
    <div class="container">
        <h1>Featured Products</h1>
        {!! $carousel->renderHtml() !!}
    </div>
@endsection

@section('scripts')
    {!! $carousel->renderJs() !!}
@endsection
```

---

## Options de configuration complètes

### Toutes les options disponibles

```php
$carousel = Carousel::infiniteCarousel('my-carousel', $images, [
    // Autoplay
    'autoplay' => true,                    // Activer/désactiver l'autoplay
    'autoplayInterval' => 3000,             // Intervalle en millisecondes (1000-60000)
    
    // Navigation
    'showArrows' => true,                  // Afficher les flèches
    'showDots' => true,                    // Afficher les points indicateurs
    'showThumbnails' => false,            // Afficher les miniatures (gallery uniquement)
    
    // Layout
    'itemsPerSlide' => 3,                  // Items par slide (1-10)
    'itemsPerSlideDesktop' => 4,          // Desktop (1-10)
    'itemsPerSlideTablet' => 3,           // Tablet (1-10)
    'itemsPerSlideMobile' => 2,            // Mobile (1-10)
    'gap' => 20,                           // Espacement entre items (px)
    
    // Animation
    'transition' => 'slide',              // 'slide', 'fade', 'cube'
    'transitionDuration' => 500,           // Durée transition (ms, 0-5000)
    
    // Comportement
    'loop' => true,                        // Boucle infinie
    'responsive' => true,                  // Responsive design
    'lazyLoad' => true,                    // Lazy loading des images
    'keyboardNavigation' => true,         // Navigation clavier
    'touchSwipe' => true,                 // Swipe tactile
    
    // Style
    'height' => 'auto',                    // Hauteur du carousel
    'width' => '100%',                     // Largeur du carousel
]);
```

---

## Bonnes pratiques

### 1. IDs uniques
Toujours utiliser des IDs uniques pour chaque carousel sur une page :
```php
Carousel::infiniteCarousel('products-home', $images1);
Carousel::infiniteCarousel('products-category', $images2); // ✅ ID différent
```

### 2. Séparation HTML/CSS/JS
Pour de meilleures performances, séparer le rendu :
```php
// Dans <head>
echo $carousel->renderCss();

// Dans le body
echo $carousel->renderHtml();

// Avant </body>
echo $carousel->renderJs();
```

### 3. Lazy loading
Activer le lazy loading pour les carousels avec beaucoup d'images :
```php
Carousel::infiniteCarousel('gallery', $manyImages, [
    'lazyLoad' => true, // ✅ Chargement différé
]);
```

### 4. Accessibilité
Respecter les préférences utilisateur :
```php
// Le carousel respecte automatiquement prefers-reduced-motion
// Pas besoin de configuration supplémentaire
```

### 5. Performance
Pour plusieurs carousels, regrouper le CSS/JS :
```php
// Collecter tous les CSS
$allCss = '';
foreach ($carousels as $carousel) {
    $allCss .= $carousel->renderCss();
}
echo $allCss; // Une seule balise <style>
```

---

## Dépannage

### Le carousel ne s'affiche pas
- Vérifier que l'ID est unique
- Vérifier que les images sont accessibles
- Vérifier la console JavaScript pour les erreurs

### Les images ne se chargent pas
- Vérifier les URLs des images
- Vérifier les permissions d'accès
- Activer le lazy loading si beaucoup d'images

### Le carousel ne fonctionne pas avec Twig/Blade
- Vérifier que l'extension est bien enregistrée
- Vérifier que le service provider est chargé (Blade)
- Utiliser `|raw` (Twig) ou `{!! !!}` (Blade) pour le rendu


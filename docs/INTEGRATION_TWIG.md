# Intégration Twig - PHP Carousel

Ce guide explique comment intégrer et utiliser PHP Carousel avec Twig.

---

## Installation

### 1. Installer les dépendances

```bash
composer require julienlinard/php-carousel
composer require twig/twig
```

### 2. Enregistrer l'extension Twig

```php
<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use JulienLinard\Carousel\Twig\CarouselExtension;

// Créer l'environnement Twig
$loader = new FilesystemLoader('/path/to/templates');
$twig = new Environment($loader);

// Enregistrer l'extension Carousel
$twig->addExtension(new CarouselExtension());
```

---

## Utilisation

### Fonctions Twig disponibles

- `carousel(id, type, items, options)` - Carousel générique
- `carousel_image(id, images, options)` - Carousel d'images
- `carousel_card(id, cards, options)` - Carousel de cartes
- `carousel_infinite(id, images, options)` - Carousel infini
- `carousel_hero(id, banners, options)` - Hero banner
- `carousel_products(id, products, options)` - Showcase produits
- `carousel_testimonial(id, testimonials, options)` - Témoignages
- `carousel_gallery(id, images, options)` - Galerie

---

## Exemples

### Carousel d'images simple

```twig
{# Avec des URLs d'images simples #}
{{ carousel_image('my-carousel', [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
    'https://example.com/image3.jpg'
])|raw }}
```

### Carousel infini

```twig
{% set images = [
    'https://example.com/img1.jpg',
    'https://example.com/img2.jpg',
    'https://example.com/img3.jpg',
    'https://example.com/img4.jpg'
] %}

{{ carousel_infinite('products', images)|raw }}
```

### Hero banner avec options

```twig
{% set banners = [
    {
        'id': 'banner1',
        'title': 'Banner 1',
        'image': 'https://example.com/banner1.jpg',
        'link': '/promo1'
    },
    {
        'id': 'banner2',
        'title': 'Banner 2',
        'image': 'https://example.com/banner2.jpg',
        'link': '/promo2'
    }
] %}

{{ carousel_hero('hero-banner', banners, {
    'height': '700px',
    'autoplayInterval': 4000,
    'transition': 'fade'
})|raw }}
```

### Carousel de produits (e-commerce)

```twig
{% set products = [
    {
        'id': '1',
        'title': 'Product 1',
        'content': 'Description du produit 1',
        'image': 'https://example.com/product1.jpg',
        'link': '/product/1'
    },
    {
        'id': '2',
        'title': 'Product 2',
        'content': 'Description du produit 2',
        'image': 'https://example.com/product2.jpg',
        'link': '/product/2'
    }
] %}

{{ carousel_products('products', products, {
    'itemsPerSlide': 4,
    'gap': 20,
    'autoplay': false
})|raw }}
```

### Séparer HTML, CSS et JS

Pour un meilleur contrôle, vous pouvez séparer le rendu :

```twig
{# Dans <head> #}
<head>
    {% set carousel = carousel_hero('banner', banners) %}
    {{ carousel.renderCss()|raw }}
</head>

{# Dans le body #}
<body>
    {{ carousel.renderHtml()|raw }}
</body>

{# Avant </body> #}
    {{ carousel.renderJs()|raw }}
</body>
```

### Carousel avec variables

```twig
{% set carousel = carousel_infinite('products', products, {
    'autoplay': true,
    'autoplayInterval': 3000,
    'itemsPerSlide': 3,
    'showDots': false
}) %}

{{ carousel.render()|raw }}
```

### Carousel de témoignages

```twig
{% set testimonials = [
    {
        'id': '1',
        'title': 'Jean Dupont',
        'content': 'Excellent produit, je recommande !',
        'image': 'https://example.com/avatar1.jpg'
    },
    {
        'id': '2',
        'title': 'Marie Martin',
        'content': 'Service client au top !',
        'image': 'https://example.com/avatar2.jpg'
    }
] %}

{{ carousel_testimonial('testimonials', testimonials, {
    'transition': 'fade',
    'autoplayInterval': 6000
})|raw }}
```

---

## Options disponibles

Toutes les options standard sont disponibles :

```twig
{{ carousel_image('my-carousel', images, {
    'autoplay': true,              # Lecture automatique
    'autoplayInterval': 5000,      # Intervalle en ms
    'loop': true,                  # Boucle infinie
    'transition': 'slide',         # slide, fade, cube
    'transitionDuration': 500,     # Durée transition en ms
    'showArrows': true,            # Afficher les flèches
    'showDots': true,               # Afficher les points
    'keyboardNavigation': true,    # Navigation clavier
    'touchSwipe': true,            # Swipe tactile
    'itemsPerSlide': 1,            # Items par slide
    'itemsPerSlideDesktop': 4,     # Desktop
    'itemsPerSlideTablet': 3,      # Tablet
    'itemsPerSlideMobile': 1,     # Mobile
    'gap': 10,                     # Espacement entre items
    'height': '400px',             # Hauteur du carousel
    'lazyLoad': true,              # Lazy loading
    'minify': false                # Minification CSS/JS
})|raw }}
```

---

## Bonnes pratiques

### 1. Utiliser des IDs uniques

Chaque carousel doit avoir un ID unique :

```twig
{# ✅ Bon #}
{{ carousel_image('homepage-hero', images)|raw }}
{{ carousel_image('products-grid', products)|raw }}

{# ❌ Éviter #}
{{ carousel_image('carousel', images)|raw }}
{{ carousel_image('carousel', products)|raw }}  {# ID dupliqué #}
```

### 2. Séparer CSS/JS pour les performances

Pour plusieurs carousels sur la même page :

```twig
{# Dans <head> #}
{% set carousel1 = carousel_hero('hero', banners) %}
{% set carousel2 = carousel_products('products', products) %}
{{ carousel1.renderCss()|raw }}
{{ carousel2.renderCss()|raw }}

{# Dans le body #}
{{ carousel1.renderHtml()|raw }}
{{ carousel2.renderHtml()|raw }}

{# Avant </body> #}
{{ carousel1.renderJs()|raw }}
{{ carousel2.renderJs()|raw }}
```

### 3. Utiliser le lazy loading

Par défaut activé, mais vous pouvez le désactiver :

```twig
{{ carousel_image('my-carousel', images, {
    'lazyLoad': false  # Désactiver pour les images critiques
})|raw }}
```

### 4. Minification en production

```twig
{{ carousel_image('my-carousel', images, {
    'minify': true  # Activer en production
})|raw }}
```

---

## Intégration avec Symfony

### Configuration dans `config/services.yaml`

```yaml
services:
    JulienLinard\Carousel\Twig\CarouselExtension:
        tags:
            - { name: twig.extension }
```

### Utilisation dans les templates Symfony

```twig
{# templates/home/index.html.twig #}
{{ carousel_hero('homepage-banner', banners)|raw }}
```

---

## Dépannage

### L'extension n'est pas reconnue

Vérifiez que l'extension est bien enregistrée :

```php
$twig->addExtension(new \JulienLinard\Carousel\Twig\CarouselExtension());
```

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

1. Vérifiez que vous utilisez `|raw` pour éviter l'échappement HTML
2. Vérifiez que le CSS et JS sont bien chargés
3. Inspectez la console JavaScript pour les erreurs

---

## Support

Pour plus d'informations :
- Documentation complète : [README.md](../README.md)
- Issues : https://github.com/julien-lin/php-carousel/issues


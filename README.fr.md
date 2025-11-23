# PHP Carousel

[🇬🇧 Lire en anglais](README.md) | [🇫🇷 Lire en français](README.fr.md)

## 💝 Soutenir le projet

Si ce bundle vous est utile, envisagez de [devenir un sponsor](https://github.com/sponsors/julien-lin) pour soutenir le développement et la maintenance de ce projet open source.

---

Une librairie de carrousels moderne et performante pour PHP avec des designs élégants. Implémentation CSS/JS native pure avec **zéro dépendance externe**.

## 🚀 Installation

```bash
composer require julienlinard/php-carousel
```

**Requirements** : PHP 8.0 ou supérieur

## ⚡ Démarrage rapide

### Carousel d'images simple

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Carousel\Carousel;

// Créer un carousel d'images
$carousel = Carousel::image('mon-carousel', [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
    'https://example.com/image3.jpg',
]);

// Afficher le carousel
echo $carousel->render();
```

### Carousel de cartes

```php
use JulienLinard\Carousel\Carousel;

$carousel = Carousel::card('produits', [
    [
        'id' => '1',
        'title' => 'Produit 1',
        'content' => 'Description du produit 1',
        'image' => 'https://example.com/produit1.jpg',
        'link' => '/produit/1',
    ],
    [
        'id' => '2',
        'title' => 'Produit 2',
        'content' => 'Description du produit 2',
        'image' => 'https://example.com/produit2.jpg',
        'link' => '/produit/2',
    ],
], [
    'itemsPerSlide' => 3,
    'itemsPerSlideMobile' => 1,
]);

echo $carousel->render();
```

### Carousel de témoignages

```php
use JulienLinard\Carousel\Carousel;

$carousel = Carousel::testimonial('temoignages', [
    [
        'id' => '1',
        'title' => 'Jean Dupont',
        'content' => 'Ce produit a changé ma vie ! Je le recommande vivement.',
        'image' => 'https://example.com/avatar1.jpg',
    ],
    [
        'id' => '2',
        'title' => 'Marie Martin',
        'content' => 'Qualité exceptionnelle et service client excellent.',
        'image' => 'https://example.com/avatar2.jpg',
    ],
], [
    'transition' => 'fade',
    'autoplayInterval' => 6000,
]);

echo $carousel->render();
```

## 📋 Fonctionnalités

- ✅ **Zéro Dépendance** - Implémentation CSS/JS native pure
- ✅ **Types Multiples** - Carrousels Image, Carte, Témoignage, Galerie
- ✅ **Entièrement Responsive** - Optimisé mobile, tablette et desktop
- ✅ **Swipe Tactile** - Support des gestes tactiles natifs
- ✅ **Navigation Clavier** - Contrôles clavier accessibles
- ✅ **Lecture Automatique** - Autoplay configurable avec pause au survol
- ✅ **Animations Fluides** - Transitions et transformations CSS
- ✅ **Chargement Différé** - Lazy loading d'images intégré
- ✅ **Personnalisable** - Options de configuration étendues
- ✅ **Accessible** - Labels ARIA et HTML sémantique

## 📖 Documentation

### Types de Carrousels

#### Carousel d'Images

Parfait pour les galeries d'images et les bannières hero.

```php
$carousel = Carousel::image('galerie', [
    'image1.jpg',
    'image2.jpg',
    'image3.jpg',
], [
    'height' => '500px',
    'showDots' => true,
    'showArrows' => true,
]);
```

#### Carousel de Cartes

Idéal pour les listes de produits, articles de blog ou cartes de fonctionnalités.

```php
$carousel = Carousel::card('produits', $produits, [
    'itemsPerSlide' => 3,
    'itemsPerSlideDesktop' => 3,
    'itemsPerSlideTablet' => 2,
    'itemsPerSlideMobile' => 1,
    'gap' => 24,
]);
```

#### Carousel de Témoignages

Parfait pour les avis clients et témoignages.

```php
$carousel = Carousel::testimonial('avis', $temoignages, [
    'transition' => 'fade',
    'autoplayInterval' => 7000,
]);
```

#### Carousel Galerie

Galerie avancée avec navigation par miniatures.

```php
$carousel = Carousel::gallery('galerie-photos', $images, [
    'showThumbnails' => true,
    'itemsPerSlide' => 1,
]);
```

### Options de Configuration

```php
$carousel = new Carousel('mon-carousel', Carousel::TYPE_IMAGE, [
    // Autoplay
    'autoplay' => true,                    // Activer/désactiver l'autoplay
    'autoplayInterval' => 5000,             // Intervalle d'autoplay en millisecondes
    
    // Navigation
    'showArrows' => true,                  // Afficher les flèches de navigation
    'showDots' => true,                    // Afficher les indicateurs de points
    'showThumbnails' => false,            // Afficher les miniatures (galerie uniquement)
    
    // Mise en page
    'itemsPerSlide' => 1,                  // Nombre d'éléments par slide
    'itemsPerSlideDesktop' => 1,           // Éléments par slide desktop
    'itemsPerSlideTablet' => 1,            // Éléments par slide tablette
    'itemsPerSlideMobile' => 1,            // Éléments par slide mobile
    'gap' => 16,                           // Espacement entre les éléments (px)
    
    // Animation
    'transition' => 'slide',               // 'slide', 'fade', 'cube'
    'transitionDuration' => 500,           // Durée de transition (ms)
    
    // Comportement
    'loop' => true,                        // Boucle à travers les slides
    'responsive' => true,                  // Activer le comportement responsive
    'lazyLoad' => true,                    // Activer le lazy loading
    'keyboardNavigation' => true,          // Activer la navigation clavier
    'touchSwipe' => true,                  // Activer le swipe tactile
    
    // Style
    'height' => 'auto',                    // Hauteur du carousel
    'width' => '100%',                     // Largeur du carousel
]);
```

### Utilisation Avancée

#### Items Personnalisés

```php
use JulienLinard\Carousel\Carousel;
use JulienLinard\Carousel\CarouselItem;

$carousel = new Carousel('personnalise', Carousel::TYPE_CARD);

$carousel->addItem(new CarouselItem(
    id: 'item1',
    title: 'Item Personnalisé',
    content: 'Ceci est un item de carousel personnalisé',
    image: 'https://example.com/image.jpg',
    link: '/item/1',
    attributes: ['class' => 'custom-class']
));

$carousel->addItem([
    'id' => 'item2',
    'title' => 'Autre Item',
    'content' => 'Ajouté depuis un tableau',
    'image' => 'https://example.com/image2.jpg',
]);

echo $carousel->render();
```

#### Séparer HTML, CSS et JS

```php
// Afficher uniquement le HTML
echo $carousel->renderHtml();

// Afficher uniquement le CSS (dans <head>)
echo $carousel->renderCss();

// Afficher uniquement le JavaScript (avant </body>)
echo $carousel->renderJs();
```

#### Plusieurs Carrousels sur la Même Page

```php
$carousel1 = Carousel::image('carousel-1', $images1);
$carousel2 = Carousel::card('carousel-2', $cartes);

// Chaque carousel a des IDs et styles uniques
echo $carousel1->render();
echo $carousel2->render();
```

## 🎨 Styling

Le carousel utilise du CSS pur sans dépendances externes. Tous les styles sont limités au conteneur du carousel pour éviter les conflits.

### Style Personnalisé

Vous pouvez surcharger les styles en utilisant CSS :

```css
#carousel-mon-carousel .carousel-arrow {
    background: #votre-couleur;
}

#carousel-mon-carousel .carousel-dot.active {
    background: #votre-couleur;
}
```

## 📚 API Reference

### Classe Carousel

#### Méthodes Factory Statiques

- `Carousel::image(string $id, array $images, array $options = []): self`
- `Carousel::card(string $id, array $cards, array $options = []): self`
- `Carousel::testimonial(string $id, array $testimonials, array $options = []): self`
- `Carousel::gallery(string $id, array $images, array $options = []): self`

#### Méthodes d'Instance

- `addItem(CarouselItem|array $item): self` - Ajouter un seul item
- `addItems(array $items): self` - Ajouter plusieurs items
- `setOptions(array $options): self` - Définir les options du carousel
- `getOption(string $key, mixed $default = null): mixed` - Obtenir une valeur d'option
- `render(): string` - Afficher le carousel complet (HTML + CSS + JS)
- `renderHtml(): string` - Afficher uniquement le HTML
- `renderCss(): string` - Afficher uniquement le CSS
- `renderJs(): string` - Afficher uniquement le JavaScript
- `getId(): string` - Obtenir l'ID du carousel
- `getType(): string` - Obtenir le type de carousel
- `getItems(): array` - Obtenir tous les items
- `getOptions(): array` - Obtenir toutes les options

### Classe CarouselItem

#### Constructeur

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

#### Méthodes Statiques

- `CarouselItem::fromArray(array $data): self` - Créer depuis un tableau

#### Méthodes d'Instance

- `toArray(): array` - Convertir en tableau

## 💡 Exemples

### Exemple 1 : Carousel de Produits

```php
<?php

use JulienLinard\Carousel\Carousel;

$produits = [
    [
        'id' => '1',
        'title' => 'Casque Premium',
        'content' => 'Son de haute qualité avec annulation de bruit',
        'image' => '/images/casque.jpg',
        'link' => '/produits/casque',
    ],
    [
        'id' => '2',
        'title' => 'Souris Sans Fil',
        'content' => 'Design ergonomique avec longue autonomie',
        'image' => '/images/souris.jpg',
        'link' => '/produits/souris',
    ],
    // ... plus de produits
];

$carousel = Carousel::card('produits', $produits, [
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

### Exemple 2 : Carousel Bannière Hero

```php
<?php

use JulienLinard\Carousel\Carousel;

$bannieres = [
    [
        'id' => 'banniere1',
        'title' => 'Bienvenue dans Notre Boutique',
        'content' => 'Découvrez des produits incroyables',
        'image' => '/images/banniere1.jpg',
        'link' => '/boutique',
    ],
    [
        'id' => 'banniere2',
        'title' => 'Soldes d\'Été',
        'content' => 'Jusqu\'à 50% de réduction sur les articles sélectionnés',
        'image' => '/images/banniere2.jpg',
        'link' => '/soldes',
    ],
];

$carousel = Carousel::image('hero', $bannieres, [
    'height' => '600px',
    'autoplay' => true,
    'autoplayInterval' => 5000,
    'transition' => 'fade',
]);

echo $carousel->render();
```

### Exemple 3 : Témoignages Clients

```php
<?php

use JulienLinard\Carousel\Carousel;

$temoignages = [
    [
        'id' => '1',
        'title' => 'Sarah Johnson',
        'content' => 'Le meilleur service que j\'ai jamais connu. Je recommande vivement !',
        'image' => '/avatars/sarah.jpg',
    ],
    [
        'id' => '2',
        'title' => 'Michael Chen',
        'content' => 'Qualité exceptionnelle et livraison rapide. Je commanderai à nouveau !',
        'image' => '/avatars/michael.jpg',
    ],
];

$carousel = Carousel::testimonial('temoignages', $temoignages, [
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

MIT License - Voir le fichier LICENSE pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

## 📧 Support

Pour toute question ou problème, veuillez ouvrir une issue sur GitHub.

## 💝 Soutenir le projet

Si ce bundle vous est utile, envisagez de [devenir un sponsor](https://github.com/sponsors/julien-lin) pour soutenir le développement et la maintenance de ce projet open source.

---

**Développé avec ❤️ par Julien Linard**


# Intégrations CMS

Ce document décrit comment utiliser PHP Carousel avec différents CMS.

## WordPress

### Installation

1. Copiez le dossier `plugins/wordpress/` dans votre répertoire `wp-content/plugins/`
2. Activez le plugin "PHP Carousel" dans l'administration WordPress
3. Assurez-vous que la bibliothèque PHP Carousel est installée via Composer

### Utilisation

#### Shortcode

```php
[php_carousel id="my-carousel" type="image" images="image1.jpg,image2.jpg,image3.jpg"]
```

**Options disponibles :**
- `id` - ID unique du carousel (requis)
- `type` - Type de carousel : `image`, `card`, `testimonial`, `gallery`, `infinite`
- `images` - Liste d'images séparées par des virgules
- `autoplay` - `true` ou `false` (défaut: `true`)
- `autoplay_interval` - Intervalle en millisecondes (défaut: `5000`)
- `loop` - `true` ou `false` (défaut: `true`)
- `show_arrows` - `true` ou `false` (défaut: `true`)
- `show_dots` - `true` ou `false` (défaut: `true`)
- `items_per_slide` - Nombre d'items par slide (défaut: `1`)
- `transition` - Type de transition : `slide`, `fade`, `cube` (défaut: `slide`)
- `theme` - Thème : `auto`, `light`, `dark` (défaut: `auto`)

#### Widget

Le plugin inclut un widget que vous pouvez ajouter dans les zones de widgets :

1. Allez dans **Apparence > Widgets**
2. Ajoutez le widget "PHP Carousel"
3. Configurez le titre, l'ID, le type et les images

#### Utilisation en PHP

```php
<?php
// Dans un template ou un plugin personnalisé
echo do_shortcode('[php_carousel id="gallery" type="gallery" images="img1.jpg,img2.jpg"]');
?>
```

---

## PrestaShop

### Installation

1. Copiez le dossier `plugins/prestashop/` dans votre répertoire `modules/`
2. Renommez-le en `phpcarousel`
3. Installez le module dans **Modules > Module Manager**
4. Assurez-vous que la bibliothèque PHP Carousel est installée via Composer

### Hooks disponibles

#### displayHome

Affiche un carousel de produits en vedette sur la page d'accueil.

Le module récupère automatiquement les produits de la catégorie d'accueil et les affiche dans un carousel.

#### displayProductAdditionalInfo

Affiche un carousel de produits similaires sur la page produit.

### Configuration

1. Allez dans **Modules > Module Manager > PHP Carousel > Configurer**
2. Configurez les paramètres du carousel

### Personnalisation

Pour personnaliser le carousel, modifiez les hooks dans `phpcarousel.php` :

```php
public function hookDisplayHome($params): string
{
    // Votre logique personnalisée
    $items = $this->getCustomProducts();
    
    $carousel = Carousel::productShowcase('home', $items, [
        'itemsPerSlide' => 4,
        'autoplay' => true,
    ]);
    
    return $carousel->render();
}
```

---

## Drupal

### Installation

1. Copiez le dossier `plugins/drupal/` dans votre répertoire `modules/custom/`
2. Renommez-le en `php_carousel`
3. Activez le module dans **Extend > PHP Carousel**
4. Assurez-vous que la bibliothèque PHP Carousel est installée via Composer

### Utilisation

#### Block

1. Allez dans **Structure > Block layout**
2. Ajoutez le block "PHP Carousel" dans la zone souhaitée
3. Configurez le block

#### Filter

Le module fournit un filtre de texte qui permet d'utiliser la syntaxe shortcode :

```
[php_carousel id="my-carousel" type="image" images="img1.jpg,img2.jpg"]
```

**Activation du filtre :**
1. Allez dans **Configuration > Text formats and editors**
2. Éditez le format de texte souhaité
3. Activez le filtre "PHP Carousel"

#### Utilisation en PHP

```php
<?php
// Dans un template ou un module personnalisé
$config = [
    'id' => 'gallery',
    'type' => 'gallery',
    'items' => [
        ['image' => '/sites/default/files/img1.jpg'],
        ['image' => '/sites/default/files/img2.jpg'],
    ],
    'options' => [
        'autoplay' => true,
        'autoplayInterval' => 5000,
    ],
];

echo php_carousel_render_carousel($config);
?>
```

---

## Notes importantes

### Dépendances

Tous les plugins/modules nécessitent que la bibliothèque PHP Carousel soit installée via Composer :

```bash
composer require julien-lin/php-carousel
```

### Autoloader

Les plugins tentent automatiquement de charger la bibliothèque depuis `vendor/autoload.php`. Assurez-vous que le chemin est correct.

### Sécurité

Tous les plugins sanitent les entrées utilisateur et utilisent les fonctions d'échappement appropriées pour chaque CMS.

### Performance

Pour de meilleures performances, utilisez `renderStatic()` pour générer du HTML statique cacheable :

```php
$staticHtml = $carousel->renderStatic();
// Cachez ce HTML
// Ajoutez JavaScript plus tard avec hydrate()
```

---

## Support

Pour toute question ou problème, consultez la [documentation principale](../README.md) ou ouvrez une issue sur GitHub.


# Politique de sécurité

## Signaler une vulnérabilité

Si vous découvrez une vulnérabilité de sécurité, **ne l’ouvrez pas en issue publique**.

- **Email** : [julien.linard.dev@gmail.com](mailto:julien.linard.dev@gmail.com)
- Décrivez le problème, les étapes pour le reproduire et l’impact potentiel.
- Nous nous engageons à répondre sous 72 h et à tenir un échange confidentiel jusqu’à correction (ou décision de non-correction).

## Bonnes pratiques d’utilisation

### Configuration et entrées

- Ne construisez pas de carousel à partir de données utilisateur non validées (IDs, titres, liens, options) sans les valider/sanitiser en amont.
- Utilisez `Carousel::fromConfig()` avec des tableaux contrôlés ; la lib valide et limite (ex. 1000 items max en config).

### Chemins de fichiers (FileAnalytics)

- Passez toujours une **base explicite** quand c’est possible : `new FileAnalytics('carousel-analytics', $basePath)`.
- Ne pas utiliser de chemin utilisateur ou de paramètre non fiable comme `$storagePath` sans `$basePath`.

### A/B testing (cookies / session)

- La variante A/B est stockée en **session** par défaut (clé `carousel_variant_{testId}`). Assurez-vous que les sessions sont correctement configurées (cookie sécurisé, HTTPS en production).

### Cache et en-têtes

- La lib **n’envoie aucun en-tête HTTP**. Cache-Control, ETag et compression (gzip) sont à gérer par votre application ou le serveur. Voir [docs/CACHE_AND_HEADERS.md](docs/CACHE_AND_HEADERS.md).

### Dépendances

- Exécutez `composer audit` régulièrement et avant chaque release pour vérifier les CVE connues.

## Checklist pré-production

Avant de déployer en production, vérifier :

- [ ] **XSS** : Données utilisateur (titres, liens, attributs) passées par la whitelist / sanitization de la lib (CarouselItem, UrlValidator).
- [ ] **Chemins** : FileAnalytics utilisé avec une base explicite (`$basePath`) et pas de chemin utilisateur brut.
- [ ] **A/B** : Session configurée correctement (cookie sécurisé, HTTPS) ; pas de cookie A/B non validé.
- [ ] **CSS** : Thème (themeColors) alimenté par des valeurs validées (hex, rgb, rgba) ; IDs carousel sanitized pour les sélecteurs.
- [ ] **URLs** : Liens carousel validés (pas de `//`, schemes dangereux) ; usage de UrlValidator côté app si entrées utilisateur.
- [ ] **Analytics** : Rate limiting et limites de taille activés par défaut ; chemins de stockage sous base contrôlée.
- [ ] **Cache / headers** : Cache-Control, ETag, compression gérés par l’application ou le serveur (voir [CACHE_AND_HEADERS.md](docs/CACHE_AND_HEADERS.md)).
- [ ] **Tests** : `composer test` et `composer audit` exécutés ; tests de sécurité (SecurityTest) passants.

## Versions supportées

Les correctifs de sécurité sont appliqués aux versions maintenues. Consultez les releases et le CHANGELOG pour les versions supportées.

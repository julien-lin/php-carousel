# Cache et en-têtes HTTP

## Responsabilité de l'application

La librairie **ne envoie jamais d'en-têtes HTTP** (`header()`, `Cache-Control`, `ETag`, etc.). Le HTML, CSS et JS rendus sont retournés sous forme de chaînes ; c’est à **l’application qui sert la page** (contrôleur, middleware, serveur web) de définir les en-têtes de cache et de compression.

Cela permet :
- de garder la librairie découplée du transport HTTP ;
- d’adapter la stratégie de cache selon le contexte (CDN, reverse proxy, environnement).

## En-têtes recommandés pour le carousel

Lorsque vous servez une page contenant un carousel (HTML inline ou endpoint qui renvoie CSS/JS) :

### Cache-Control

Pour du HTML contenant le carousel :
```http
Cache-Control: private, max-age=300
```
(Ex. 5 minutes en privé si le contenu varie selon l’utilisateur.)

Pour un fichier ou endpoint dédié CSS/JS du carousel (contenu stable) :
```http
Cache-Control: public, max-age=3600
```
(Ex. 1 heure, cache public.)

### ETag (optionnel)

Pour éviter de renvoyer le corps si le client a déjà la bonne version :

```php
$content = $carousel->render();
$etag = '"' . md5($content) . '"';
header('ETag: ' . $etag);
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    http_response_code(304);
    exit;
}
echo $content;
```

### Exemple minimal (PHP)

```php
$carousel = Carousel::image('gallery', $images);
$html = $carousel->render();

header('Cache-Control: private, max-age=300');
header('Content-Type: text/html; charset=utf-8');
echo $html;
```

## Compression (gzip)

La librairie **ne compresse pas** les réponses et **n’envoie aucun en-tête** (`Content-Encoding`, etc.). La compression (gzip, brotli) doit être gérée par :

- le **serveur web** (Apache `mod_deflate`, Nginx `gzip on`, etc.) ;
- ou l’**application** (middleware, `ob_start('ob_gzhandler')` avant tout output, ou envoi explicite de `Content-Encoding: gzip` avec le corps compressé).

Exemple PHP (à utiliser avec précaution selon votre stack) :

```php
if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
}
echo $carousel->render();
```

En production, privilégier la compression au niveau serveur ou reverse proxy.

### Références

- [MDN: Cache-Control](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)
- [MDN: ETag](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag)
- [MDN: Content-Encoding](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding)

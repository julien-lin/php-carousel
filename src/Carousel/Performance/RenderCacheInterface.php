<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Performance;

/**
 * Contrat pour un cache persistant des rendus (HTML/CSS/JS).
 *
 * La librairie n'implémente pas de cache persistant par défaut.
 * Vous pouvez injecter une implémentation (fichier, Redis, PSR-6/16)
 * pour mettre en cache les sorties de Carousel::render(), renderCss(), etc.
 *
 * Exemple avec une clé dérivée de l'id du carousel et du type de rendu :
 *   $key = 'carousel_' . $carouselId . '_html';
 *   $cached = $cache->get($key);
 *   if ($cached !== null) return $cached;
 *   $output = $carousel->render();
 *   $cache->set($key, $output, 3600);
 */
interface RenderCacheInterface
{
    /**
     * Récupère une entrée du cache.
     *
     * @param string $key Clé (ex. carousel_xxx_html)
     * @return string|null Contenu ou null si absent/expiré
     */
    public function get(string $key): ?string;

    /**
     * Stocke une entrée dans le cache.
     *
     * @param string $key   Clé
     * @param string $value Contenu (HTML, CSS ou JS)
     * @param int    $ttl   Durée de vie en secondes (ex. 3600)
     */
    public function set(string $key, string $value, int $ttl = 3600): void;

    /**
     * Supprime une entrée (optionnel, pour invalidation).
     */
    public function delete(string $key): void;
}

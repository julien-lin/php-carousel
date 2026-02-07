<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Helper;

/**
 * JavaScript output helper.
 *
 * Minification côté PHP est désactivée pour éviter les risques liés aux regex
 * (cassure de code, injection). Pour la production, minifier le JS au build
 * avec un outil dédié : Vite, Webpack, Rollup, ou Terser (npm).
 *
 * @see https://terser.org/
 * @see https://vitejs.dev/
 */
class JsMinifier
{
    /**
     * Retourne le JavaScript trimé (pas de minification côté PHP).
     *
     * Pour une vraie minification, utiliser un build tool (Vite, Webpack, Terser).
     *
     * @param string $js JavaScript code
     * @return string Code trimé
     */
    public static function minify(string $js): string
    {
        return trim($js);
    }
}


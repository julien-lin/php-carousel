<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Translator;

/**
 * Array-based translator implementation
 */
class ArrayTranslator implements TranslatorInterface
{
    private array $translations = [];
    private string $locale = 'en';
    private string $fallbackLocale = 'en';

    public function __construct(array $translations = [], string $locale = 'en')
    {
        $this->translations = $translations ?: $this->getDefaultTranslations();
        $this->locale = $locale;
    }

    /**
     * Get default translations for common languages
     */
    private function getDefaultTranslations(): array
    {
        return [
            'en' => [
                'loading' => 'Loading carousel',
                'image_unavailable' => 'Image unavailable',
                'previous_slide' => 'Previous slide',
                'next_slide' => 'Next slide',
                'go_to_slide' => 'Go to slide {index}',
                'slide_of' => 'Slide {current} of {total}',
                'carousel' => 'Carousel',
            ],
            'fr' => [
                'loading' => 'Chargement du carousel',
                'image_unavailable' => 'Image non disponible',
                'previous_slide' => 'Slide précédent',
                'next_slide' => 'Slide suivant',
                'go_to_slide' => 'Aller au slide {index}',
                'slide_of' => 'Slide {current} sur {total}',
                'carousel' => 'Carousel',
            ],
            'es' => [
                'loading' => 'Cargando carrusel',
                'image_unavailable' => 'Imagen no disponible',
                'previous_slide' => 'Diapositiva anterior',
                'next_slide' => 'Diapositiva siguiente',
                'go_to_slide' => 'Ir a la diapositiva {index}',
                'slide_of' => 'Diapositiva {current} de {total}',
                'carousel' => 'Carrusel',
            ],
            'de' => [
                'loading' => 'Karussell wird geladen',
                'image_unavailable' => 'Bild nicht verfügbar',
                'previous_slide' => 'Vorherige Folie',
                'next_slide' => 'Nächste Folie',
                'go_to_slide' => 'Zu Folie {index} gehen',
                'slide_of' => 'Folie {current} von {total}',
                'carousel' => 'Karussell',
            ],
            'it' => [
                'loading' => 'Caricamento carosello',
                'image_unavailable' => 'Immagine non disponibile',
                'previous_slide' => 'Slide precedente',
                'next_slide' => 'Slide successiva',
                'go_to_slide' => 'Vai alla slide {index}',
                'slide_of' => 'Slide {current} di {total}',
                'carousel' => 'Carosello',
            ],
            'pt' => [
                'loading' => 'Carregando carrossel',
                'image_unavailable' => 'Imagem não disponível',
                'previous_slide' => 'Slide anterior',
                'next_slide' => 'Próximo slide',
                'go_to_slide' => 'Ir para o slide {index}',
                'slide_of' => 'Slide {current} de {total}',
                'carousel' => 'Carrossel',
            ],
            'nl' => [
                'loading' => 'Carrousel laden',
                'image_unavailable' => 'Afbeelding niet beschikbaar',
                'previous_slide' => 'Vorige dia',
                'next_slide' => 'Volgende dia',
                'go_to_slide' => 'Ga naar dia {index}',
                'slide_of' => 'Dia {current} van {total}',
                'carousel' => 'Carrousel',
            ],
            'pl' => [
                'loading' => 'Ładowanie karuzeli',
                'image_unavailable' => 'Obraz niedostępny',
                'previous_slide' => 'Poprzedni slajd',
                'next_slide' => 'Następny slajd',
                'go_to_slide' => 'Przejdź do slajdu {index}',
                'slide_of' => 'Slajd {current} z {total}',
                'carousel' => 'Karuzela',
            ],
            'ru' => [
                'loading' => 'Загрузка карусели',
                'image_unavailable' => 'Изображение недоступно',
                'previous_slide' => 'Предыдущий слайд',
                'next_slide' => 'Следующий слайд',
                'go_to_slide' => 'Перейти к слайду {index}',
                'slide_of' => 'Слайд {current} из {total}',
                'carousel' => 'Карусель',
            ],
            'ja' => [
                'loading' => 'カルーセルを読み込み中',
                'image_unavailable' => '画像が利用できません',
                'previous_slide' => '前のスライド',
                'next_slide' => '次のスライド',
                'go_to_slide' => 'スライド {index} に移動',
                'slide_of' => 'スライド {current} / {total}',
                'carousel' => 'カルーセル',
            ],
            'zh' => [
                'loading' => '正在加载轮播',
                'image_unavailable' => '图片不可用',
                'previous_slide' => '上一张',
                'next_slide' => '下一张',
                'go_to_slide' => '转到第 {index} 张',
                'slide_of' => '第 {current} 张，共 {total} 张',
                'carousel' => '轮播',
            ],
        ];
    }

    public function translate(string $key, ?string $locale = null, array $params = []): string
    {
        $locale = $locale ?? $this->locale;
        
        // Try requested locale
        $translation = $this->getTranslation($key, $locale);
        
        // Fallback to default locale if not found
        if ($translation === null && $locale !== $this->fallbackLocale) {
            $translation = $this->getTranslation($key, $this->fallbackLocale);
        }
        
        // Fallback to key if still not found
        if ($translation === null) {
            return $key;
        }
        
        // Replace placeholders
        return $this->replacePlaceholders($translation, $params);
    }

    private function getTranslation(string $key, string $locale): ?string
    {
        return $this->translations[$locale][$key] ?? null;
    }

    private function replacePlaceholders(string $text, array $params): string
    {
        foreach ($params as $key => $value) {
            $text = str_replace('{' . $key . '}', (string) $value, $text);
        }
        return $text;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? $this->locale;
        return isset($this->translations[$locale][$key]);
    }

    /**
     * Add custom translations
     * 
     * @param array $translations Translations array [locale => [key => value]]
     * @return void
     */
    public function addTranslations(array $translations): void
    {
        foreach ($translations as $locale => $keys) {
            if (!isset($this->translations[$locale])) {
                $this->translations[$locale] = [];
            }
            $this->translations[$locale] = array_merge($this->translations[$locale], $keys);
        }
    }
}


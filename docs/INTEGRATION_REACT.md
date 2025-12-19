# Intégration React

Ce guide explique comment intégrer PHP Carousel dans une application React.

## Installation

```bash
composer require julien-lin/php-carousel
```

## Utilisation de base

### Composant React simple

```jsx
import { useEffect, useRef } from 'react';

function CarouselComponent({ id, items, options = {} }) {
    const carouselRef = useRef(null);
    
    useEffect(() => {
        if (window.CarouselAPI && carouselRef.current) {
            const instance = window.CarouselAPI.init(id, options);
            
            // Écouter les événements
            instance.on('slideChange', ({ index, previousIndex }) => {
                console.log(`Slide changed from ${previousIndex} to ${index}`);
            });
            
            // Cleanup on unmount
            return () => {
                window.CarouselAPI.destroy(id);
            };
        }
    }, [id, options]);
    
    return <div ref={carouselRef} id={`carousel-${id}`} />;
}
```

## Exemple complet avec contrôle

```jsx
import { useEffect, useRef, useState } from 'react';

function CarouselWithControls({ id, items, options = {} }) {
    const carouselRef = useRef(null);
    const instanceRef = useRef(null);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [totalSlides, setTotalSlides] = useState(0);
    
    useEffect(() => {
        if (window.CarouselAPI && carouselRef.current) {
            const instance = window.CarouselAPI.init(id, options);
            instanceRef.current = instance;
            
            // Mettre à jour l'état lors du changement de slide
            instance.on('slideChange', ({ index }) => {
                setCurrentIndex(index);
            });
            
            // Récupérer le nombre total de slides
            setTotalSlides(instance.getTotalSlides());
            setCurrentIndex(instance.getCurrentIndex());
            
            return () => {
                window.CarouselAPI.destroy(id);
            };
        }
    }, [id, options]);
    
    const handleNext = () => {
        if (instanceRef.current) {
            instanceRef.current.next();
        }
    };
    
    const handlePrev = () => {
        if (instanceRef.current) {
            instanceRef.current.prev();
        }
    };
    
    const handleGoTo = (index) => {
        if (instanceRef.current) {
            instanceRef.current.goTo(index);
        }
    };
    
    return (
        <div>
            <div ref={carouselRef} id={`carousel-${id}`} />
            <div className="carousel-controls">
                <button onClick={handlePrev}>Précédent</button>
                <span>{currentIndex + 1} / {totalSlides}</span>
                <button onClick={handleNext}>Suivant</button>
            </div>
        </div>
    );
}
```

## Hook personnalisé

```jsx
import { useEffect, useRef, useState, useCallback } from 'react';

function useCarousel(id, options = {}) {
    const carouselRef = useRef(null);
    const instanceRef = useRef(null);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [totalSlides, setTotalSlides] = useState(0);
    const [isReady, setIsReady] = useState(false);
    
    useEffect(() => {
        if (window.CarouselAPI && carouselRef.current) {
            const instance = window.CarouselAPI.init(id, options);
            instanceRef.current = instance;
            
            instance.on('slideChange', ({ index }) => {
                setCurrentIndex(index);
            });
            
            setTotalSlides(instance.getTotalSlides());
            setCurrentIndex(instance.getCurrentIndex());
            setIsReady(true);
            
            return () => {
                window.CarouselAPI.destroy(id);
            };
        }
    }, [id, options]);
    
    const next = useCallback(() => {
        instanceRef.current?.next();
    }, []);
    
    const prev = useCallback(() => {
        instanceRef.current?.prev();
    }, []);
    
    const goTo = useCallback((index) => {
        instanceRef.current?.goTo(index);
    }, []);
    
    const startAutoplay = useCallback(() => {
        instanceRef.current?.startAutoplay();
    }, []);
    
    const stopAutoplay = useCallback(() => {
        instanceRef.current?.stopAutoplay();
    }, []);
    
    return {
        carouselRef,
        currentIndex,
        totalSlides,
        isReady,
        next,
        prev,
        goTo,
        startAutoplay,
        stopAutoplay,
    };
}

// Utilisation
function MyCarousel({ id, items }) {
    const {
        carouselRef,
        currentIndex,
        totalSlides,
        isReady,
        next,
        prev,
    } = useCarousel(id);
    
    if (!isReady) return <div>Chargement...</div>;
    
    return (
        <div>
            <div ref={carouselRef} id={`carousel-${id}`} />
            <button onClick={prev}>Précédent</button>
            <span>{currentIndex + 1} / {totalSlides}</span>
            <button onClick={next}>Suivant</button>
        </div>
    );
}
```

## Événements disponibles

```jsx
instance.on('slideChange', ({ index, previousIndex }) => {
    // Slide a changé
});

instance.on('autoplayStart', () => {
    // Autoplay démarré
});

instance.on('autoplayStop', () => {
    // Autoplay arrêté
});

instance.on('destroy', () => {
    // Carousel détruit
});
```

## Notes importantes

1. **Initialisation** : L'API doit être disponible avant l'initialisation. Le carousel PHP génère automatiquement l'API JavaScript.

2. **Cleanup** : Toujours détruire l'instance dans le cleanup de `useEffect` pour éviter les fuites mémoire.

3. **Réinitialisation** : Si l'ID ou les options changent, l'instance est automatiquement recréée.

4. **SSR** : Compatible avec Next.js SSR. Le carousel HTML est rendu côté serveur, l'API JavaScript s'initialise côté client.


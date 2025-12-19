# Intégration Vue.js

Ce guide explique comment intégrer PHP Carousel dans une application Vue.js.

## Installation

```bash
composer require julien-lin/php-carousel
```

## Utilisation de base

### Composant Vue simple

```vue
<template>
    <div ref="carouselRef" :id="`carousel-${id}`"></div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';

export default {
    name: 'CarouselComponent',
    props: {
        id: {
            type: String,
            required: true
        },
        options: {
            type: Object,
            default: () => ({})
        }
    },
    setup(props) {
        const carouselRef = ref(null);
        let instance = null;
        
        onMounted(() => {
            if (window.CarouselAPI && carouselRef.value) {
                instance = window.CarouselAPI.init(props.id, props.options);
                
                // Écouter les événements
                instance.on('slideChange', ({ index, previousIndex }) => {
                    console.log(`Slide changed from ${previousIndex} to ${index}`);
                });
            }
        });
        
        onUnmounted(() => {
            if (instance) {
                window.CarouselAPI.destroy(props.id);
            }
        });
        
        return {
            carouselRef
        };
    }
};
</script>
```

## Exemple complet avec contrôle

```vue
<template>
    <div>
        <div ref="carouselRef" :id="`carousel-${id}`"></div>
        <div class="carousel-controls">
            <button @click="handlePrev">Précédent</button>
            <span>{{ currentIndex + 1 }} / {{ totalSlides }}</span>
            <button @click="handleNext">Suivant</button>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';

export default {
    name: 'CarouselWithControls',
    props: {
        id: {
            type: String,
            required: true
        },
        options: {
            type: Object,
            default: () => ({})
        }
    },
    setup(props) {
        const carouselRef = ref(null);
        const currentIndex = ref(0);
        const totalSlides = ref(0);
        let instance = null;
        
        onMounted(() => {
            if (window.CarouselAPI && carouselRef.value) {
                instance = window.CarouselAPI.init(props.id, props.options);
                
                // Mettre à jour l'état lors du changement de slide
                instance.on('slideChange', ({ index }) => {
                    currentIndex.value = index;
                });
                
                // Récupérer le nombre total de slides
                totalSlides.value = instance.getTotalSlides();
                currentIndex.value = instance.getCurrentIndex();
            }
        });
        
        onUnmounted(() => {
            if (instance) {
                window.CarouselAPI.destroy(props.id);
            }
        });
        
        const handleNext = () => {
            if (instance) {
                instance.next();
            }
        };
        
        const handlePrev = () => {
            if (instance) {
                instance.prev();
            }
        };
        
        const handleGoTo = (index) => {
            if (instance) {
                instance.goTo(index);
            }
        };
        
        return {
            carouselRef,
            currentIndex,
            totalSlides,
            handleNext,
            handlePrev,
            handleGoTo
        };
    }
};
</script>
```

## Composable Vue 3

```vue
<script>
import { ref, onMounted, onUnmounted } from 'vue';

export function useCarousel(id, options = {}) {
    const carouselRef = ref(null);
    const currentIndex = ref(0);
    const totalSlides = ref(0);
    const isReady = ref(false);
    let instance = null;
    
    onMounted(() => {
        if (window.CarouselAPI && carouselRef.value) {
            instance = window.CarouselAPI.init(id, options);
            
            instance.on('slideChange', ({ index }) => {
                currentIndex.value = index;
            });
            
            totalSlides.value = instance.getTotalSlides();
            currentIndex.value = instance.getCurrentIndex();
            isReady.value = true;
        }
    });
    
    onUnmounted(() => {
        if (instance) {
            window.CarouselAPI.destroy(id);
        }
    });
    
    const next = () => {
        instance?.next();
    };
    
    const prev = () => {
        instance?.prev();
    };
    
    const goTo = (index) => {
        instance?.goTo(index);
    };
    
    const startAutoplay = () => {
        instance?.startAutoplay();
    };
    
    const stopAutoplay = () => {
        instance?.stopAutoplay();
    };
    
    return {
        carouselRef,
        currentIndex,
        totalSlides,
        isReady,
        next,
        prev,
        goTo,
        startAutoplay,
        stopAutoplay
    };
}
</script>
```

## Utilisation du composable

```vue
<template>
    <div v-if="isReady">
        <div ref="carouselRef" :id="`carousel-${id}`"></div>
        <button @click="prev">Précédent</button>
        <span>{{ currentIndex + 1 }} / {{ totalSlides }}</span>
        <button @click="next">Suivant</button>
    </div>
    <div v-else>Chargement...</div>
</template>

<script>
import { useCarousel } from './composables/useCarousel';

export default {
    name: 'MyCarousel',
    props: {
        id: {
            type: String,
            required: true
        }
    },
    setup(props) {
        const {
            carouselRef,
            currentIndex,
            totalSlides,
            isReady,
            next,
            prev
        } = useCarousel(props.id);
        
        return {
            carouselRef,
            currentIndex,
            totalSlides,
            isReady,
            next,
            prev
        };
    }
};
</script>
```

## Événements disponibles

```javascript
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

2. **Cleanup** : Toujours détruire l'instance dans `onUnmounted` pour éviter les fuites mémoire.

3. **Réactivité** : Utilisez `ref` pour les valeurs réactives qui changent via les événements du carousel.

4. **SSR** : Compatible avec Nuxt.js SSR. Le carousel HTML est rendu côté serveur, l'API JavaScript s'initialise côté client.


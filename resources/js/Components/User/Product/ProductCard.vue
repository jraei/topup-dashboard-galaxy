
<script setup>
import { computed, ref, onMounted } from "vue";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const productName = computed(() => props.product.nama || "Unnamed Product");
const developer = computed(
    () => props.product.developer || "Unknown Developer"
);
const thumbnail = computed(() => {
    if (props.product.thumbnail && props.product.thumbnail.startsWith("http")) {
        return props.product.thumbnail;
    } else if (props.product.thumbnail) {
        return `/storage/${props.product.thumbnail}`;
    }
    return "/img/default-product.png";
});

// Planet system
const planets = [
    {
        name: 'jupiter',
        elements: {
            base: 'bg-gradient-to-br from-amber-400 to-orange-600',
            feature: 'bg-red-500 opacity-80',
            pattern: 'bg-gradient-radial from-amber-300 to-transparent',
        }
    },
    {
        name: 'mars',
        elements: {
            base: 'bg-gradient-to-br from-red-600 to-red-800',
            feature: 'bg-gray-200 opacity-70',
            pattern: 'bg-gradient-radial from-red-500 to-transparent',
        }
    },
    {
        name: 'saturn',
        elements: {
            base: 'bg-gradient-to-br from-yellow-300 to-amber-500',
            feature: 'bg-yellow-200 opacity-60',
            ring: true,
            pattern: 'bg-gradient-radial from-yellow-400 to-transparent',
        }
    },
    {
        name: 'neptune',
        elements: {
            base: 'bg-gradient-to-br from-blue-500 to-indigo-700',
            feature: 'bg-blue-300 opacity-70',
            pattern: 'bg-gradient-radial from-blue-400 to-transparent',
        }
    },
    {
        name: 'venus',
        elements: {
            base: 'bg-gradient-to-br from-yellow-200 to-orange-300',
            feature: 'bg-yellow-100 opacity-70',
            pattern: 'bg-gradient-conic from-yellow-300 via-orange-300 to-yellow-300',
        }
    },
];

const selectedPlanet = ref(null);

onMounted(() => {
    // Randomly select a planet
    const randomIndex = Math.floor(Math.random() * planets.length);
    selectedPlanet.value = planets[randomIndex];
});

// Generate a unique ID for each card's animations
const cardId = ref(`card-${Math.random().toString(36).substring(2, 9)}`);

// Handle prefers-reduced-motion
const prefersReducedMotion = ref(false);
onMounted(() => {
    // Check for reduced motion preference
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        prefersReducedMotion.value = true;
    }
});

// Create dynamic class names based on the cardId
const cometClassName = computed(() => `animate-comet-${cardId.value}`);
</script>

<template>
    <div
        class="relative overflow-hidden transition-all duration-300 ease-out border rounded-lg bg-gradient-to-br from-secondary/10 to-content_background border-primary/20 group hover:border-primary hover:border-2 hover:shadow-glow-primary"
    >
        <!-- Enhanced Cosmic Comet -->
        <div
            class="absolute inset-0 overflow-hidden pointer-events-none opacity-30 group-hover:opacity-60"
        >
            <!-- Comet with enhanced visuals -->
            <div
                :class="[
                    'absolute w-20 h-1 rounded-full z-10',
                    'bg-primary',
                    prefersReducedMotion ? '' : cometClassName,
                    'shadow-glow-primary'
                ]"
                style="top: 20%; right: -20%;"
            >
                <!-- Ion trail layer -->
                <div class="absolute inset-0 bg-primary blur-sm opacity-70"></div>
                
                <!-- Dust particle layer -->
                <div class="absolute h-2 w-16 -left-4 top-0 bg-secondary opacity-40 blur-md rounded-full"></div>
                
                <!-- Glow effect -->
                <div class="absolute h-3 w-10 -left-2 -top-1 bg-primary/20 blur-lg rounded-full"></div>
            </div>

            <!-- Subtle stars -->
            <div class="absolute inset-0 stars">
                <div
                    class="star animate-twinkle bg-primary rounded-full absolute"
                    style="top: 20%; left: 80%; width: 1px; height: 1px"
                ></div>
                <div
                    class="star animate-twinkle bg-primary rounded-full absolute"
                    style="
                        top: 60%;
                        left: 30%;
                        width: 2px;
                        height: 2px;
                        animation-delay: 1s;
                    "
                ></div>
                <div
                    class="star animate-twinkle bg-primary rounded-full absolute"
                    style="
                        top: 80%;
                        left: 70%;
                        width: 1px;
                        height: 1px;
                        animation-delay: 0.5s;
                    "
                ></div>
            </div>
        </div>

        <!-- Planetary decoration (positioned at bottom right, partially visible) -->
        <div 
            v-if="selectedPlanet"
            :class="[
                'absolute bottom-0 right-0 rounded-full z-5 overflow-hidden',
                'w-24 h-24 -mb-12 -mr-12 md:-mr-6',
                prefersReducedMotion ? '' : 'animate-planet-rotate',
                selectedPlanet.elements.base
            ]"
        >
            <!-- Planet surface pattern -->
            <div 
                :class="[
                    'absolute inset-0',
                    selectedPlanet.elements.pattern
                ]"
            ></div>
            
            <!-- Planet features (spots, storms, etc) -->
            <div 
                :class="[
                    'absolute rounded-full w-6 h-6',
                    selectedPlanet.elements.feature
                ]"
                style="top: 25%; left: 35%;"
            ></div>
            
            <!-- Saturn rings (conditionally rendered) -->
            <div 
                v-if="selectedPlanet.name === 'saturn'"
                class="absolute w-36 h-4 bg-yellow-200/50 rounded-full top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rotate-12"
            ></div>
        </div>

        <!-- Card Body -->
        <div class="flex p-1 md:p-2 h-[85%]">
            <!-- Product Thumbnail -->
            <div class="flex items-center justify-center w-2/5 p-2 md:p-3">
                <div
                    class="w-16 h-16 overflow-hidden transition-transform duration-300 ease-out border rounded-lg md:w-24 md:h-24 border-primary/20 group-hover:scale-105"
                >
                    <img
                        :src="thumbnail"
                        :alt="productName"
                        loading="lazy"
                        class="object-cover w-full h-full"
                    />
                </div>
            </div>

            <!-- Product Info -->
            <div class="flex flex-col justify-center w-3/5 pl-3 md:pl-4 z-20">
                <h3
                    class="mb-1 text-xs font-bold md:text-lg text-primary-text line-clamp-2"
                >
                    {{ productName }}
                </h3>
                <p class="text-xs md:text-sm text-primary-text/80">
                    {{ developer }}
                </p>
            </div>
        </div>

        <!-- Card Footer -->
        <div
            class="border-t border-primary/20 bg-primary/30 py-2 px-3 h-[15%] flex items-center justify-end z-20 relative"
        ></div>
    </div>
    
    <!-- Dynamic style for unique card animations -->
    <style scoped>
    @keyframes twinkle {
        0%,
        100% {
            opacity: 0.2;
            transform: scale(0.8);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.2);
        }
    }

    /* Custom comet animation keyframe - dynamically generated for each card instance */
    @keyframes comet-flight {
        0% {
            transform: translateX(0) translateY(0);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        60% {
            transform: translateX(-250%) translateY(150%) scale(0.9);
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateX(-300%) translateY(300%) scale(0.7);
            opacity: 0;
        }
    }

    /* Planet rotation animation */
    @keyframes planet-rotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .animate-twinkle {
        animation: twinkle 3s ease-in-out infinite;
    }

    /* Use the same animation for all comets */
    .animate-comet-card-* {
        animation: comet-flight 4s linear infinite;
    }

    .animate-planet-rotate {
        animation: planet-rotate 120s linear infinite;
    }

    /* Support for reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .animate-twinkle,
        .animate-comet-card-*,
        .animate-planet-rotate {
            animation: none;
        }
    }
    </style>
</template>

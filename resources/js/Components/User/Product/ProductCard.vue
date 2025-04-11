
<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const productName = computed(() => props.product.nama || "Unnamed Product");
const developer = computed(() => props.product.developer || "Unknown Developer");
const thumbnail = computed(() => {
    if (props.product.thumbnail && props.product.thumbnail.startsWith("http")) {
        return props.product.thumbnail;
    } else if (props.product.thumbnail) {
        return `/storage/${props.product.thumbnail}`;
    }
    return "/img/default-product.png";
});

// Random planet generation
const planets = [
    {
        name: "jupiter",
        bgColor: "bg-amber-400",
        detail: "bg-red-500",
        rings: false,
    },
    {
        name: "saturn",
        bgColor: "bg-yellow-300",
        detail: "bg-yellow-400",
        rings: true,
    },
    {
        name: "mars",
        bgColor: "bg-red-600",
        detail: "bg-red-700",
        rings: false,
    },
];

const randomPlanet = planets[Math.floor(Math.random() * planets.length)];
</script>

<template>
    <div
        class="relative overflow-hidden transition-all duration-300 ease-out border rounded-lg bg-gradient-to-br from-secondary/10 to-content_background border-primary/20 group hover:border-primary hover:border-2 hover:shadow-glow-primary p-3 md:p-4"
    >
        <!-- Cosmic Comet -->
        <div
            class="absolute inset-0 overflow-hidden pointer-events-none opacity-30 group-hover:opacity-60"
        >
            <div
                class="absolute w-20 h-1 bg-primary rounded-full comet-trail top-1/4 -left-20 shadow-[0_0_8px_rgba(155,135,245,0.8)] animate-comet-flight"
            >
                <div class="comet-head absolute right-0 w-3 h-3 rounded-full bg-secondary shadow-[0_0_12px_rgba(51,195,240,0.9)]"></div>
                <div class="comet-dust absolute right-1 w-12 h-[2px] bg-white/30 rounded-full"></div>
            </div>

            <!-- Star background -->
            <div class="absolute inset-0 stars">
                <div
                    class="star animate-twinkle"
                    style="top: 20%; left: 80%; width: 1px; height: 1px"
                ></div>
                <div
                    class="star animate-twinkle"
                    style="
                        top: 60%;
                        left: 30%;
                        width: 2px;
                        height: 2px;
                        animation-delay: 1s;
                    "
                ></div>
                <div
                    class="star animate-twinkle"
                    style="
                        top: 80%;
                        left: 70%;
                        width: 1px;
                        height: 1px;
                        animation-delay: 0.5s;
                    "
                ></div>
            </div>

            <!-- Planet -->
            <div class="absolute bottom-0 right-0 planet-container">
                <div :class="[
                    'planet',
                    randomPlanet.bgColor,
                    'rounded-full w-24 h-24 md:w-28 md:h-28 translate-x-1/2 translate-y-1/2 shadow-[0_0_8px_2px_rgba(155,135,245,0.5)] animate-slow-rotate'
                ]">
                    <!-- Planet details -->
                    <div v-if="randomPlanet.name === 'jupiter'" class="absolute w-12 h-3 rounded-full opacity-60 top-1/4 left-1/4 rotate-12 bg-red-500"></div>
                    <div v-if="randomPlanet.name === 'mars'" class="absolute w-8 h-8 rounded-full opacity-30 top-1/3 left-1/3 bg-red-700"></div>
                    
                    <!-- Saturn rings -->
                    <div v-if="randomPlanet.rings" class="absolute inset-0 rings">
                        <div class="absolute top-1/2 left-1/2 w-[140%] h-4 -translate-x-1/2 -translate-y-1/2 bg-yellow-200/40 rounded-full rotate-12 transform-gpu animate-pulse-slow"></div>
                        <div class="absolute top-1/2 left-1/2 w-[130%] h-2 -translate-x-1/2 -translate-y-1/2 bg-yellow-100/30 rounded-full -rotate-6 transform-gpu"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="flex p-0 h-[80%] relative z-10">
            <!-- Product Thumbnail -->
            <div class="flex items-center justify-center w-2/5 p-1 md:p-2">
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
            <div class="flex flex-col justify-center w-3/5 pl-2 md:pl-3">
                <h3
                    class="mb-1 text-sm font-bold md:text-lg text-primary-text line-clamp-2"
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
            class="border-t border-primary/20 bg-primary/30 py-2 px-3 h-[20%] flex items-center justify-end relative z-10"
        ></div>
    </div>
</template>

<style scoped>
.star {
    @apply bg-primary rounded-full absolute;
}

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

@keyframes comet-flight {
    0% {
        transform: translateX(0) translateY(0) rotate(45deg);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateX(300%) translateY(300%) rotate(45deg);
        opacity: 0;
    }
}

@keyframes slow-rotate {
    0% {
        transform: translate(50%, 50%) rotate(0deg);
    }
    100% {
        transform: translate(50%, 50%) rotate(360deg);
    }
}

.animate-twinkle {
    animation: twinkle 3s ease-in-out infinite;
}

.animate-comet-flight {
    animation: comet-flight 3.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

.animate-slow-rotate {
    animation: slow-rotate 90s linear infinite;
}

.animate-pulse-slow {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Support for reduced motion */
@media (prefers-reduced-motion: reduce) {
    .animate-twinkle,
    .animate-comet-flight,
    .animate-slow-rotate,
    .animate-pulse-slow {
        animation: none;
    }
}

/* Mobile optimization - reduce animations for better performance */
@media (max-width: 767px) {
    .comet-trail {
        width: 16px;
    }
    
    .comet-dust {
        width: 10px;
    }
}
</style>

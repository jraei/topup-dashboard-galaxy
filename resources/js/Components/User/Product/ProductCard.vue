
<script setup>
import { computed } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const productName = computed(() => props.product.nama || 'Unnamed Product');
const developer = computed(() => props.product.developer || 'Unknown Developer');
const thumbnail = computed(() => {
    if (props.product.thumbnail && props.product.thumbnail.startsWith('http')) {
        return props.product.thumbnail;
    } else if (props.product.thumbnail) {
        return `/storage/${props.product.thumbnail}`;
    }
    return '/img/default-product.png';
});
</script>

<template>
    <div class="relative overflow-hidden bg-gradient-to-br from-secondary/10 to-content_background rounded-lg border border-primary/20 transition-all duration-300 ease-out group hover:border-primary hover:border-2 hover:shadow-glow-primary">
        <!-- Cosmic Comet -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden opacity-30 group-hover:opacity-60">
            <div class="comet absolute h-1 w-20 bg-primary rounded-full top-1/3 -right-20 shadow-glow-primary animate-comet"></div>
            
            <div class="stars absolute inset-0">
                <div class="star animate-twinkle" style="top: 20%; left: 80%; width: 1px; height: 1px;"></div>
                <div class="star animate-twinkle" style="top: 60%; left: 30%; width: 2px; height: 2px; animation-delay: 1s;"></div>
                <div class="star animate-twinkle" style="top: 80%; left: 70%; width: 1px; height: 1px; animation-delay: 0.5s;"></div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="flex p-3 h-[90%]">
            <!-- Product Thumbnail -->
            <div class="w-2/5 flex items-center justify-center p-2">
                <div class="w-20 h-20 md:w-24 md:h-24 overflow-hidden rounded-lg border border-primary/20 transition-transform duration-300 ease-out group-hover:scale-105">
                    <img :src="thumbnail" :alt="productName" loading="lazy" class="w-full h-full object-cover" />
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="w-3/5 flex flex-col justify-center">
                <h3 class="text-lg font-bold text-primary-text mb-1 line-clamp-2">{{ productName }}</h3>
                <p class="text-sm text-primary-text/80">{{ developer }}</p>
                <p class="text-xs text-primary mt-1">{{ product.kategori?.kategori_name }}</p>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="border-t border-primary/20 py-2 px-3 h-[10%] flex items-center justify-end">
            <span class="text-xs text-primary-text/60">View Details</span>
        </div>
    </div>
</template>

<style scoped>
.star {
    @apply bg-primary rounded-full absolute;
}

@keyframes twinkle {
    0%, 100% { opacity: 0.2; transform: scale(0.8); }
    50% { opacity: 0.8; transform: scale(1.2); }
}

@keyframes comet {
    0% { transform: translateX(0) translateY(0); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateX(-400%) translateY(200%); opacity: 0; }
}

.animate-twinkle {
    animation: twinkle 3s ease-in-out infinite;
}

.animate-comet {
    animation: comet 4s linear infinite;
}

/* Support for reduced motion */
@media (prefers-reduced-motion: reduce) {
    .animate-twinkle, .animate-comet {
        animation: none;
    }
}
</style>

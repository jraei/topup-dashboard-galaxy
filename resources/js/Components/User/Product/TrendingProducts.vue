
<script setup>
import ProductCard from './ProductCard.vue';
import CosmicParticles from '../Flashsale/CosmicParticles.vue';

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <section class="relative overflow-hidden w-full py-8 bg-content_background">
        <div class="absolute inset-0 z-0">
            <CosmicParticles />
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-4">
            <!-- Section Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-primary to-secondary animate-pulse-slow">
                        <span class="text-xl">🔥</span>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-primary drop-shadow-glow">
                        Populer Sekarang
                    </h2>
                </div>
                <p class="text-sm md:text-base text-primary-text/80 ml-14 animate-fade-in">
                    Berikut adalah beberapa produk yang paling populer saat ini.
                </p>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-5">
                <ProductCard 
                    v-for="product in products" 
                    :key="product.id" 
                    :product="product"
                    class="h-[200px] md:h-[220px]"
                />
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.8s ease-out forwards;
}

.drop-shadow-glow {
    filter: drop-shadow(0 0 6px rgba(155, 135, 245, 0.6));
}
</style>

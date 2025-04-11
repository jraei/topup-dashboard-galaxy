
<script setup>
import { ref } from 'vue';
import ProductCard from '@/Components/User/Product/ProductCard.vue';

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    }
});

const isVisible = ref(false);

// Initialize observer when component is mounted
const setupIntersectionObserver = () => {
    if (typeof window !== 'undefined' && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        isVisible.value = true;
                        observer.disconnect();
                    }
                });
            },
            { threshold: 0.1 }
        );

        const target = document.querySelector('#trending-products-section');
        if (target) observer.observe(target);
    } else {
        // Fallback for browsers without IntersectionObserver
        isVisible.value = true;
    }
};

// Limit visible comets for performance
const shouldShowComet = (index) => {
    // On mobile, show comets only on every 3rd card
    if (window.innerWidth < 768) {
        return index % 3 === 0;
    }
    // On desktop, show on every 2nd card
    return index % 2 === 0;
};
</script>

<template>
    <section 
        id="trending-products-section"
        class="relative w-full py-8 bg-content_background overflow-hidden"
        v-if="products && products.length > 0"
    >
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mb-6 flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary/20 mr-3">
                    <span class="text-lg animate-pulse">🔥</span>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-primary-text">
                        Populer Sekarang
                    </h2>
                    <p class="text-sm text-primary-text/80 mt-1">
                        Berikut adalah beberapa produk yang paling populer saat ini.
                    </p>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 lg:gap-6">
                <div
                    v-for="(product, index) in products"
                    :key="product.id"
                    class="min-h-[150px]"
                >
                    <ProductCard :product="product" />
                </div>
            </div>
        </div>
    </section>
</template>

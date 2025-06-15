<script setup>
defineProps({
    products: Array,
    category: Object,
    selectedProducts: Array,
    isLowPowerDevice: Boolean,
    toggleSelection: Function,
});
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div
            v-for="product in products"
            :key="product.id"
            class="relative p-3 overflow-hidden transition-all border rounded-lg cursor-pointer product-card"
            :class="[
                selectedProducts.includes(product.id)
                    ? product.kategori_id === category.id
                        ? 'bg-blue-500/20 border-blue-500/40 text-blue-400'
                        : 'bg-primary/20 border-primary/40 text-white'
                    : product.kategori_id === category.id
                    ? 'bg-blue-500/10 border-blue-500/30'
                    : 'bg-dark-lighter border-gray-700 hover:border-primary/30',
            ]"
            @click="toggleSelection(product.id)"
        >
            <!-- Cosmic glow effect -->
            <div
                v-if="
                    selectedProducts.includes(product.id) && !isLowPowerDevice
                "
                class="absolute inset-0 z-0 opacity-30"
            >
                <div class="cosmic-glow"></div>
            </div>

            <div class="relative z-10 flex">
                <!-- Product Image -->
                <div
                    class="flex items-center justify-center flex-shrink-0 w-16 h-16 mr-3 overflow-hidden border rounded-md bg-dark-sidebar border-gray-700/50"
                >
                    <img
                        v-if="product.thumbnail"
                        :src="'/storage/' + product.thumbnail"
                        :alt="product.nama"
                        class="object-cover w-full h-full"
                        loading="lazy"
                    />
                    <span v-else class="text-2xl">🎮</span>
                </div>

                <!-- Product Info -->
                <div class="flex-grow">
                    <h4 class="font-medium text-white line-clamp-1">
                        {{ product.nama }}
                    </h4>
                    <div class="flex items-center mt-2">
                        <span class="mr-1 text-xs text-gray-400"
                            >Category:</span
                        >
                        <span
                            class="text-xs px-2 py-0.5 rounded-full"
                            :class="[
                                product.kategori_id === category.id
                                    ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30'
                                    : 'bg-dark-sidebar text-gray-300 border border-gray-700',
                            ]"
                        >
                            {{ product.kategori?.kategori_name || "None" }}
                        </span>
                    </div>
                </div>

                <!-- Selection Indicator -->
                <div
                    v-if="selectedProducts.includes(product.id)"
                    class="absolute w-5 h-5 top-2 right-2 text-primary"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

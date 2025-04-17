
<script setup>
import { Rocket } from "lucide-vue-next";

const props = defineProps({
    produk: Object,
    minPrice: {
        type: Number,
        default: 0
    }
});

// Format currency to IDR
const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <div class="p-4 rounded-lg bg-gradient-to-b from-primary/20 to-primary/5 border border-secondary/20 cosmic-card">
        <h3 class="mb-3 text-lg font-bold text-white">Ringkasan Pembelian</h3>
        
        <!-- Product Info -->
        <div class="flex items-center gap-3 mb-4">
            <div class="relative overflow-hidden rounded-lg w-14 h-14 cosmic-thumbnail">
                <img 
                    v-if="produk.thumbnail" 
                    :src="`/storage/${produk.thumbnail}`" 
                    :alt="produk.nama"
                    class="object-cover w-full h-full"
                />
                <!-- Cosmic ray overlay -->
                <div class="absolute inset-0 cosmic-ray-overlay opacity-30"></div>
            </div>
            
            <div class="overflow-hidden">
                <h4 class="font-medium text-white text-md truncate">{{ produk.nama }}</h4>
                <p v-if="produk.developer" class="text-xs text-primary-text/80 truncate">{{ produk.developer }}</p>
            </div>
        </div>
        
        <!-- Pricing Details -->
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-primary-text/80">Harga</span>
                <span class="font-medium text-white">{{ formatCurrency(minPrice) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-primary-text/80">Jumlah</span>
                <span class="font-medium text-white">1</span>
            </div>
            <div class="flex justify-between">
                <span class="text-primary-text/80">Biaya</span>
                <span class="font-medium text-white">{{ formatCurrency(0) }}</span>
            </div>
            
            <div class="my-2 border-t border-secondary/20"></div>
            
            <div class="flex justify-between">
                <span class="font-medium text-primary-text">Total</span>
                <span class="font-bold text-white">{{ formatCurrency(minPrice) }}</span>
            </div>
        </div>
        
        <!-- Checkout Button -->
        <button class="flex items-center justify-center w-full gap-2 px-4 py-2 mt-4 text-white transition-all rounded-md bg-primary hover:bg-primary-hover group">
            <span>Lanjutkan</span>
            <Rocket class="w-4 h-4 transition-transform group-hover:translate-y-[-2px] group-hover:translate-x-[2px]" />
        </button>
    </div>
</template>

<style scoped>
.cosmic-card {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.cosmic-thumbnail {
    transform: perspective(300px) rotateY(5deg);
    transition: transform 0.3s ease;
    will-change: transform;
}

.cosmic-thumbnail:hover {
    transform: perspective(300px) rotateY(0deg) scale(1.05);
}

.cosmic-ray-overlay {
    background: linear-gradient(
        45deg,
        transparent 0%,
        rgba(155, 135, 245, 0.1) 10%,
        rgba(51, 195, 240, 0.2) 20%,
        transparent 30%,
        transparent 70%,
        rgba(155, 135, 245, 0.1) 80%,
        rgba(51, 195, 240, 0.2) 90%,
        transparent 100%
    );
    background-size: 200% 200%;
    animation: ray-animation 4s linear infinite;
}

@keyframes ray-animation {
    0% {
        background-position: 0% 0%;
    }
    100% {
        background-position: 200% 200%;
    }
}
</style>

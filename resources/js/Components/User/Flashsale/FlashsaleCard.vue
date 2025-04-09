
<script setup>
import { computed } from 'vue';

const props = defineProps({
    flashItem: {
        type: Object,
        required: true
    }
});

const layanan = computed(() => props.flashItem.layanan);
const produk = computed(() => layanan.value.produk);

// Calculate discount percentage
const discountPercentage = computed(() => {
    const original = layanan.value.harga_beli_idr;
    const sale = props.flashItem.harga_flashsale;
    
    if (!original || !sale || original <= 0) return 0;
    
    const percentage = Math.round(((original - sale) / original) * 100);
    return percentage;
});

// Format price with IDR
const formatPrice = (price) => {
    if (!price) return '0';
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

// Calculate stock percentage
const stockPercentage = computed(() => {
    // If no stock tracking, return 100%
    if (props.flashItem.stok_tersedia === null) return 100;

    const total = props.flashItem.stok_tersedia + (props.flashItem.stok_terjual || 0);
    
    // Prevent division by zero
    if (total <= 0) return 0;

    const percentage = (props.flashItem.stok_tersedia / total) * 100;
    return Math.max(0, Math.min(100, percentage)); // Clamp between 0-100
});

// Get thumbnail image
const thumbnailImage = computed(() => {
    if (produk.value && produk.value.thumbnails && produk.value.thumbnails.length > 0) {
        return produk.value.thumbnails[0].image_url;
    }
    return null;
});

// Check if stock is low (less than 20%)
const isStockLow = computed(() => {
    return stockPercentage.value < 20;
});
</script>

<template>
    <div class="flashsale-card group">
        <!-- User Limit Badge -->
        <div v-if="flashItem.batas_user" class="absolute top-2 right-2 z-10">
            <div class="bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full border border-primary">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    Max. {{ flashItem.batas_user }}/user
                </span>
            </div>
        </div>

        <!-- Card Body -->
        <div class="relative p-4 bg-dark-lighter rounded-t-lg h-[80%] overflow-hidden group-hover:bg-opacity-90 transition-all duration-300">
            <!-- Game-themed doodle overlay -->
            <div class="absolute right-0 top-0 w-full h-full opacity-15 pointer-events-none">
                <img 
                    v-if="thumbnailImage"
                    :src="thumbnailImage" 
                    class="object-contain w-full h-full mix-blend-screen"
                    alt=""
                    loading="lazy"
                />
            </div>
            
            <!-- Content -->
            <div class="relative z-10">
                <!-- Layanan name -->
                <h3 class="text-primary font-bold mb-1 group-hover:text-glow group-hover:scale-[1.02] transition-all duration-300">
                    {{ layanan.nama_layanan }}
                </h3>
                
                <!-- Produk name -->
                <p class="text-gray-300 text-sm mb-3">{{ produk.nama }}</p>
                
                <!-- Price comparison -->
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-primary font-bold flex items-center">
                        <!-- Coin icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v12" />
                            <path d="M8 12h8" />
                        </svg>
                        {{ formatPrice(flashItem.harga_flashsale) }}
                    </span>
                    
                    <span class="text-gray-400 text-sm line-through">
                        {{ formatPrice(layanan.harga_beli_idr) }}
                    </span>
                    
                    <span v-if="discountPercentage > 0" class="text-xs bg-primary text-white px-1.5 py-0.5 rounded">
                        -{{ discountPercentage }}%
                    </span>
                </div>
            </div>
            
            <!-- Space particle effects -->
            <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                <div v-for="i in 5" :key="i" class="particle"></div>
            </div>
        </div>
        
        <!-- Card Footer -->
        <div class="bg-dark-card p-3 rounded-b-lg border-t border-gray-800">
            <!-- Progress Bar -->
            <div class="relative h-4 bg-gray-800 rounded overflow-hidden mb-1">
                <div 
                    class="h-full transition-all duration-1000 ease-out"
                    :class="[isStockLow ? 'bg-red-500' : 'bg-primary']"
                    :style="{ width: `${stockPercentage}%` }"
                ></div>
            </div>
            
            <!-- Stock text -->
            <p class="text-xs text-gray-300">
                <span v-if="flashItem.stok_tersedia !== null">
                    Tersisa {{ flashItem.stok_tersedia }}
                </span>
                <span v-else>Stok tersedia</span>
            </p>
        </div>
    </div>
</template>

<style scoped>
.flashsale-card {
    @apply relative overflow-hidden transition-all duration-300 hover:shadow-glow-primary;
    height: 240px;
    border: 1px solid rgba(155, 135, 245, 0.1);
    border-radius: 0.5rem;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    transform-origin: center bottom;
}

.flashsale-card:hover {
    transform: translateY(-5px);
}

/* Hexagonal grid pattern overlay */
.flashsale-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(rgba(155, 135, 245, 0.1) 2px, transparent 2px);
    background-size: 16px 16px;
    background-position: -8px -8px;
    opacity: 0.3;
    z-index: 0;
    pointer-events: none;
}

/* Neon glow effect */
.text-glow {
    text-shadow: 0 0 5px rgba(155, 135, 245, 0.7);
}

/* Particles */
.particle {
    position: absolute;
    width: 3px;
    height: 3px;
    background-color: rgba(155, 135, 245, 0.7);
    border-radius: 50%;
    opacity: 0;
    animation: float 3s infinite ease-out;
}

.particle:nth-child(1) { left: 10%; top: 20%; animation-delay: 0s; }
.particle:nth-child(2) { left: 30%; top: 40%; animation-delay: 0.3s; }
.particle:nth-child(3) { left: 50%; top: 10%; animation-delay: 0.6s; }
.particle:nth-child(4) { left: 70%; top: 30%; animation-delay: 0.9s; }
.particle:nth-child(5) { left: 90%; top: 50%; animation-delay: 1.2s; }

@keyframes float {
    0% {
        transform: translateY(0) scale(0);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translateY(-20px) scale(1.5);
        opacity: 0;
    }
}
</style>

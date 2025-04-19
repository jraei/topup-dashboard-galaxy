
<script setup>
import { computed, watch } from 'vue';
import { Rocket } from "lucide-vue-next";

const props = defineProps({
    produk: Object,
    minPrice: {
        type: Number,
        default: 0,
    },
    selectedService: {
        type: Object,
        default: null
    },
    quantity: {
        type: Number,
        default: 1
    }
});

const emit = defineEmits(['checkout']);

// Format currency to IDR
const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

// Calculate item price based on selection
const itemPrice = computed(() => {
    if (!props.selectedService) return props.minPrice;
    
    if (props.selectedService.flashSaleItem && 
        props.selectedService.flashSaleItem.is_active) {
        return props.selectedService.flashSaleItem.harga_flashsale;
    }
    
    return props.selectedService.harga_jual;
});

// Calculate total price
const totalPrice = computed(() => {
    return itemPrice.value * props.quantity;
});

// Get the display name - service name or product name if no service selected
const displayName = computed(() => {
    if (props.selectedService) {
        return props.selectedService.nama_layanan;
    }
    return props.produk.nama;
});

// Get developer name as fallback
const developerName = computed(() => {
    return props.produk.developer || '';
});

const handleCheckout = () => {
    if (!props.selectedService) return;
    emit('checkout');
};

const calculationClass = computed(() => {
    return props.selectedService ? 'price-updated' : '';
});
</script>

<template>
    <div
        class="p-4 border rounded-lg bg-gradient-to-b from-primary/20 to-primary/5 border-secondary/20 cosmic-card"
    >
        <h3 class="mb-3 text-lg font-bold text-white">Ringkasan Pembelian</h3>

        <!-- Product Info -->
        <div class="flex items-center gap-3 mb-4">
            <div
                class="relative overflow-hidden rounded-lg w-14 h-14 cosmic-thumbnail"
            >
                <img
                    v-if="produk.thumbnail"
                    :src="`/storage/${produk.thumbnail}`"
                    :alt="produk.nama"
                    class="object-cover w-full h-full"
                />
                <!-- Cosmic ray overlay -->
                <div
                    class="absolute inset-0 cosmic-ray-overlay opacity-30"
                ></div>
            </div>

            <div class="overflow-hidden">
                <h4 class="font-medium text-white truncate text-md">
                    {{ displayName }}
                </h4>
                <p
                    v-if="developerName"
                    class="text-xs truncate text-primary-text/80"
                >
                    {{ developerName }}
                </p>
            </div>
        </div>

        <!-- Pricing Details -->
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-primary-text/80">Harga</span>
                <span :class="['font-medium text-white', calculationClass]">
                    {{ formatCurrency(itemPrice) }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-primary-text/80">Jumlah</span>
                <span :class="['font-medium text-white', calculationClass]">
                    {{ quantity }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-primary-text/80">Biaya</span>
                <span class="font-medium text-white">{{
                    formatCurrency(0)
                }}</span>
            </div>

            <div class="my-2 border-t border-secondary/20"></div>

            <div class="flex justify-between">
                <span class="font-medium text-primary-text">Total</span>
                <span :class="['font-bold text-white total-price', calculationClass]">
                    {{ formatCurrency(totalPrice) }}
                </span>
            </div>
        </div>

        <!-- Checkout Button -->
        <button
            @click="handleCheckout"
            :disabled="!selectedService"
            class="flex items-center justify-center w-full gap-2 px-4 py-2 mt-4 text-white transition-all rounded-md bg-primary hover:bg-primary-hover group disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span>Pesan Sekarang</span>
            <Rocket
                class="w-4 h-4 transition-transform group-hover:translate-y-[-2px] group-hover:translate-x-[2px]"
            />
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

.price-updated {
    animation: price-update 0.5s ease-out;
    position: relative;
}

.total-price.price-updated::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(
        circle,
        rgba(51, 195, 240, 0.8) 0%,
        transparent 70%
    );
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    opacity: 0;
    z-index: -1;
    animation: price-burst 0.6s ease-out;
}

@keyframes ray-animation {
    0% {
        background-position: 0% 0%;
    }
    100% {
        background-position: 200% 200%;
    }
}

@keyframes price-update {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

@keyframes price-burst {
    0% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 0.8;
    }
    100% {
        transform: translate(-50%, -50%) scale(2);
        opacity: 0;
    }
}
</style>

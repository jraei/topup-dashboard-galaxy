
<script setup>
import { computed } from 'vue';
import { Rocket, Flame } from 'lucide-vue-next';

const props = defineProps({
    service: {
        type: Object,
        required: true
    },
    isSelected: {
        type: Boolean,
        default: false
    },
    isFlashsale: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['select']);

const selectService = () => {
    emit('select', props.service);
};

const hasDiscount = computed(() => {
    if (props.isFlashsale && props.service.flashSaleItem) {
        return props.service.flashSaleItem.harga_flashsale < props.service.harga_jual;
    }
    return false;
});

const discountPercentage = computed(() => {
    if (!hasDiscount.value) return 0;
    
    const regular = props.service.harga_jual;
    const sale = props.service.flashSaleItem.harga_flashsale;
    return Math.round(((regular - sale) / regular) * 100);
});

const servicePrice = computed(() => {
    if (props.isFlashsale && props.service.flashSaleItem) {
        return props.service.flashSaleItem.harga_flashsale;
    }
    return props.service.harga_jual;
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
    <div 
        @click="selectService"
        :class="[
            'cursor-pointer p-3 rounded-lg border transition-all',
            'bg-gradient-to-b from-primary/10 to-primary/5 cosmic-service-card',
            isSelected ? 'ring-2 ring-primary border-primary/50' : 'border-secondary/20 hover:border-secondary/40'
        ]"
    >
        <!-- Service Title -->
        <h4 class="font-semibold text-white cosmic-glow text-md">
            {{ service.nama_layanan }}
        </h4>
        
        <!-- Price Row -->
        <div class="flex items-center justify-between mt-2">
            <div class="flex flex-col">
                <!-- Original price (shown with line-through if discounted) -->
                <span 
                    :class="[
                        'text-sm',
                        hasDiscount ? 'line-through text-primary-text/50' : 'text-primary-text/80'
                    ]"
                >
                    {{ formatCurrency(service.harga_jual) }}
                </span>
                
                <!-- Sale price (if applicable) -->
                <span v-if="hasDiscount" class="font-bold text-white supernova-text">
                    {{ formatCurrency(servicePrice) }}
                </span>
            </div>
            
            <!-- Discount Badge -->
            <div 
                v-if="hasDiscount"
                class="px-2 py-1 text-xs font-bold text-white rounded-full discount-badge bg-primary/90"
            >
                -{{ discountPercentage }}%
            </div>
        </div>
        
        <!-- Footer -->
        <div class="flex items-center mt-2 text-xs text-primary-text/70">
            <Rocket class="w-3 h-3 mr-1 text-secondary" />
            <span>Instant Process</span>
            
            <!-- Rocket trail animation when selected -->
            <div v-if="isSelected" class="ml-1 rocket-trail">
                <div v-for="i in 3" :key="`spark-${i}`" 
                    class="spark" 
                    :style="{animationDelay: `${i * 0.2}s`}">
                </div>
            </div>
        </div>
        
        <!-- Flashsale indicator -->
        <div v-if="isFlashsale" class="absolute top-0 right-0 p-1 -m-1">
            <Flame class="w-4 h-4 text-secondary animate-pulse" />
        </div>
    </div>
</template>

<style scoped>
.cosmic-service-card {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    will-change: transform, box-shadow;
}

.cosmic-service-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(155, 135, 245, 0.15);
}

.cosmic-glow {
    text-shadow: 0 0 8px rgba(155, 135, 245, 0.3);
}

.supernova-text {
    background: linear-gradient(90deg, #33C3F0, #9b87f5);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    position: relative;
}

.discount-badge {
    animation: pulse-badge 2s infinite;
    box-shadow: 0 0 10px rgba(155, 135, 245, 0.5);
}

.rocket-trail {
    display: flex;
    align-items: center;
}

.spark {
    width: 4px;
    height: 4px;
    margin-right: 2px;
    border-radius: 50%;
    background-color: #33C3F0;
    opacity: 0.7;
    animation: spark-fade 1s infinite;
}

@keyframes pulse-badge {
    0%, 100% {
        transform: scale(1);
        opacity: 0.9;
    }
    50% {
        transform: scale(1.05);
        opacity: 1;
    }
}

@keyframes spark-fade {
    0% {
        transform: scale(0.5);
        opacity: 1;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}
</style>

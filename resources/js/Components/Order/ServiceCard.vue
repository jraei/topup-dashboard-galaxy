
<script setup>
import { computed } from "vue";
import { Rocket, Flame } from "lucide-vue-next";

const props = defineProps({
    service: {
        type: Object,
        required: true,
    },
    isSelected: {
        type: Boolean,
        default: false,
    },
    isFlashsale: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(["select"]);

const selectService = () => {
    emit("select", props.service);
};

// Computed values for pricing display
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

// Get the service thumbnail
const thumbnail = computed(() => {
    return props.service.gambar || 
           (props.service.thumbnails?.length ? props.service.thumbnails[0].image_path : null);
});

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
            'cursor-pointer p-4 rounded-lg border transition-all duration-300',
            'bg-gradient-to-b from-primary/10 to-primary/5 cosmic-service-card',
            isSelected
                ? 'ring-2 ring-primary border-primary/50 cosmic-selected'
                : 'border-secondary/20 hover:border-secondary/40 hover:transform hover:-translate-y-1',
        ]"
    >
        <div class="flex items-start space-x-3">
            <!-- Service Thumbnail -->
            <div v-if="thumbnail" class="flex-shrink-0">
                <img 
                    :src="`/storage/${thumbnail}`"
                    :alt="service.nama_layanan"
                    class="w-6 h-6 md:w-8 md:h-8 rounded object-cover"
                />
            </div>

            <!-- Service Info -->
            <div class="flex-1">
                <h4 class="font-semibold text-white cosmic-text-glow text-sm md:text-base">
                    {{ service.nama_layanan }}
                </h4>

                <!-- Price Section -->
                <div class="mt-2 space-y-1">
                    <span
                        :class="[
                            'block text-sm',
                            hasDiscount
                                ? 'line-through text-primary-text/50'
                                : 'text-primary-text/80',
                        ]"
                    >
                        {{ formatCurrency(service.harga_jual) }}
                    </span>

                    <span
                        v-if="hasDiscount"
                        class="block font-bold text-white supernova-text"
                    >
                        {{ formatCurrency(servicePrice) }}
                    </span>
                </div>
            </div>

            <!-- Discount Badge -->
            <div
                v-if="hasDiscount"
                class="absolute top-2 right-2 px-2 py-1 text-xs font-bold text-white rounded-full cosmic-badge bg-primary/90"
            >
                -{{ discountPercentage }}%
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center mt-3 text-xs text-primary-text/70">
            <Rocket class="w-3 h-3 mr-1 text-secondary" />
            <span>Instant Process</span>

            <!-- Rocket trail animation when selected -->
            <div v-if="isSelected" class="ml-1 rocket-trail">
                <div
                    v-for="i in 3"
                    :key="`spark-${i}`"
                    class="spark"
                    :style="{ animationDelay: `${i * 0.2}s` }"
                ></div>
            </div>
        </div>

        <!-- Flashsale indicator -->
        <div v-if="isFlashsale" class="absolute top-2 right-2">
            <Flame class="w-4 h-4 text-secondary animate-pulse" />
        </div>
    </div>
</template>

<style scoped>
.cosmic-service-card {
    position: relative;
    overflow: hidden;
    transform: translateZ(0);
    will-change: transform, box-shadow;
}

.cosmic-selected {
    box-shadow: 0 0 15px rgba(155, 135, 245, 0.2);
}

.cosmic-text-glow {
    text-shadow: 0 0 8px rgba(155, 135, 245, 0.3);
}

.supernova-text {
    background: linear-gradient(90deg, #33c3f0, #9b87f5);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.cosmic-badge {
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
    background-color: #33c3f0;
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

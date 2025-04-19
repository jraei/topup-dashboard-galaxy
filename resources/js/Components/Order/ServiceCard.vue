
<script setup>
import { computed } from "vue";
import { Rocket, Flame, Image } from "lucide-vue-next";

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
    },
});

const emit = defineEmits(["select"]);

const selectService = () => {
    emit("select", props.service);
};

const hasDiscount = computed(() => {
    if (props.isFlashsale && props.service.flashSaleItem) {
        return props.service.flashSaleItem.harga_flashsale < props.service.harga_beli_idr;
    }
    return false;
});

const discountPercentage = computed(() => {
    if (!hasDiscount.value) return 0;
    const regular = props.service.harga_beli_idr;
    const sale = props.service.flashSaleItem.harga_flashsale;
    return Math.round(((regular - sale) / regular) * 100);
});

const servicePrice = computed(() => {
    if (props.isFlashsale && props.service.flashSaleItem) {
        return props.service.flashSaleItem.harga_flashsale;
    }
    return props.service.harga_beli_idr;
});

</script>

<template>
    <div
        @click="selectService"
        :class="[
            'cursor-pointer p-3 rounded-lg border transition-all relative',
            'bg-gradient-to-b from-primary/10 to-primary/5 cosmic-service-card',
            isSelected
                ? 'ring-2 ring-primary border-primary/50 pointer-events-none'
                : 'border-secondary/20 hover:border-secondary/40',
        ]"
    >
        <!-- Thumbnail -->
        <div class="flex items-start space-x-3">
            <div class="relative w-6 h-6 md:w-8 md:h-8 shrink-0">
                <img
                    v-if="service.thumbnail"
                    :src="service.thumbnail"
                    :alt="service.nama_layanan"
                    class="w-full h-full object-cover rounded-lg cosmic-thumbnail"
                />
                <div
                    v-else
                    class="w-full h-full flex items-center justify-center rounded-lg bg-secondary/20"
                >
                    <Image class="w-4 h-4 md:w-5 md:h-5 text-secondary/60" />
                </div>
                <div class="absolute inset-0 cosmic-ring"></div>
            </div>

            <div class="flex-1 min-w-0">
                <!-- Service Title -->
                <h4 class="font-semibold text-white cosmic-glow text-sm md:text-base truncate">
                    {{ service.nama_layanan }}
                </h4>

                <!-- Price Row -->
                <div class="flex items-center justify-between mt-2">
                    <div class="flex flex-col">
                        <span
                            :class="[
                                'text-xs md:text-sm',
                                hasDiscount
                                    ? 'line-through text-primary-text/50'
                                    : 'text-primary-text/80',
                            ]"
                        >
                            {{ servicePrice }}
                        </span>

                        <span
                            v-if="hasDiscount"
                            class="font-bold text-white supernova-text text-xs md:text-sm"
                        >
                            {{ servicePrice }}
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
                        <div
                            v-for="i in 3"
                            :key="`spark-${i}`"
                            class="spark"
                            :style="{ animationDelay: `${i * 0.2}s` }"
                        ></div>
                    </div>
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

.cosmic-service-card:not(.pointer-events-none):hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(155, 135, 245, 0.15);
}

.cosmic-glow {
    text-shadow: 0 0 8px rgba(155, 135, 245, 0.3);
}

.supernova-text {
    background: linear-gradient(90deg, #33c3f0, #9b87f5);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    position: relative;
}

.cosmic-thumbnail {
    position: relative;
    z-index: 1;
}

.cosmic-ring {
    border: 1px solid rgba(51, 195, 240, 0.3);
    border-radius: 0.5rem;
    animation: ring-pulse 3s ease-in-out infinite;
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
    background-color: #33c3f0;
    opacity: 0.7;
    animation: spark-fade 1s infinite;
}

@keyframes ring-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.3;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.5;
    }
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

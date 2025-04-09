
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import FlashsaleCard from './FlashsaleCard.vue';
import FlashsaleHeader from './FlashsaleHeader.vue';
import CosmicParticles from './CosmicParticles.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true
    },
    serverTime: {
        type: String,
        required: true
    }
});

const carouselRef = ref(null);
const isScrolling = ref(false);
const isHovering = ref(false);
const scrollInterval = ref(null);

// Calculate remaining time based on server time sync
const endTime = computed(() => {
    return new Date(props.event.event_end_date).getTime();
});

// Handle automatic scrolling
const startAutoScroll = () => {
    if (scrollInterval.value) return;
    
    scrollInterval.value = setInterval(() => {
        if (isHovering.value || isScrolling.value) return;
        
        if (carouselRef.value) {
            carouselRef.value.scrollLeft += 1;
            
            // Reset scroll position when reaching the end
            if (carouselRef.value.scrollLeft >= carouselRef.value.scrollWidth - carouselRef.value.clientWidth - 10) {
                carouselRef.value.scrollLeft = 0;
            }
        }
    }, 20); // Smoother scrolling with smaller increments
};

// Handle manual scrolling
const handleScroll = () => {
    isScrolling.value = true;
    
    // Clear existing timeout
    if (window.scrollTimeout) {
        clearTimeout(window.scrollTimeout);
    }
    
    // Set a new timeout to detect when scrolling stops
    window.scrollTimeout = setTimeout(() => {
        isScrolling.value = false;
    }, 200);
};

onMounted(() => {
    startAutoScroll();
});

onUnmounted(() => {
    if (scrollInterval.value) {
        clearInterval(scrollInterval.value);
    }
    
    if (window.scrollTimeout) {
        clearTimeout(window.scrollTimeout);
    }
});
</script>

<template>
    <section class="relative overflow-hidden py-8 bg-content_background">
        <!-- Cosmic particles overlay -->
        <CosmicParticles class="absolute inset-0 z-0" />
        
        <div class="max-w-6xl mx-auto px-4 relative z-10">
            <!-- Flash Sale Header -->
            <FlashsaleHeader 
                :event-name="event.event_name"
                :end-time="endTime"
                :server-time="serverTime"
            />
            
            <!-- Cards Carousel -->
            <div 
                ref="carouselRef"
                @scroll="handleScroll"
                @mouseenter="isHovering = true"
                @mouseleave="isHovering = false"
                class="flex overflow-x-auto pb-4 pt-2 snap-x scrollbar-none space-x-4"
            >
                <FlashsaleCard 
                    v-for="item in event.item" 
                    :key="item.id"
                    :flash-item="item"
                    class="flex-none snap-start"
                    :style="{
                        width: 'calc((100% - 3rem) / 4)',
                        minWidth: '260px'
                    }"
                />
                
                <!-- Spacer element to ensure proper scrolling -->
                <div class="flex-none w-4"></div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Hide scrollbar but keep functionality */
.scrollbar-none {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

.scrollbar-none::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

/* Create CRT scanline effect */
section::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        to bottom,
        rgba(255,255,255,0) 50%,
        rgba(0,0,0,0.05) 50%
    );
    background-size: 100% 4px;
    pointer-events: none;
    z-index: 2;
    opacity: 0.05;
}
</style>

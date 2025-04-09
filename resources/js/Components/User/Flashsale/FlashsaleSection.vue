
<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import FlashsaleCard from "./FlashsaleCard.vue";
import FlashsaleHeader from "./FlashsaleHeader.vue";
import CosmicParticles from "./CosmicParticles.vue";

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    serverTime: {
        type: String,
        required: true,
    },
});

const carouselRef = ref(null);
const isScrolling = ref(false);
const isHovering = ref(false);
const scrollInterval = ref(null);
const isMobile = ref(window.innerWidth < 768);

// Calculate remaining time based on server time sync
const endTime = computed(() => {
    return new Date(props.event.event_end_date).getTime();
});

// Calculate scroll speed based on device size
const getScrollSpeed = () => {
    return isMobile.value ? 3.25 : 2.5; // 30% faster on mobile
};

// Handle automatic scrolling with improved logic
const startAutoScroll = () => {
    if (scrollInterval.value) return;

    scrollInterval.value = setInterval(() => {
        if (isHovering.value || isScrolling.value || !carouselRef.value) return;

        carouselRef.value.scrollLeft += getScrollSpeed();

        // Implement infinite loop via virtual DOM replication
        if (
            carouselRef.value.scrollLeft >=
            carouselRef.value.scrollWidth - carouselRef.value.clientWidth - 10
        ) {
            // Reset smoothly
            isScrolling.value = true;
            const firstCardWidth = carouselRef.value.querySelector('.flex-none').offsetWidth;
            
            // Animate scroll to beginning
            const scrollTo = 0;
            const currentPos = carouselRef.value.scrollLeft;
            const startTime = performance.now();
            const duration = 800;
            
            const scrollStep = (timestamp) => {
                const elapsed = timestamp - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easeProgress = 1 - Math.pow(1 - progress, 3); // cubic ease out
                
                carouselRef.value.scrollLeft = currentPos - (currentPos * easeProgress);
                
                if (progress < 1) {
                    window.requestAnimationFrame(scrollStep);
                } else {
                    carouselRef.value.scrollLeft = scrollTo;
                    isScrolling.value = false;
                }
            };
            
            window.requestAnimationFrame(scrollStep);
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

// Handle window resize for responsive behavior
const handleResize = () => {
    isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
    startAutoScroll();
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    if (scrollInterval.value) {
        clearInterval(scrollInterval.value);
    }

    if (window.scrollTimeout) {
        clearTimeout(window.scrollTimeout);
    }
    
    window.removeEventListener('resize', handleResize);
});
</script>

<template>
    <section class="relative py-8 overflow-hidden bg-content_background">
        <!-- Cosmic particles overlay -->
        <CosmicParticles class="absolute inset-0 z-0" />

        <div class="relative z-10 max-w-6xl px-4 mx-auto">
            <!-- Flash Sale Header -->
            <FlashsaleHeader
                :event-name="event.event_name"
                :end-time="endTime"
                :server-time="serverTime"
            />

            <!-- Cards Carousel with improved scroll behavior -->
            <div
                ref="carouselRef"
                @scroll="handleScroll"
                @mouseenter="isHovering = true"
                @mouseleave="isHovering = false"
                class="flex pt-2 pb-4 space-x-4 overflow-x-auto snap-x scrollbar-none"
            >
                <FlashsaleCard
                    v-for="item in event.item"
                    :key="item.id"
                    :flash-item="item"
                    class="flex-none snap-start"
                    :style="{
                        width: 'calc((100% - 3rem) / 4)',
                        minWidth: '290px',
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
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}

.scrollbar-none::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

/* Create CRT scanline effect */
section::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        to bottom,
        rgba(255, 255, 255, 0) 50%,
        rgba(0, 0, 0, 0.05) 50%
    );
    background-size: 100% 4px;
    pointer-events: none;
    z-index: 2;
    opacity: 0.05;
}
</style>

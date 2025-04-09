
<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import FlashsaleCard from "./FlashsaleCard.vue";

const props = defineProps({
    items: {
        type: Array,
        required: true,
    }
});

const carouselRef = ref(null);
const isScrolling = ref(false);
const isHovering = ref(false);
const scrollInterval = ref(null);
const isMobile = ref(window.innerWidth < 768);
const isUserScrolling = ref(false);
const scrollTimeoutId = ref(null);
const scrollAnimationId = ref(null);

// Calculate scroll speed based on device size
const getScrollSpeed = () => {
    return isMobile.value ? 3.25 : 3; // 3px/sec on desktop (adjustable)
};

// Start auto-scroll with improved infinite loop logic
const startAutoScroll = () => {
    if (scrollInterval.value) return;

    scrollInterval.value = setInterval(() => {
        if (isHovering.value || isUserScrolling.value || !carouselRef.value)
            return;

        // Increment scroll position based on speed
        carouselRef.value.scrollLeft += getScrollSpeed();

        // Check if we need to loop back
        const container = carouselRef.value;
        const scrollRight = container.scrollWidth - container.clientWidth;

        // When we reach the end (with 20px buffer), reset seamlessly
        if (container.scrollLeft >= scrollRight - 20) {
            resetScroll();
        }
    }, 16); // ~60fps refresh rate
};

// Handle seamless reset with animation
const resetScroll = () => {
    if (!carouselRef.value) return;

    const container = carouselRef.value;
    isScrolling.value = true;

    // Cancel any existing animation
    if (scrollAnimationId.value) {
        cancelAnimationFrame(scrollAnimationId.value);
    }

    // Animate scroll to beginning with easing
    const startPosition = container.scrollLeft;
    const startTime = performance.now();
    const duration = 800; // ms

    const animateScroll = (timestamp) => {
        const elapsed = timestamp - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // Cubic ease out for smooth deceleration
        const easeProgress = 1 - Math.pow(1 - progress, 3);
        container.scrollLeft = startPosition * (1 - easeProgress);

        if (progress < 1) {
            scrollAnimationId.value = requestAnimationFrame(animateScroll);
        } else {
            container.scrollLeft = 0;
            isScrolling.value = false;
            scrollAnimationId.value = null;
        }
    };

    scrollAnimationId.value = requestAnimationFrame(animateScroll);
};

// Handle manual scrolling with improved detection
const handleScroll = () => {
    if (!carouselRef.value) return;

    isUserScrolling.value = true;

    // Clear previous timeout
    if (scrollTimeoutId.value) {
        clearTimeout(scrollTimeoutId.value);
    }

    // Set new timeout to detect when user stops scrolling
    scrollTimeoutId.value = setTimeout(() => {
        isUserScrolling.value = false;
    }, 250);
};

// Check if we're on mobile/tablet
const handleResize = () => {
    isMobile.value = window.innerWidth < 768;

    // Stop auto-scroll on mobile
    if (isMobile.value && scrollInterval.value) {
        clearInterval(scrollInterval.value);
        scrollInterval.value = null;
    } else if (!isMobile.value && !scrollInterval.value) {
        // Restart on desktop
        startAutoScroll();
    }
};

// Clean up all intervals and animations
const cleanupAnimations = () => {
    if (scrollInterval.value) {
        clearInterval(scrollInterval.value);
        scrollInterval.value = null;
    }

    if (scrollTimeoutId.value) {
        clearTimeout(scrollTimeoutId.value);
        scrollTimeoutId.value = null;
    }

    if (scrollAnimationId.value) {
        cancelAnimationFrame(scrollAnimationId.value);
        scrollAnimationId.value = null;
    }
};

onMounted(() => {
    // Only start auto-scroll on desktop
    if (!isMobile.value) {
        startAutoScroll();
    }
    window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
    cleanupAnimations();
    window.removeEventListener("resize", handleResize);
});
</script>

<template>
    <div class="relative w-full flashsale-carousel" data-debug="flashsale-carousel">
        <!-- Scroll fade indicators -->
        <div 
            class="absolute top-0 bottom-0 left-0 w-16 z-10 pointer-events-none opacity-0 
                   bg-gradient-to-r from-content_background to-transparent
                   transition-opacity duration-300 ease-in-out
                   group-hover:opacity-70"
        ></div>
        <div 
            class="absolute top-0 bottom-0 right-0 w-16 z-10 pointer-events-none opacity-0
                   bg-gradient-to-l from-content_background to-transparent
                   transition-opacity duration-300 ease-in-out
                   group-hover:opacity-70"
        ></div>

        <!-- Main carousel container -->
        <div
            ref="carouselRef"
            @scroll="handleScroll"
            @mouseenter="isHovering = true"
            @mouseleave="isHovering = false"
            @touchstart="isHovering = true"
            @touchend="setTimeout(() => (isHovering = false), 1000)"
            class="flex pt-2 pb-4 space-x-4 overflow-x-auto snap-x scrollbar-hidden
                   will-change-transform transform-gpu"
        >
            <FlashsaleCard
                v-for="item in props.items"
                :key="item.id"
                :flash-item="item"
                class="flex-none snap-start transform-gpu transition-transform duration-300 hover:scale-[1.02]"
                :class="{ 'animate-card-breathing': isHovering }"
                :style="{
                    width: 'calc((100% - 3rem) / 4)',
                    minWidth: '290px',
                }"
            />

            <!-- Spacer element to ensure proper scrolling -->
            <div class="flex-none w-4"></div>
        </div>
    </div>
</template>

<style scoped>
/* Hide scrollbar but keep functionality */
.scrollbar-hidden {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}

.scrollbar-hidden::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

@keyframes card-breathing {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

.animate-card-breathing {
    animation: card-breathing 4s infinite ease-in-out;
}
</style>

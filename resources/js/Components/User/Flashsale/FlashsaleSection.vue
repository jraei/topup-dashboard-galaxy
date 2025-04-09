
<script setup>
import { ref, computed } from "vue";
import FlashsaleHeader from "./FlashsaleHeader.vue";
import CosmicParticles from "./CosmicParticles.vue";
import FlashsaleCarousel from "./FlashsaleCarousel.vue";

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

// Calculate remaining time based on server time sync
const endTime = computed(() => {
    return new Date(props.event.event_end_date).getTime();
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

            <!-- Enhanced Cards Carousel with improved scroll behavior -->
            <div class="relative group">
                <FlashsaleCarousel :items="event.item" />
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Create CRT scanline effect - keeping as it's a complex animation */
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

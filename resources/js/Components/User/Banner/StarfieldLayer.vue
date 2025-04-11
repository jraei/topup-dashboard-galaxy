<template>
    <div class="absolute inset-0 pointer-events-none starfield-container z-5">
        <div
            v-for="(star, index) in stars"
            :key="`star-${index}`"
            class="absolute rounded-full star"
            :style="{
                width: `${star.size}px`,
                height: `${star.size}px`,
                left: `${star.x}%`,
                top: `${star.y}%`,
                backgroundColor: star.color,
                boxShadow: star.glow,
                animationDuration: `${star.duration}s`,
                animationDelay: `${star.delay}s`,
                opacity: star.baseOpacity,
            }"
        ></div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";

const isReducedMotion =
    window?.matchMedia("(prefers-reduced-motion: reduce)")?.matches || false;
const isMobile = computed(() => window?.innerWidth < 768);

const stars = ref([]);

onMounted(() => {
    generateStars();
    window.addEventListener("resize", generateStars);
});

const generateStars = () => {
    const starCount = isMobile.value ? 5 : 10; // Reduced on mobile
    const starData = [];

    // Generate stars with higher density near center
    for (let i = 0; i < starCount; i++) {
        // Create bias toward center for x and y
        const edgeBiasX =
            (Math.random() < 0.5 ? -1 : 1) * Math.pow(Math.random(), 1.5);
        const edgeBiasY =
            (Math.random() < 0.5 ? -1 : 1) * Math.pow(Math.random(), 1.5);

        // Convert to percentage (40% to 60% for more center density)
        const x = 50 + edgeBiasX * 50; // 25% to 75% with center bias
        const y = 50 + edgeBiasY * 50; // 25% to 75% with center bias

        const size = Math.random() * 2 + 2; // 2-4px
        const useSecondary = Math.random() > 0.3;
        const color = useSecondary ? "#33C3F0" : "#ffffff"; // secondary or white
        const glowSize = size * 2;
        const glowColor = useSecondary
            ? "rgba(51, 195, 240, 0.8)"
            : "rgba(255, 255, 255, 0.6)";
        const glow = `0 0 ${glowSize}px ${glowSize / 2}px ${glowColor}`;
        const duration = Math.random() * 2 + 2; // 2-4s
        const delay = Math.random() * 2; // 0-2s

        starData.push({
            size,
            x,
            y,
            color,
            glow,
            duration,
            delay,
            baseOpacity: Math.random() * 0.4 + 0.6, // 0.6-1.0
        });
    }

    stars.value = starData;
};
</script>

<style scoped>
.starfield-container {
    z-index: 5;
}

.star {
    animation: star-pulse infinite alternate ease-in-out;
}

@keyframes star-pulse {
    0% {
        opacity: var(--opacity, 0.3);
        transform: scale(0.95);
    }
    100% {
        opacity: calc(var(--opacity, 0.3) * 2);
        transform: scale(1.1);
    }
}

@media (prefers-reduced-motion: reduce) {
    .star {
        animation: star-twinkle 3s infinite alternate ease-in-out;
    }

    @keyframes star-twinkle {
        0% {
            opacity: var(--opacity, 0.6);
        }
        100% {
            opacity: calc(var(--opacity, 0.6) * 1.3);
        }
    }
}
</style>

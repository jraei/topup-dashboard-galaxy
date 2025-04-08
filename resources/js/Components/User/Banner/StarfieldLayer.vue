
<template>
    <div class="absolute inset-0 pointer-events-none starfield-container z-5">
        <div 
            v-for="(star, index) in stars" 
            :key="`star-${index}`"
            class="star absolute rounded-full"
            :style="{
                width: `${star.size}px`,
                height: `${star.size}px`,
                left: `${star.x}%`,
                top: `${star.y}%`,
                backgroundColor: star.color,
                boxShadow: star.glow,
                animationDuration: `${star.duration}s`,
                animationDelay: `${star.delay}s`,
                opacity: star.baseOpacity
            }"
        ></div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const isReducedMotion = window?.matchMedia('(prefers-reduced-motion: reduce)')?.matches || false;
const isMobile = computed(() => window?.innerWidth < 768);
const isDesktop = computed(() => window?.innerWidth >= 768);

const stars = ref([]);

onMounted(() => {
    generateStars();
    window.addEventListener('resize', generateStars);
});

const generateStars = () => {
    const starCount = isMobile.value ? 5 : 12; // More stars on desktop
    const starData = [];
    
    // Generate stars with full viewport coverage for desktop
    for (let i = 0; i < starCount; i++) {
        // For desktop: -20% to 120% (beyond container)
        // For mobile: 15% to 85% (within container)
        const minPosition = isDesktop.value ? -20 : 15;
        const maxPosition = isDesktop.value ? 120 : 85;
        const range = maxPosition - minPosition;

        // Higher density near edges on desktop
        let x, y;
        
        if (isDesktop.value && Math.random() > 0.5) {
            // 40% chance to place stars near edges
            const nearEdge = Math.random() > 0.5;
            if (nearEdge) {
                // Near horizontal edges (top/bottom)
                x = minPosition + Math.random() * range;
                y = Math.random() > 0.5 ? 
                    minPosition + Math.random() * 25 : // Top edge
                    maxPosition - Math.random() * 25;  // Bottom edge
            } else {
                // Near vertical edges (left/right)
                y = minPosition + Math.random() * range;
                x = Math.random() > 0.5 ? 
                    minPosition + Math.random() * 25 : // Left edge
                    maxPosition - Math.random() * 25;  // Right edge
            }
        } else {
            // Regular distribution
            x = minPosition + Math.random() * range;
            y = minPosition + Math.random() * range;
        }
        
        const size = Math.random() * 2 + (isDesktop.value ? 2.5 : 2); // 2-4px mobile, 2.5-4.5px desktop
        const useSecondary = Math.random() > 0.3;
        const color = useSecondary ? '#33C3F0' : '#ffffff'; // secondary or white
        const glowSize = size * 2;
        const glowColor = useSecondary ? 'rgba(51, 195, 240, 0.8)' : 'rgba(255, 255, 255, 0.6)';
        const glow = `0 0 ${glowSize}px ${glowSize / 2}px ${glowColor}`;
        const duration = Math.random() * 2 + 2; // 2-4s
        const delay = Math.random() * 2; // 0-2s
        
        // Calculate distance from center to adjust opacity
        const distanceFromCenter = Math.sqrt(Math.pow((x - 50), 2) + Math.pow((y - 50), 2));
        const normalizedDistance = Math.min(distanceFromCenter / 70, 1); // 0-1 scale
        
        // Stars near banner edge get higher opacity
        const edgeOpacityBonus = isDesktop.value && normalizedDistance > 0.7 ? 0.2 : 0;
        
        starData.push({
            size,
            x,
            y,
            color,
            glow,
            duration,
            delay,
            baseOpacity: Math.random() * 0.4 + 0.6 + edgeOpacityBonus, // 0.6-1.0 + edge bonus
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
    will-change: opacity, transform;
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
        0% { opacity: var(--opacity, 0.6); }
        100% { opacity: calc(var(--opacity, 0.6) * 1.3); }
    }
}
</style>

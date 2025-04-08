
<template>
    <div class="absolute inset-0 pointer-events-none nebulas-container z-10">
        <div 
            v-for="(nebula, index) in nebulas" 
            :key="`nebula-${index}`"
            class="nebula absolute"
            :style="{
                width: `${nebula.size}px`,
                height: `${nebula.size}px`,
                left: `${nebula.x}%`,
                top: `${nebula.y}%`,
                opacity: nebula.opacity,
                background: `radial-gradient(circle at center, ${nebula.color1} 0%, ${nebula.color2} 70%, transparent 100%)`,
                animationDuration: `${nebula.duration}s`,
                animationDelay: `${nebula.delay}s`,
                transform: `rotate(${nebula.rotation}deg) scale(${nebula.scale})`,
                filter: `blur(${nebula.blur}px)`,
                mixBlendMode: 'overlay'
            }"
        ></div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

// Generate nebulas based on screen size
const isReducedMotion = window?.matchMedia('(prefers-reduced-motion: reduce)')?.matches || false;
const isMobile = computed(() => window?.innerWidth < 768);
const isDesktop = computed(() => window?.innerWidth >= 768);

const nebulas = ref([]);

onMounted(() => {
    generateNebulas();
    window.addEventListener('resize', generateNebulas);
});

const generateNebulas = () => {
    // Enhanced positioning for desktop/tablet
    const count = isMobile.value ? 2 : 4; // Increased nebula count for desktop
    const nebulaData = [];
    
    for (let i = 0; i < count; i++) {
        // Extended positioning beyond banner on desktop
        // For desktop: -30% to 130% (extending beyond container)
        // For mobile: 20-80% (contained within banner)
        const minPosition = isDesktop.value ? -30 : 20;
        const maxPosition = isDesktop.value ? 130 : 80;
        const range = maxPosition - minPosition;
        
        const centerX = minPosition + Math.random() * range;
        const centerY = minPosition + Math.random() * range;
        
        // Increased size for better visibility and extension
        const baseSize = isDesktop.value ? 500 : 300;
        const size = Math.random() * 200 + baseSize; // 300-500px mobile, 500-700px desktop

        // Higher opacity near center, fading toward edges (density gradient)
        const distanceFromCenter = Math.sqrt(Math.pow((centerX - 50), 2) + Math.pow((centerY - 50), 2));
        const maxDistance = Math.sqrt(Math.pow(130, 2) + Math.pow(130, 2));
        const opacityFactor = 1 - (distanceFromCenter / maxDistance);
        
        // Base opacity higher on desktop for visibility when extended
        const baseOpacity = isDesktop.value 
            ? Math.random() * 0.12 + 0.18 // 0.18-0.30 (desktop)
            : Math.random() * 0.10 + 0.15; // 0.15-0.25 (mobile)
            
        const adjustedOpacity = baseOpacity * (0.7 + opacityFactor * 0.3); // Apply density gradient
        
        nebulaData.push({
            size,
            x: centerX, 
            y: centerY,
            opacity: adjustedOpacity,
            color1: 'rgba(155, 135, 245, 0.3)', // primary with higher opacity
            color2: 'rgba(51, 195, 240, 0.2)', // secondary with higher opacity
            duration: Math.random() * 100 + 100, // 100-200s
            delay: Math.random() * 10, // 0-10s
            rotation: Math.random() * 360, // 0-360deg
            scale: Math.random() * 0.5 + 0.8, // 0.8-1.3
            blur: Math.random() * 40 + 30 // 30-70px
        });
    }
    
    nebulas.value = nebulaData;
};
</script>

<style scoped>
.nebulas-container {
    overflow: hidden;
    z-index: 10;
}

.nebula {
    border-radius: 50%;
    will-change: transform;
    animation: nebula-pulse ease-in-out infinite alternate;
    pointer-events: none;
}

@keyframes nebula-pulse {
    0% {
        transform: scale(1) rotate(0deg);
        opacity: var(--opacity, 0.15);
    }
    100% {
        transform: scale(1.05) rotate(5deg);
        opacity: calc(var(--opacity, 0.15) * 1.3);
    }
}

@media (prefers-reduced-motion: reduce) {
    .nebula {
        animation: none;
    }
}
</style>

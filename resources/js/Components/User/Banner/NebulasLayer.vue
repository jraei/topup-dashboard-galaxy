
<template>
    <div class="absolute inset-0 pointer-events-none nebulas-container">
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
                filter: `blur(${nebula.blur}px)`
            }"
        ></div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

// Generate nebulas based on screen size
const isReduced = window?.matchMedia('(prefers-reduced-motion: reduce)')?.matches || false;
const isMobile = computed(() => window?.innerWidth < 768);

const nebulas = ref([]);

onMounted(() => {
    generateNebulas();
    window.addEventListener('resize', generateNebulas);
});

const generateNebulas = () => {
    // Skip for mobile or reduced motion
    if (isMobile.value || isReduced) {
        nebulas.value = [];
        return;
    }
    
    const count = 3; // Keep nebula count low for performance
    const nebulaData = [];
    
    for (let i = 0; i < count; i++) {
        nebulaData.push({
            size: Math.random() * 400 + 200, // 200-600px
            x: Math.random() * 100, // 0-100%
            y: Math.random() * 100, // 0-100%
            opacity: Math.random() * 0.1 + 0.05, // 0.05-0.15
            color1: 'rgba(155, 135, 245, 0.2)', // primary with low opacity
            color2: 'rgba(51, 195, 240, 0.1)', // secondary with lower opacity
            duration: Math.random() * 100 + 100, // 100-200s
            delay: Math.random() * 10, // 0-10s
            rotation: Math.random() * 360, // 0-360deg
            scale: Math.random() * 0.5 + 0.5, // 0.5-1
            blur: Math.random() * 50 + 30 // 30-80px
        });
    }
    
    nebulas.value = nebulaData;
};
</script>

<style scoped>
.nebulas-container {
    overflow: hidden;
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
        opacity: var(--opacity, 0.1);
    }
    100% {
        transform: scale(1.05) rotate(5deg);
        opacity: calc(var(--opacity, 0.1) * 1.3);
    }
}

@media (prefers-reduced-motion: reduce) {
    .nebula {
        animation: none;
    }
}
</style>

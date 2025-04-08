
<template>
    <div class="absolute inset-0 pointer-events-none planets-container">
        <div 
            v-for="(planet, index) in planets" 
            :key="`planet-${index}`"
            class="planet absolute rounded-full"
            :class="[`planet-${index + 1}`]"
            :style="{
                width: `${planet.size}px`,
                height: `${planet.size}px`,
                left: `${planet.left}%`,
                top: `${planet.top}%`,
                opacity: planet.opacity,
                background: planet.gradient,
                boxShadow: planet.glow,
                animationDuration: `${planet.duration}s`,
                animationName: `orbit-${index + 1}`,
                filter: `blur(${planet.blur}px)`
            }"
        >
            <div 
                v-if="!isReducedMotion"
                class="absolute inset-0 planet-ring rounded-full"
                :style="{
                    borderWidth: `${planet.ringWidth}px`,
                    borderColor: planet.ringColor,
                    transform: `rotate(${planet.ringRotation}deg) scale(1.1)`,
                    opacity: planet.ringOpacity
                }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const planets = ref([]);
const isReducedMotion = window?.matchMedia('(prefers-reduced-motion: reduce)')?.matches || false;
const isMobile = computed(() => window?.innerWidth < 768);

onMounted(() => {
    generatePlanets();
    window.addEventListener('resize', generatePlanets);
});

const generatePlanets = () => {
    const planetCount = isMobile.value ? 2 : 4;
    const planetData = [];
    
    // Create dynamic keyframes for each planet
    const styleSheet = document.styleSheets[0];
    
    for (let i = 0; i < planetCount; i++) {
        const size = Math.random() * 60 + 40; // 40-100px
        const left = Math.random() * 80 + 10; // 10-90%
        const top = Math.random() * 70 + 10; // 10-80%
        const duration = Math.random() * 60 + 60; // 60-120s
        const hasRing = Math.random() > 0.5;
        const isPrimary = Math.random() > 0.5;
        const gradient = isPrimary 
            ? 'linear-gradient(135deg, rgba(155, 135, 245, 0.7), rgba(155, 135, 245, 0.2))'
            : 'linear-gradient(135deg, rgba(51, 195, 240, 0.7), rgba(51, 195, 240, 0.2))';
        const glow = isPrimary
            ? '0 0 20px rgba(155, 135, 245, 0.3)'
            : '0 0 20px rgba(51, 195, 240, 0.3)';
            
        // Create unique keyframes for this planet
        const orbitRule = `
            @keyframes orbit-${i + 1} {
                0% { transform: translate(0, 0) rotate(0deg); }
                33% { transform: translate(${Math.random() * 5 - 2.5}%, ${Math.random() * 5 - 2.5}%) rotate(120deg); }
                66% { transform: translate(${Math.random() * 5 - 2.5}%, ${Math.random() * 5 - 2.5}%) rotate(240deg); }
                100% { transform: translate(0, 0) rotate(360deg); }
            }
        `;
        
        try {
            styleSheet.insertRule(orbitRule, styleSheet.cssRules.length);
        } catch(e) {
            console.warn('Could not insert CSS rule:', e);
        }
        
        planetData.push({
            size,
            left,
            top,
            opacity: Math.random() * 0.2 + 0.4, // 0.4-0.6
            gradient,
            glow,
            duration,
            blur: Math.random() * 2, // 0-2px subtle blur
            ringWidth: hasRing ? Math.random() * 3 + 1 : 0, // 1-4px if has ring
            ringColor: isPrimary ? 'rgba(155, 135, 245, 0.3)' : 'rgba(51, 195, 240, 0.3)',
            ringRotation: Math.random() * 180,
            ringOpacity: Math.random() * 0.3 + 0.2 // 0.2-0.5
        });
    }
    
    planets.value = planetData;
};
</script>

<style scoped>
.planets-container {
    overflow: hidden;
}

.planet {
    will-change: transform;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    position: relative;
}

.planet-ring {
    border-style: solid;
    border-radius: 50%;
}

@media (prefers-reduced-motion: reduce) {
    .planet {
        animation: none !important;
    }
}
</style>

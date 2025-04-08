<template>
    <div class="absolute inset-0 pointer-events-none planets-container z-15">
        <!-- Saturn-like planet with distinctive ring system -->
        <div 
            class="saturn-planet absolute rounded-full"
            :style="{
                width: `${saturnSize}px`,
                height: `${saturnSize}px`,
                left: `${saturnLeft}%`,
                top: `${saturnTop}%`,
                opacity: saturnOpacity,
                background: saturnGradient,
                boxShadow: saturnGlow,
                transform: `rotate(${saturnRotation}deg)`,
                filter: `blur(${saturnBlur}px)`
            }"
        >
            <!-- Tilted ring system -->
            <div 
                v-if="!isReducedMotion"
                class="absolute planet-ring saturn-ring"
                :style="{
                    width: `${saturnSize * 2.2}px`,
                    height: `${saturnSize * 0.8}px`,
                    left: `${-saturnSize * 0.6}px`,
                    top: `${saturnSize * 0.1}px`,
                    transform: `rotate(${saturnRingTilt}deg)`,
                    opacity: 0.7
                }"
            ></div>
        </div>
        
        <!-- Regular planets -->
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
                boxShadow: `${planet.glow}, ${planet.craters}`,
                animationDuration: `${planet.duration}s`,
                animationName: `orbit-${index + 1}`,
                filter: `blur(${planet.blur}px)`
            }"
        >
            <!-- Atmospheric halo -->
            <div 
                v-if="!isReducedMotion && planet.hasAtmosphere"
                class="absolute atmosphere-halo"
                :style="{
                    inset: '-15%',
                    borderRadius: '50%',
                    background: `radial-gradient(circle at center, transparent 60%, ${planet.atmosphereColor} 100%)`,
                    opacity: planet.atmosphereOpacity
                }"
            ></div>
            
            <div 
                v-if="!isReducedMotion && planet.hasRing"
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

// Enhanced planets with Saturn-like planet
const planets = ref([]);
const isReducedMotion = window?.matchMedia('(prefers-reduced-motion: reduce)')?.matches || false;
const isMobile = computed(() => window?.innerWidth < 768);
const isDesktop = computed(() => window?.innerWidth >= 768);

// Saturn planet properties - now positioned in top-right quadrant for desktop
const saturnSize = ref(80); // Larger than regular planets
const saturnLeft = ref(75);
const saturnTop = ref(25);
const saturnOpacity = ref(0.7);
const saturnGradient = ref('linear-gradient(135deg, rgba(227, 190, 150, 0.9), rgba(209, 165, 101, 0.3))');
const saturnGlow = ref('0 0 20px rgba(227, 190, 150, 0.4)');
const saturnRotation = ref(15);
const saturnBlur = ref(0.5);
const saturnRingTilt = ref(30); // 30° tilt for the rings

onMounted(() => {
    // Adjust Saturn properties based on screen size
    if (isDesktop.value) {
        // Position Saturn in top-right quadrant on desktop
        saturnSize.value = 100; // Larger on desktop
        saturnLeft.value = 85; // Further right, extending beyond container
        saturnTop.value = 15;  // Higher position
    } else if (isMobile.value) {
        saturnSize.value = 60;
        saturnLeft.value = 70;
        saturnTop.value = 15;
    }
    
    generatePlanets();
    window.addEventListener('resize', generatePlanets);
});

const generatePlanets = () => {
    const planetCount = isMobile.value ? 2 : 4; // More planets on desktop
    const planetData = [];
    
    // Create dynamic keyframes for each planet
    const styleSheet = document.styleSheets[0];
    
    for (let i = 0; i < planetCount; i++) {
        // Desktop: Position planets partly outside container (-20% to 120%)
        // Mobile: Keep planets contained (10% to 70%)
        const minPosition = isDesktop.value ? -20 : 10;
        const maxPosition = isDesktop.value ? 120 : 70;
        const range = maxPosition - minPosition;
        
        const size = isDesktop.value ? 
            (Math.random() * 50 + 40) : // 40-90px desktop
            (Math.random() * 40 + 30);  // 30-70px mobile
            
        const left = minPosition + Math.random() * range;
        const top = minPosition + Math.random() * range;
        
        const duration = Math.random() * 60 + 60; // 60-120s
        const hasRing = Math.random() > 0.6;
        const hasAtmosphere = Math.random() > 0.4;
        const isPrimary = Math.random() > 0.5;
        
        // Gradient fills using primary/secondary colors
        const gradient = isPrimary 
            ? 'linear-gradient(135deg, rgba(155, 135, 245, 0.7), rgba(155, 135, 245, 0.2))'
            : 'linear-gradient(135deg, rgba(51, 195, 240, 0.7), rgba(51, 195, 240, 0.2))';
        
        // Glow effect
        const glow = isPrimary
            ? '0 0 20px rgba(155, 135, 245, 0.3)'
            : '0 0 20px rgba(51, 195, 240, 0.3)';
            
        // Crater textures using box-shadow
        const craterCount = Math.floor(Math.random() * 3) + 2; // 2-4 craters
        let craters = '';
        
        for (let c = 0; c < craterCount; c++) {
            const craterX = Math.random() * size * 0.6 - size * 0.3;
            const craterY = Math.random() * size * 0.6 - size * 0.3;
            const craterSize = Math.random() * 4 + 1;
            const craterOpacity = Math.random() * 0.3 + 0.1;
            craters += `inset ${craterX}px ${craterY}px ${craterSize}px rgba(0,0,0,${craterOpacity})`;
            
            if (c < craterCount - 1) {
                craters += ', ';
            }
        }
            
        // Create unique keyframes for this planet - expanded orbit for desktop
        const orbitRange = isDesktop.value ? 8 : 5; // Larger orbit range on desktop
        const orbitRule = `
            @keyframes orbit-${i + 1} {
                0% { transform: translate(0, 0) rotate(0deg); }
                33% { transform: translate(${Math.random() * orbitRange - orbitRange/2}%, ${Math.random() * orbitRange - orbitRange/2}%) rotate(120deg); }
                66% { transform: translate(${Math.random() * orbitRange - orbitRange/2}%, ${Math.random() * orbitRange - orbitRange/2}%) rotate(240deg); }
                100% { transform: translate(0, 0) rotate(360deg); }
            }
        `;
        
        try {
            styleSheet.insertRule(orbitRule, styleSheet.cssRules.length);
        } catch(e) {
            console.warn('Could not insert CSS rule:', e);
        }
        
        // Atmospheric halo properties
        const atmosphereColor = isPrimary 
            ? 'rgba(155, 135, 245, 0.15)'
            : 'rgba(51, 195, 240, 0.15)';
        
        planetData.push({
            size,
            left,
            top,
            opacity: Math.random() * 0.3 + 0.4, // 0.4-0.7
            gradient,
            glow,
            craters,
            duration,
            blur: Math.random() * 1.5, // 0-1.5px subtle blur
            ringWidth: hasRing ? Math.random() * 3 + 1 : 0, // 1-4px if has ring
            ringColor: isPrimary ? 'rgba(155, 135, 245, 0.3)' : 'rgba(51, 195, 240, 0.3)',
            ringRotation: Math.random() * 180,
            ringOpacity: Math.random() * 0.3 + 0.2, // 0.2-0.5
            hasRing,
            hasAtmosphere,
            atmosphereColor,
            atmosphereOpacity: Math.random() * 0.2 + 0.1 // 0.1-0.3
        });
    }
    
    planets.value = planetData;
};
</script>

<style scoped>
.planets-container {
    overflow: hidden;
    z-index: 15;
    will-change: transform;
}

.planet {
    will-change: transform;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    position: relative;
    transition: transform 0.3s ease;
}

.planet-ring {
    border-style: solid;
    border-radius: 50%;
}

.saturn-planet {
    position: relative;
    will-change: transform;
    animation: saturn-float 120s linear infinite;
}

.saturn-ring {
    position: absolute;
    border-radius: 50%;
    border: none;
    background: linear-gradient(90deg, 
        transparent 10%, 
        rgba(155, 135, 245, 0.3) 20%, 
        rgba(155, 135, 245, 0.3) 30%, 
        rgba(51, 195, 240, 0.2) 40%, 
        rgba(51, 195, 240, 0.2) 60%, 
        rgba(155, 135, 245, 0.3) 70%, 
        rgba(155, 135, 245, 0.3) 80%, 
        transparent 90%
    );
}

@keyframes saturn-float {
    0% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(-2%, 1%) rotate(180deg); }
    100% { transform: translate(0, 0) rotate(360deg); }
}

/* Scale planets by 20% when outside container on desktop */
@media (min-width: 768px) {
    .planet:hover, .saturn-planet:hover {
        transform: scale(1.2);
    }
}

@media (prefers-reduced-motion: reduce) {
    .planet, .saturn-planet {
        animation: none !important;
    }
}
</style>

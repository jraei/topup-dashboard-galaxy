
<template>
    <div class="absolute inset-0 pointer-events-none z-25 comets-container">
        <div 
            v-for="(comet, index) in comets" 
            :key="`comet-${index}`"
            class="comet absolute"
            :class="[`comet-${index + 1}`]"
            :style="{
                width: `${comet.size}px`,
                height: `${comet.size}px`,
                left: comet.leftToRight ? '-10%' : '110%', // Further outside viewport
                top: `${comet.top}%`,
                opacity: 0,
                backgroundColor: '#fff',
                boxShadow: comet.glow,
                filter: 'drop-shadow(0 0 3px rgba(255, 255, 255, 0.8))',
                animationName: comet.leftToRight ? 'comet-left-to-right' : 'comet-right-to-left',
                animationDuration: `${comet.duration}s`,
                animationDelay: `${comet.delay}s`,
                animationTimingFunction: 'ease-in-out',
                animationIterationCount: 'infinite',
            }"
        >
            <!-- Dual tail effect (dust + ion) -->
            <div 
                class="comet-tail absolute"
                :style="{
                    height: '2px',
                    width: `${comet.tailLength}px`,
                    left: comet.leftToRight ? '-100%' : '100%', 
                    top: '50%',
                    transform: 'translateY(-50%)',
                    background: comet.tailGradient,
                }"
            ></div>
            <div 
                class="comet-ion-tail absolute"
                :style="{
                    height: '1px',
                    width: `${comet.tailLength * 0.7}px`,
                    left: comet.leftToRight ? '-70%' : '70%', 
                    top: '50%',
                    transform: 'translateY(-50%) rotate(5deg)',
                    background: comet.ionTailGradient,
                }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const isReducedMotion = window?.matchMedia('(prefers-reduced-motion: reduce)')?.matches || false;
const isMobile = computed(() => window?.innerWidth < 768);
const isDesktop = computed(() => window?.innerWidth >= 768);

// Skip generating comets on mobile devices
const comets = ref([]);

onMounted(() => {
    if ((!isMobile.value || isDesktop.value) && !isReducedMotion) {
        generateComets();
        window.addEventListener('resize', generateComets);
    }
});

const generateComets = () => {
    const cometCount = isDesktop.value ? 4 : 3; // More comets on desktop
    const cometData = [];
    
    // Insert keyframes for comet animations - extended beyond viewport
    const styleSheet = document.styleSheets[0];
    
    try {
        styleSheet.insertRule(`
            @keyframes comet-left-to-right {
                0% { left: -10%; opacity: 0; }
                5% { opacity: 1; }
                95% { opacity: 1; }
                100% { left: 110%; opacity: 0; }
            }
        `, styleSheet.cssRules.length);
        
        styleSheet.insertRule(`
            @keyframes comet-right-to-left {
                0% { left: 110%; opacity: 0; }
                5% { opacity: 1; }
                95% { opacity: 1; }
                100% { left: -10%; opacity: 0; }
            }
        `, styleSheet.cssRules.length);
    } catch (e) {
        console.warn('Could not insert CSS rule:', e);
    }
    
    for (let i = 0; i < cometCount; i++) {
        const size = Math.random() * 3 + 3; // 3-6px core size
        const leftToRight = Math.random() > 0.5; // 50% left-to-right, 50% right-to-left
        const tailLength = Math.random() * 60 + 40; // 40-100px
        
        // Longer duration and varied delays for desktop
        const baseDuration = isDesktop.value ? 20 : 15;
        const baseDelay = isDesktop.value ? 20 : 15;
        
        const duration = Math.random() * 8 + baseDuration; // 15-23s mobile, 20-28s desktop
        const delay = Math.random() * 15 + baseDelay; // 15-30s mobile, 20-35s desktop
        
        const tailGradient = `linear-gradient(${leftToRight ? 'to left' : 'to right'}, rgba(255,255,255,0.8), rgba(255,220,170,0.4), transparent)`;
        const ionTailGradient = `linear-gradient(${leftToRight ? 'to left' : 'to right'}, rgba(155, 135, 245, 0.6), rgba(51, 195, 240, 0.3), transparent)`;
        
        cometData.push({
            size,
            leftToRight,
            // Use full range for vertical positioning
            top: Math.random() * (isDesktop.value ? 100 : 70) + (isDesktop.value ? 0 : 15), // 15-85% mobile, 0-100% desktop
            tailLength,
            duration,
            delay,
            glow: `0 0 ${size * 1.5}px rgba(255,255,255,0.8)`,
            tailGradient,
            ionTailGradient
        });
    }
    
    comets.value = cometData;
};
</script>

<style scoped>
.comets-container {
    overflow: hidden;
}

.comet {
    border-radius: 50%;
    z-index: 25;
}

@media (prefers-reduced-motion: reduce) {
    .comets-container {
        display: none;
    }
}
</style>

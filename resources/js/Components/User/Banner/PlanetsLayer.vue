<template>
    <div class="absolute inset-0 pointer-events-none planets-container z-15">
        <!-- Saturn-like planet with distinctive ring system -->
        <div
            class="absolute rounded-full saturn-planet"
            :style="{
                width: `${saturnSize}px`,
                height: `${saturnSize}px`,
                left: `${saturnLeft}%`,
                top: `${saturnTop}%`,
                opacity: saturnOpacity,
                background: saturnGradient,
                boxShadow: saturnGlow,
                transform: `rotate(${saturnRotation}deg)`,
                filter: `blur(${saturnBlur}px)`,
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
                    opacity: 0.7,
                }"
            ></div>
        </div>

        <!-- Regular planets -->
        <div
            v-for="(planet, index) in planets"
            :key="`planet-${index}`"
            class="absolute rounded-full planet"
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
                filter: `blur(${planet.blur}px)`,
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
                    opacity: planet.atmosphereOpacity,
                }"
            ></div>

            <div
                v-if="!isReducedMotion && planet.hasRing"
                class="absolute inset-0 rounded-full planet-ring"
                :style="{
                    borderWidth: `${planet.ringWidth}px`,
                    borderColor: planet.ringColor,
                    transform: `rotate(${planet.ringRotation}deg) scale(1.1)`,
                    opacity: planet.ringOpacity,
                }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";

// Enhanced planets with Saturn-like planet
const planets = ref([]);
const isReducedMotion =
    window?.matchMedia("(prefers-reduced-motion: reduce)")?.matches || false;
const isMobile = computed(() => window?.innerWidth < 768);

// Saturn planet properties
const saturnSize = ref(90); // Larger than regular planets
const saturnLeft = ref(90);
const saturnTop = ref(25);
const saturnOpacity = ref(0.7);
const saturnGradient = ref(
    "linear-gradient(135deg, rgba(227, 190, 150, 0.9), rgba(209, 165, 101, 0.3))"
);
const saturnGlow = ref("0 0 20px rgba(227, 190, 150, 0.4)");
const saturnRotation = ref(15);
const saturnBlur = ref(0.5);
const saturnRingTilt = ref(30); // 30° tilt for the rings

onMounted(() => {
    // Adjust Saturn properties based on screen size
    if (isMobile.value) {
        saturnSize.value = 60;
        saturnLeft.value = 90;
        saturnTop.value = 15;
    }

    generatePlanets();
    window.addEventListener("resize", generatePlanets);
});

const generatePlanets = () => {
    const planetCount = isMobile.value ? 2 : 3; // 2-3 additional planets
    const planetData = [];

    // Create dynamic keyframes for each planet
    const styleSheet = document.styleSheets[0];

    for (let i = 0; i < planetCount; i++) {
        const size = Math.random() * 40 + 30; // 30-70px
        const left = Math.random() * 15 + 5; // 10-70%
        const top = Math.random() * 50 + 5; // 25-75%
        const duration = Math.random() * 60 + 60; // 60-120s
        const hasRing = Math.random() > 0.6;
        const hasAtmosphere = Math.random() > 0.4;
        const isPrimary = Math.random() > 0.5;

        // Gradient fills using primary/secondary colors
        const gradient = isPrimary
            ? "linear-gradient(135deg, rgba(155, 135, 245, 0.7), rgba(155, 135, 245, 0.2))"
            : "linear-gradient(135deg, rgba(51, 195, 240, 0.7), rgba(51, 195, 240, 0.2))";

        // Glow effect
        const glow = isPrimary
            ? "0 0 20px rgba(155, 135, 245, 0.3)"
            : "0 0 20px rgba(51, 195, 240, 0.3)";

        // Crater textures using box-shadow
        const craterCount = Math.floor(Math.random() * 3) + 2; // 2-4 craters
        let craters = "";

        for (let c = 0; c < craterCount; c++) {
            const craterX = Math.random() * size * 0.6 - size * 0.3;
            const craterY = Math.random() * size * 0.6 - size * 0.3;
            const craterSize = Math.random() * 4 + 1;
            const craterOpacity = Math.random() * 0.3 + 0.1;
            craters += `inset ${craterX}px ${craterY}px ${craterSize}px rgba(0,0,0,${craterOpacity})`;

            if (c < craterCount - 1) {
                craters += ", ";
            }
        }

        // Create unique keyframes for this planet
        const orbitRule = `
            @keyframes orbit-${i + 1} {
                0% { transform: translate(0, 0) rotate(0deg); }
                33% { transform: translate(${Math.random() * 5 - 2.5}%, ${
            Math.random() * 5 - 2.5
        }%) rotate(120deg); }
                66% { transform: translate(${Math.random() * 5 - 2.5}%, ${
            Math.random() * 5 - 2.5
        }%) rotate(240deg); }
                100% { transform: translate(0, 0) rotate(360deg); }
            }
        `;

        try {
            styleSheet.insertRule(orbitRule, styleSheet.cssRules.length);
        } catch (e) {
            console.warn("Could not insert CSS rule:", e);
        }

        // Atmospheric halo properties
        const atmosphereColor = isPrimary
            ? "rgba(155, 135, 245, 0.15)"
            : "rgba(51, 195, 240, 0.15)";

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
            ringColor: isPrimary
                ? "rgba(155, 135, 245, 0.3)"
                : "rgba(51, 195, 240, 0.3)",
            ringRotation: Math.random() * 180,
            ringOpacity: Math.random() * 0.3 + 0.2, // 0.2-0.5
            hasRing,
            hasAtmosphere,
            atmosphereColor,
            atmosphereOpacity: Math.random() * 0.2 + 0.1, // 0.1-0.3
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
    background: linear-gradient(
        90deg,
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
    0% {
        transform: translate(0, 0) rotate(0deg);
    }
    50% {
        transform: translate(-2%, 1%) rotate(180deg);
    }
    100% {
        transform: translate(0, 0) rotate(360deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .planet,
    .saturn-planet {
        animation: none !important;
    }
}
</style>

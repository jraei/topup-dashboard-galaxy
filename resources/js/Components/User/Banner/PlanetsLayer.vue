<template>
    <div class="absolute inset-0 pointer-events-none planets-container z-15">
        <!-- SVG definitions for planets and rings -->
        <svg class="absolute" width="0" height="0">
            <defs>
                <!-- Saturn ring pattern -->
                <linearGradient
                    id="saturn-ring-gradient"
                    x1="0%"
                    y1="0%"
                    x2="100%"
                    y2="0%"
                >
                    <stop offset="10%" stop-color="transparent" />
                    <stop offset="20%" stop-color="rgba(155, 135, 245, 0.3)" />
                    <stop offset="30%" stop-color="rgba(155, 135, 245, 0.3)" />
                    <stop offset="40%" stop-color="rgba(51, 195, 240, 0.2)" />
                    <stop offset="60%" stop-color="rgba(51, 195, 240, 0.2)" />
                    <stop offset="70%" stop-color="rgba(155, 135, 245, 0.3)" />
                    <stop offset="80%" stop-color="rgba(155, 135, 245, 0.3)" />
                    <stop offset="90%" stop-color="transparent" />
                </linearGradient>

                <!-- Planet primary gradient -->
                <radialGradient id="planet-primary-gradient">
                    <stop offset="30%" stop-color="rgba(155, 135, 245, 0.7)" />
                    <stop offset="100%" stop-color="rgba(155, 135, 245, 0.2)" />
                </radialGradient>

                <!-- Planet secondary gradient -->
                <radialGradient id="planet-secondary-gradient">
                    <stop offset="30%" stop-color="rgba(51, 195, 240, 0.7)" />
                    <stop offset="100%" stop-color="rgba(51, 195, 240, 0.2)" />
                </radialGradient>

                <!-- Saturn gradient -->
                <linearGradient id="saturn-gradient">
                    <stop offset="0%" stop-color="rgba(227, 190, 150, 0.9)" />
                    <stop offset="100%" stop-color="rgba(209, 165, 101, 0.3)" />
                </linearGradient>

                <!-- Filter for glow effect -->
                <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur
                        in="SourceGraphic"
                        stdDeviation="5"
                        result="blur"
                    />
                    <feComposite
                        in="SourceGraphic"
                        in2="blur"
                        operator="over"
                    />
                </filter>
            </defs>
        </svg>

        <!-- Saturn-like planet -->
        <div
            class="absolute rounded-full saturn-planet"
            :class="[isMobile ? 'saturn-mobile' : '']"
            :style="styleSaturn"
        >
            <!-- SVG Saturn -->
            <svg
                :width="saturnSize"
                :height="saturnSize"
                viewBox="0 0 100 100"
                class="absolute inset-0"
            >
                <!-- Saturn body -->
                <circle
                    cx="50"
                    cy="50"
                    :r="saturnSize / 2 - 5"
                    fill="url(#saturn-gradient)"
                    :filter="isLowPowerDevice ? '' : 'url(#glow)'"
                />

                <!-- Saturn ring system (when not in reduced motion) -->
                <ellipse
                    v-if="!isReducedMotion"
                    cx="50"
                    cy="50"
                    rx="55"
                    ry="20"
                    fill="none"
                    stroke="url(#saturn-ring-gradient)"
                    stroke-width="4"
                    :transform="`rotate(${saturnRingTilt})`"
                    opacity="0.7"
                />
            </svg>
        </div>

        <!-- Planet instances using SVG -->
        <div
            v-for="(planet, index) in planets"
            :key="`planet-${index}`"
            class="absolute planet"
            :class="[`planet-${index + 1}`, isMobile ? 'planet-mobile' : '']"
            :style="planetStyles[index]"
        >
            <svg
                :width="planet.size"
                :height="planet.size"
                viewBox="0 0 100 100"
                class="absolute inset-0"
            >
                <!-- Planet body -->
                <circle
                    cx="50"
                    cy="50"
                    :r="45"
                    :fill="`url(#planet-${
                        planet.isPrimary ? 'primary' : 'secondary'
                    }-gradient)`"
                    :filter="isLowPowerDevice ? '' : 'url(#glow)'"
                />

                <!-- Planet ring if applicable -->
                <ellipse
                    v-if="!isReducedMotion && planet.hasRing"
                    cx="50"
                    cy="50"
                    rx="55"
                    ry="20"
                    fill="none"
                    :stroke="
                        planet.isPrimary
                            ? 'rgba(155, 135, 245, 0.3)'
                            : 'rgba(51, 195, 240, 0.3)'
                    "
                    stroke-width="2"
                    :transform="`rotate(${planet.ringRotation})`"
                    :opacity="planet.ringOpacity"
                />

                <!-- Create some craters using SVG circles -->
                <circle
                    v-for="(crater, i) in planet.craters"
                    :key="`crater-${i}`"
                    :cx="crater.x"
                    :cy="crater.y"
                    :r="crater.size"
                    :fill="`rgba(0,0,0,${crater.opacity})`"
                />

                <!-- Atmospheric halo if applicable -->
                <circle
                    v-if="!isReducedMotion && planet.hasAtmosphere"
                    cx="50"
                    cy="50"
                    r="58"
                    :fill="planet.atmosphereGradient"
                    opacity="0.2"
                />
            </svg>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";

// Enhanced planets with Saturn-like planet
const planets = ref([]);
const isReducedMotion = computed(() => {
    return (
        window?.matchMedia("(prefers-reduced-motion: reduce)")?.matches || false
    );
});

const isMobile = computed(() => window?.innerWidth < 768);
const isTablet = computed(
    () => window?.innerWidth >= 768 && window?.innerWidth < 1024
);
const isLowPowerDevice = computed(() => {
    return navigator.hardwareConcurrency
        ? navigator.hardwareConcurrency < 4
        : isMobile.value;
});

// Saturn planet properties with responsive sizing
const baseSaturnSize = isMobile.value ? 60 : isTablet.value ? 75 : 90;
const saturnSize = ref(baseSaturnSize);
const saturnLeft = ref(isMobile.value ? 90 : 90);
const saturnTop = ref(isMobile.value ? 15 : 25);
const saturnOpacity = ref(0.7);
const saturnRotation = ref(15);
const saturnRingTilt = ref(30); // 30° tilt for the rings

// Watch for viewport changes
watch(isMobile, (newValue) => {
    if (newValue) {
        saturnSize.value = 60;
        saturnLeft.value = 90;
        saturnTop.value = 15;
    } else if (isTablet.value) {
        saturnSize.value = 75;
        saturnLeft.value = 90;
        saturnTop.value = 20;
    } else {
        saturnSize.value = 90;
        saturnLeft.value = 90;
        saturnTop.value = 25;
    }
});

onMounted(() => {
    generatePlanets();
    generateOrbitKeyframes();
    window.addEventListener("resize", handleResize);
});

const handleResize = () => {
    // Update planet properties based on screen size
    if (isMobile.value) {
        saturnSize.value = 60;
        saturnLeft.value = 90;
        saturnTop.value = 15;
    } else if (isTablet.value) {
        saturnSize.value = 75;
        saturnLeft.value = 90;
        saturnTop.value = 20;
    } else {
        saturnSize.value = 90;
        saturnLeft.value = 90;
        saturnTop.value = 25;
    }

    // Regenerate planets and keyframes for new size
    generatePlanets();
    generateOrbitKeyframes();
};

const generateOrbitKeyframes = () => {
    // Create unique keyframes for each planet's orbit
    const styleSheet = document.styleSheets[0];

    // Remove old keyframes if any
    try {
        const rules = styleSheet.cssRules || styleSheet.rules;
        for (let i = rules.length - 1; i >= 0; i--) {
            if (rules[i].name && rules[i].name.startsWith("orbit-")) {
                styleSheet.deleteRule(i);
            }
        }
    } catch (e) {
        console.warn("Could not clean old CSS rules:", e);
    }

    // Add keyframes for each planet
    planets.value.forEach((planet, i) => {
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
    });
};

const generatePlanets = () => {
    // Determine planet count based on device
    let planetCount = 3;
    if (isReducedMotion.value) {
        planetCount = 1;
    } else if (isMobile.value) {
        planetCount = 2;
    } else if (isTablet.value) {
        planetCount = 3;
    }

    const planetData = [];

    for (let i = 0; i < planetCount; i++) {
        const isPrimary = Math.random() > 0.5;
        const size = isMobile.value
            ? Math.random() * 20 + 15 // 15-35px on mobile
            : Math.random() * 40 + 30; // 30-70px on desktop

        const left = Math.random() * 15 + 5; // 5-20%
        const top = Math.random() * 50 + 5; // 5-55%
        const duration = Math.random() * 60 + 60; // 60-120s
        const hasRing = Math.random() > 0.6;
        const hasAtmosphere = Math.random() > 0.4;

        // Generate craters (simplified data structure for SVG)
        const craterCount = Math.floor(Math.random() * 3) + 2; // 2-4 craters
        const craters = [];

        for (let c = 0; c < craterCount; c++) {
            craters.push({
                x: Math.random() * 70 + 15, // positioned within circle
                y: Math.random() * 70 + 15,
                size: Math.random() * 8 + 2,
                opacity: Math.random() * 0.3 + 0.1,
            });
        }

        planetData.push({
            size,
            left,
            top,
            opacity: Math.random() * 0.3 + 0.4, // 0.4-0.7
            isPrimary,
            duration,
            craters,
            ringRotation: Math.random() * 180,
            ringOpacity: Math.random() * 0.3 + 0.2, // 0.2-0.5
            hasRing,
            hasAtmosphere,
            atmosphereGradient: isPrimary
                ? "radial-gradient(circle at center, transparent 60%, rgba(155, 135, 245, 0.15) 100%)"
                : "radial-gradient(circle at center, transparent 60%, rgba(51, 195, 240, 0.15) 100%)",
        });
    }

    planets.value = planetData;

    const styleSaturn = computed(() => ({
        width: `${saturnSize.value}px`,
        height: `${saturnSize.value}px`,
        left: `${saturnLeft.value}%`,
        top: `${saturnTop.value}%`,
        opacity: saturnOpacity.value,
        transform: `rotate(${saturnRotation.value}deg)`,
        willChange: "transform",
    }));

    const planetStyles = computed(() => {
        return planets.map((planet, index) => ({
            width: planet.size + "px",
            height: planet.size + "px",
            left: planet.left + "%",
            top: planet.top + "%",
            opacity: planet.opacity,
            animationDuration: planet.duration + "s",
            animationName: `orbit-${index + 1}`,
            willChange: "transform",
        }));
    });
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
    transform: translateZ(0); /* Hardware acceleration */
}

.planet-mobile {
    transform: scale(0.5) translateZ(0);
}

.saturn-planet {
    position: relative;
    will-change: transform;
    animation: saturn-float 120s linear infinite;
    transform: translateZ(0); /* Hardware acceleration */
}

.saturn-mobile {
    transform: scale(0.7) translateZ(0);
}

@keyframes saturn-float {
    0% {
        transform: translate(0, 0) rotate(0deg) translateZ(0);
    }
    50% {
        transform: translate(-2%, 1%) rotate(180deg) translateZ(0);
    }
    100% {
        transform: translate(0, 0) rotate(360deg) translateZ(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .planet,
    .saturn-planet {
        animation: none !important;
    }
}
</style>

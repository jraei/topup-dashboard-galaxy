
<template>
    <div
        class="absolute inset-0 overflow-hidden pointer-events-none meteor-container z-0"
    >
        <div
            v-for="(meteor, index) in meteors"
            :key="index"
            class="absolute bg-primary rounded-full meteor"
            :class="[`meteor-${index + 1}`]"
            :style="{
                left: `${meteor.left}px`,
                width: `${meteor.size}px`,
                height: `${meteor.size}px`,
                top: '-20px',
                transform: 'rotate(215deg)',
                opacity: meteor.opacity,
                animationDelay: `${meteor.delay}s`,
                animationDuration: `${meteor.duration}s`,
                boxShadow: `0 0 ${meteor.glow}px 2px ${meteor.glowColor}`,
                willChange: 'transform',
            }"
        >
            <!-- Meteor trail -->
            <div
                class="absolute top-1/2 left-0 h-[2px] transform -translate-y-1/2"
                :style="{
                    width: `${meteor.trailLength}px`,
                    background: `linear-gradient(to right, ${meteor.trailColor}, transparent)`,
                }"
            ></div>

            <!-- Fire particles -->
            <span 
                v-for="particle in meteor.particles" 
                :key="`particle-${particle.id}`"
                class="absolute rounded-full animate-ping"
                :style="{
                    height: `${particle.size}px`,
                    width: `${particle.size}px`,
                    left: `${particle.offsetX}px`,
                    top: `${particle.offsetY}px`,
                    backgroundColor: particle.color,
                    animationDuration: `${particle.duration}s`,
                    opacity: particle.opacity
                }"
            ></span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

// Enhanced meteor positions with more meteors
const positions = [
    { left: 1130, delay: 0.686975, duration: 8 },
    { left: -350, delay: 0.670151, duration: 8 },
    { left: 563, delay: 0.632454, duration: 9 },
    { left: -969, delay: 0.524996, duration: 5 },
    { left: -1153, delay: 0.460272, duration: 8 },
    { left: -560, delay: 0.223791, duration: 6 },
    { left: -1287, delay: 0.406558, duration: 4 },
    { left: 211, delay: 0.475533, duration: 6 },
    { left: -63, delay: 0.394929, duration: 5 },
    { left: -112, delay: 0.78249, duration: 2 },
    { left: 946, delay: 0.353787, duration: 5 },
    { left: 275, delay: 0.309607, duration: 5 },
    { left: 1216, delay: 0.35162, duration: 8 },
    { left: -210, delay: 0.413144, duration: 7 },
    { left: -842, delay: 0.395388, duration: 6 },
    { left: -323, delay: 0.582248, duration: 4 },
    { left: 278, delay: 0.710367, duration: 4 },
    { left: -736, delay: 0.564896, duration: 6 },
    { left: -800, delay: 0.206357, duration: 7 },
    { left: -1118, delay: 0.628613, duration: 9 },
    { left: 1361, delay: 0.529785, duration: 7 },
    { left: -11, delay: 0.64863, duration: 6 },
    { left: -678, delay: 0.701722, duration: 3 },
    { left: -170, delay: 0.366231, duration: 5 },
    { left: 946, delay: 0.521904, duration: 7 },
    { left: 1364, delay: 0.484818, duration: 9 },
    { left: 943, delay: 0.502043, duration: 3 },
    { left: 1296, delay: 0.577243, duration: 7 },
    { left: 1273, delay: 0.273317, duration: 5 },
    { left: -1306, delay: 0.556245, duration: 7 },
];

// Calculate responsive meteor count
const getMeteorCount = () => {
    const isMobile = window.innerWidth < 768;
    return isMobile ? 20 : positions.length;
};

// Generate particle effects for each meteor
const createParticles = (count) => {
    const particles = [];
    for (let i = 0; i < count; i++) {
        particles.push({
            id: i,
            size: Math.random() * 2 + 1, // 1-3px
            offsetX: Math.random() * 10 - 20, // Position behind meteor
            offsetY: Math.random() * 6 - 3, // Slightly offset from center
            color: i % 3 === 0 ? '#FCD34D' : i % 2 === 0 ? '#F97316' : '#EF4444', // Amber, orange, red
            duration: Math.random() * 1.5 + 0.5, // 0.5-2s
            opacity: Math.random() * 0.7 + 0.3 // 0.3-1
        });
    }
    return particles;
};

// Enhanced meteor properties with glow and particles
const meteors = computed(() => {
    const count = typeof window !== 'undefined' ? getMeteorCount() : 20;
    return positions.slice(0, count).map((position, index) => {
        const particleCount = Math.floor(Math.random() * 3) + 3; // 3-5 particles
        const baseSize = Math.random() * 3 + 2; // Base size between 2-5px
        const glowSize = Math.floor(Math.random() * 6 + 6); // Glow radius 6-12px
        const trailLength = Math.floor(Math.random() * 60 + 60); // Trail length 60-120px

        // Use tailwind colors directly
        const glowColor = 'rgba(155, 135, 245, 0.8)'; // Primary color with opacity
        const trailColor = 'rgba(155, 135, 245, 0.8)'; // Primary color with opacity
        
        return {
            ...position,
            size: baseSize,
            trailLength,
            glow: glowSize,
            glowColor,
            trailColor,
            opacity: Math.random() * 0.3 + 0.7, // Higher base opacity 0.7-1
            particles: createParticles(particleCount)
        };
    });
});

// Adjust for responsive behavior on mounted
onMounted(() => {
    window.addEventListener('resize', () => {
        meteors.value = meteors.value;
    });
});
</script>

<style scoped>
.meteor-container {
    transform: translateZ(0); /* Hardware acceleration */
}

.meteor {
    animation: meteor-effect linear infinite;
    animation-fill-mode: forwards;
}

@keyframes meteor-effect {
    0% {
        opacity: 0;
        transform: rotate(215deg) translateX(0);
    }
    5% {
        opacity: 1;
    }
    10% {
        opacity: var(--opacity, 0.8);
        transform: rotate(215deg) translateX(0);
    }
    70% {
        opacity: var(--opacity, 0.8);
    }
    100% {
        opacity: 0;
        transform: rotate(215deg) translateX(-500px);
    }
}

/* Create different trajectories for some meteors */
.meteor-3,
.meteor-7,
.meteor-11,
.meteor-15,
.meteor-19,
.meteor-23,
.meteor-27 {
    animation-name: meteor-effect-curved;
}

@keyframes meteor-effect-curved {
    0% {
        opacity: 0;
        transform: rotate(215deg) translateX(0) translateY(0);
    }
    5% {
        opacity: 1;
    }
    10% {
        opacity: var(--opacity, 0.8);
        transform: rotate(215deg) translateX(-50px) translateY(20px);
    }
    70% {
        opacity: var(--opacity, 0.8);
    }
    100% {
        opacity: 0;
        transform: rotate(215deg) translateX(-500px) translateY(80px);
    }
}
</style>

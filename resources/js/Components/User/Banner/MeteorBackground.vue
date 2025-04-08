
<!-- We need to significantly revise MeteorBackground.vue with new trajectory and effects -->
<template>
    <div
        class="absolute inset-0 overflow-hidden pointer-events-none meteor-container z-20"
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
                top: meteor.top,
                transform: `rotate(${meteor.rotation}deg)`,
                opacity: meteor.opacity,
                animationDelay: `${meteor.delay}s`,
                animationDuration: `${meteor.duration}s`,
                boxShadow: `0 0 ${meteor.glow}px 2px ${meteor.glowColor}`,
                willChange: 'transform',
            }"
        >
            <!-- Enhanced meteor trail -->
            <div
                class="absolute top-1/2 left-0 h-[2px] transform -translate-y-1/2"
                :style="{
                    width: `${meteor.trailLength}px`,
                    background: `linear-gradient(to right, ${meteor.trailColor}, transparent)`,
                }"
            ></div>

            <!-- Enhanced fire particles -->
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
            
            <!-- Debris particles -->
            <span 
                v-for="debris in meteor.debris" 
                :key="`debris-${debris.id}`"
                class="absolute rounded-full"
                :style="{
                    height: `${debris.size}px`,
                    width: `${debris.size}px`,
                    left: `${debris.offsetX}px`,
                    top: `${debris.offsetY}px`,
                    backgroundColor: debris.color,
                    opacity: debris.opacity,
                    transform: `translate(${debris.translateX}px, ${debris.translateY}px)`,
                    animation: `debris-float ${debris.duration}s linear forwards`,
                }"
            ></span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

// Enhanced meteor positions with varied entry points
// All meteors now come from the top area of the viewport
const positions = [
    // Top edge entries with downward angle
    { left: '20%', top: '-20px', rotation: 30, delay: 0.686975, duration: 8 },
    { left: '30%', top: '-20px', rotation: 30, delay: 0.670151, duration: 8 },
    { left: '40%', top: '-20px', rotation: 30, delay: 0.632454, duration: 9 },
    { left: '50%', top: '-20px', rotation: 30, delay: 0.524996, duration: 5 },
    { left: '60%', top: '-20px', rotation: 30, delay: 0.460272, duration: 8 },
    { left: '70%', top: '-20px', rotation: 30, delay: 0.223791, duration: 6 },
    { left: '80%', top: '-20px', rotation: 30, delay: 0.406558, duration: 4 },
    { left: '10%', top: '-20px', rotation: 30, delay: 0.475533, duration: 6 },
    { left: '25%', top: '-20px', rotation: 30, delay: 0.394929, duration: 5 },
    { left: '35%', top: '-20px', rotation: 30, delay: 0.78249, duration: 2 },
    { left: '45%', top: '-20px', rotation: 30, delay: 0.353787, duration: 5 },
    { left: '55%', top: '-20px', rotation: 30, delay: 0.309607, duration: 5 },
    { left: '65%', top: '-20px', rotation: 30, delay: 0.35162, duration: 8 },
    { left: '75%', top: '-20px', rotation: 30, delay: 0.413144, duration: 7 },
    { left: '85%', top: '-20px', rotation: 30, delay: 0.395388, duration: 6 },
    { left: '15%', top: '-20px', rotation: 30, delay: 0.582248, duration: 4 },
    { left: '27%', top: '-20px', rotation: 30, delay: 0.710367, duration: 4 },
    { left: '37%', top: '-20px', rotation: 30, delay: 0.564896, duration: 6 },
    { left: '47%', top: '-20px', rotation: 30, delay: 0.206357, duration: 7 },
    { left: '57%', top: '-20px', rotation: 30, delay: 0.628613, duration: 9 },
    { left: '67%', top: '-20px', rotation: 30, delay: 0.529785, duration: 7 },
    { left: '77%', top: '-20px', rotation: 30, delay: 0.64863, duration: 6 },
    { left: '87%', top: '-20px', rotation: 30, delay: 0.701722, duration: 3 },
    { left: '5%', top: '-20px', rotation: 30, delay: 0.366231, duration: 5 },
    { left: '22%', top: '-20px', rotation: 30, delay: 0.521904, duration: 7 },
    { left: '32%', top: '-20px', rotation: 30, delay: 0.484818, duration: 9 },
    { left: '42%', top: '-20px', rotation: 30, delay: 0.502043, duration: 3 },
    { left: '52%', top: '-20px', rotation: 30, delay: 0.577243, duration: 7 },
    { left: '62%', top: '-20px', rotation: 30, delay: 0.273317, duration: 5 },
    { left: '72%', top: '-20px', rotation: 30, delay: 0.556245, duration: 7 },
];

// Calculate responsive meteor count
const getMeteorCount = () => {
    if (typeof window === 'undefined') return positions.length;
    const isMobile = window.innerWidth < 768;
    const isReducedMotion = window?.matchMedia('(prefers-reduced-motion: reduce)')?.matches || false;
    return isMobile || isReducedMotion ? 15 : positions.length;
};

// Generate debris particles for meteor movement
const createDebris = (count) => {
    const debris = [];
    for (let i = 0; i < count; i++) {
        const angle = Math.random() * Math.PI * 2; // Random angle in radians
        const speed = Math.random() * 5 + 2; // 2-7 pixels
        debris.push({
            id: i,
            size: Math.random() * 2 + 1, // 1-3px
            offsetX: -5 - Math.random() * 10, // Position behind meteor
            offsetY: Math.random() * 6 - 3, // Slightly offset from center
            translateX: Math.cos(angle) * speed, // X movement based on angle
            translateY: Math.sin(angle) * speed, // Y movement based on angle
            color: i % 3 === 0 ? '#FCD34D' : i % 2 === 0 ? '#F97316' : '#EF4444', // Amber, orange, red
            duration: Math.random() * 2 + 1, // 1-3s
            opacity: Math.random() * 0.7 + 0.3 // 0.3-1
        });
    }
    return debris;
};

// Generate particle effects for each meteor
const createParticles = (count) => {
    const particles = [];
    for (let i = 0; i < count; i++) {
        particles.push({
            id: i,
            size: Math.random() * 3 + 1.5, // 1.5-4.5px (increased size)
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
    const count = typeof window !== 'undefined' ? getMeteorCount() : positions.length;
    return positions.slice(0, count).map((position, index) => {
        const particleCount = Math.floor(Math.random() * 3) + 3; // 3-5 particles
        const debrisCount = Math.floor(Math.random() * 4) + 2; // 2-5 debris particles
        const baseSize = Math.random() * 3 + 2; // Base size between 2-5px
        const glowSize = Math.floor(Math.random() * 15 + 10); // Enhanced glow radius 10-25px
        const trailLength = Math.floor(Math.random() * 80 + 80); // Longer trail length 80-160px

        // Use tailwind colors directly
        const glowColor = 'rgba(155, 135, 245, 0.8)'; // Primary color with opacity
        const trailColor = 'rgba(155, 135, 245, 0.8)'; // Primary color with opacity
        
        // Parse the left position if it's a percentage
        const leftPos = position.left.toString().includes('%') 
            ? (parseFloat(position.left) * window.innerWidth) / 100
            : parseFloat(position.left);
            
        return {
            ...position,
            left: leftPos,
            size: baseSize,
            trailLength,
            glow: glowSize,
            glowColor,
            trailColor,
            opacity: Math.random() * 0.2 + 0.8, // Higher base opacity 0.8-1
            particles: createParticles(particleCount),
            debris: createDebris(debrisCount)
        };
    });
});

// Adjust for responsive behavior on mounted
onMounted(() => {
    if (typeof window !== 'undefined') {
        window.addEventListener('resize', () => {
            meteors.value = meteors.value;
        });
    }
});
</script>

<style scoped>
.meteor-container {
    transform: translateZ(0); /* Hardware acceleration */
    mix-blend-mode: plus-lighter; /* Enhanced blending for glow effect */
}

.meteor {
    animation: meteor-effect linear infinite;
    animation-fill-mode: forwards;
    z-index: 20;
}

@keyframes meteor-effect {
    0% {
        opacity: 0;
        transform: translateX(0) translateY(0);
    }
    5% {
        opacity: 1;
    }
    10% {
        opacity: var(--opacity, 0.8);
        transform: translateX(-20px) translateY(30px);
    }
    70% {
        opacity: var(--opacity, 0.8);
    }
    100% {
        opacity: 0;
        transform: translateX(-100px) translateY(500px);
    }
}

@keyframes debris-float {
    0% {
        opacity: var(--opacity, 0.7);
    }
    100% {
        opacity: 0;
        transform: translate(var(--translateX, 10px), var(--translateY, 10px));
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
        transform: translateX(0) translateY(0);
    }
    5% {
        opacity: 1;
    }
    10% {
        opacity: var(--opacity, 0.8);
        transform: translateX(-30px) translateY(40px);
    }
    70% {
        opacity: var(--opacity, 0.8);
    }
    100% {
        opacity: 0;
        transform: translateX(-150px) translateY(550px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .meteor {
        animation-duration: 10s !important;
    }
    
    .meteor:nth-child(even) {
        display: none;
    }
}
</style>


<script setup>
import { onMounted, ref } from "vue";

const container = ref(null);
const particles = ref([]);
const isMobile = ref(window.innerWidth < 768);

// Generate random particles
const generateParticles = () => {
    const particleCount = isMobile.value ? 20 : 50; // Reduce by 60% on mobile
    const newParticles = [];

    for (let i = 0; i < particleCount; i++) {
        newParticles.push({
            id: i,
            x: Math.random() * 100, // position in percentage
            y: Math.random() * 100,
            size: 1 + Math.random() * 3,
            color: Math.random() > 0.6 ? "#9b87f5" : "#33C3F0", // primary or secondary
            opacity: 0.3 + Math.random() * 0.7,
            speed: 0.2 + Math.random() * 0.8,
            direction: Math.random() * 360, // degrees
            twinkleSpeed: 1 + Math.random() * 4, // seconds
        });
    }

    particles.value = newParticles;
};

const handleResize = () => {
    isMobile.value = window.innerWidth < 768;
    generateParticles();
};

onMounted(() => {
    generateParticles();
    window.addEventListener("resize", handleResize);

    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)"
    ).matches;

    if (!prefersReducedMotion) {
        // Animate particles
        const moveParticles = () => {
            const containerElement = container.value;
            if (!containerElement) return;

            // Get all particle elements
            const particleElements =
                containerElement.querySelectorAll(".cosmic-particle");

            for (let i = 0; i < particleElements.length; i++) {
                const particle = particles.value[i];
                if (!particle) continue;

                // Move particle based on its direction and speed
                particle.x +=
                    Math.cos((particle.direction * Math.PI) / 180) *
                    particle.speed *
                    0.02;
                particle.y +=
                    Math.sin((particle.direction * Math.PI) / 180) *
                    particle.speed *
                    0.02;

                // Wrap around edges
                if (particle.x > 105) particle.x = -5;
                if (particle.x < -5) particle.x = 105;
                if (particle.y > 105) particle.y = -5;
                if (particle.y < -5) particle.y = 105;

                // Update position
                particleElements[i].style.left = `${particle.x}%`;
                particleElements[i].style.top = `${particle.y}%`;
            }

            requestAnimationFrame(moveParticles);
        };

        requestAnimationFrame(moveParticles);
    }
});
</script>

<template>
    <div ref="container" class="absolute inset-[-15%] overflow-hidden pointer-events-none z-0 md:inset-0">
        <div
            v-for="particle in particles"
            :key="particle.id"
            class="cosmic-particle absolute rounded-full transform -translate-x-1/2 -translate-y-1/2"
            :style="{
                left: `${particle.x}%`,
                top: `${particle.y}%`,
                width: `${particle.size}px`,
                height: `${particle.size}px`,
                backgroundColor: particle.color,
                opacity: particle.opacity,
                boxShadow: `0 0 8px ${particle.color}`,
                animation: `twinkle ${particle.twinkleSpeed}s infinite alternate ease-in-out`,
                '--twinkle-speed': `${particle.twinkleSpeed}s`,
            }"
        ></div>

        <!-- Extended Nebula Effect -->
        <div class="absolute w-[130%] h-[130%] left-[-15%] top-[-15%] 
                    bg-[radial-gradient(ellipse_at_center,rgba(155,135,245,0.1)_0%,rgba(51,195,240,0.05)_40%,transparent_70%)]
                    blur-xl mix-blend-screen opacity-60 animate-rotate-120s
                    md:w-full md:h-full md:left-0 md:top-0"></div>

        <!-- Hexagonal Grid Pattern -->
        <div class="absolute inset-0 opacity-10 
                    bg-[repeating-linear-gradient(to_right,rgba(155,135,245,0.1),rgba(155,135,245,0.1)_1px,transparent_1px,transparent_20px),repeating-linear-gradient(to_bottom,rgba(155,135,245,0.1),rgba(155,135,245,0.1)_1px,transparent_1px,transparent_20px)]"></div>
    </div>
</template>

<style scoped>
/* Animations that can't be easily represented in Tailwind */
@keyframes twinkle {
    0%, 100% {
        opacity: 0.2;
        transform: translate(-50%, -50%) scale(0.8);
    }
    50% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.2);
    }
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-rotate-120s {
    animation: rotate 120s linear infinite;
}

/* Support for reduced motion */
@media (prefers-reduced-motion: reduce) {
    .cosmic-particle {
        animation: twinkle 5s infinite alternate ease-in-out !important;
    }

    .animate-rotate-120s {
        animation: none;
    }
}
</style>

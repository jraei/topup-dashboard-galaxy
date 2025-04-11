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
    <div ref="container" class="cosmic-particles">
        <div
            v-for="particle in particles"
            :key="particle.id"
            class="cosmic-particle"
            :style="{
                left: `${particle.x}%`,
                top: `${particle.y}%`,
                width: `${particle.size}px`,
                height: `${particle.size}px`,
                backgroundColor: particle.color,
                opacity: particle.opacity,
                animationDuration: `${particle.twinkleSpeed}s`,
            }"
        ></div>

        <!-- Quantum Field Effect -->
        <!-- <div class="quantum-field"></div> -->

        <!-- Extended Nebula Effect that goes beyond container -->
        <div class="extended-nebula"></div>

        <!-- Hexagonal Grid Pattern -->
        <div class="hex-grid"></div>
    </div>
</template>

<style scoped>
.cosmic-particles {
    position: absolute;
    inset: -15%; /* Extend beyond container edges */
    overflow: hidden;
    pointer-events: none;
    z-index: 0;
}

@media (max-width: 768px) {
    .cosmic-particles {
        inset: 0; /* Contain within boundaries on mobile */
    }
}

.cosmic-particle {
    position: absolute;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 8px currentColor;
    animation: twinkle var(--twinkle-speed, 3s) infinite alternate ease-in-out;
}

/* Quantum Field Effect */
.quantum-field {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: repeating-linear-gradient(
        45deg,
        rgba(155, 135, 245, 0.03),
        rgba(51, 195, 240, 0.03) 10px,
        transparent 10px,
        transparent 20px
    );
    opacity: 0.5;
    mix-blend-mode: screen;
}

/* Extended Nebula Effect */
.extended-nebula {
    position: absolute;
    width: 130%;
    height: 130%;
    left: -15%;
    top: -15%;
    background: radial-gradient(
        ellipse at center,
        rgba(155, 135, 245, 0.1) 0%,
        rgba(51, 195, 240, 0.05) 40%,
        transparent 70%
    );
    filter: blur(20px);
    mix-blend-mode: screen;
    opacity: 0.6;
    animation: rotate 120s linear infinite;
    transform-origin: center;
}

@media (max-width: 768px) {
    .extended-nebula {
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
    }
}

/* Hexagonal Grid Pattern */
.hex-grid {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    background-image: repeating-linear-gradient(
            to right,
            rgba(155, 135, 245, 0.1),
            rgba(155, 135, 245, 0.1) 1px,
            transparent 1px,
            transparent 20px
        ),
        repeating-linear-gradient(
            to bottom,
            rgba(155, 135, 245, 0.1),
            rgba(155, 135, 245, 0.1) 1px,
            transparent 1px,
            transparent 20px
        );
}

/* Animations */
@keyframes twinkle {
    0%,
    100% {
        opacity: 0.2;
        transform: translate(-50%, -50%) scale(0.8);
    }
    50% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.2);
    }
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Support for reduced motion */
@media (prefers-reduced-motion: reduce) {
    .cosmic-particle {
        animation: twinkle 5s infinite alternate ease-in-out !important;
    }

    .extended-nebula {
        animation: none;
    }
}
</style>

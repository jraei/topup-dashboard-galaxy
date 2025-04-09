
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

// Generate random number between min and max
const random = (min, max) => {
    return Math.random() * (max - min) + min;
};

// Particle configuration
const particleCount = ref(window.innerWidth < 768 ? 30 : 75); // Reduce count on mobile
const particles = ref([]);

const initParticles = () => {
    particles.value = [];
    for (let i = 0; i < particleCount.value; i++) {
        particles.value.push({
            id: i,
            x: random(0, 100),
            y: random(0, 100),
            size: random(1, 3),
            opacity: random(0.1, 0.5),
            speed: random(20, 60),
            direction: random(0, 360) * (Math.PI / 180)
        });
    }
};

let animationFrame = null;

const updateParticles = () => {
    particles.value = particles.value.map(particle => {
        // Calculate new position
        const vx = Math.cos(particle.direction) * (particle.speed / 1000);
        const vy = Math.sin(particle.direction) * (particle.speed / 1000);
        
        let x = particle.x + vx;
        let y = particle.y + vy;
        
        // Handle boundary
        if (x < 0) x = 100;
        if (x > 100) x = 0;
        if (y < 0) y = 100;
        if (y > 100) y = 0;
        
        return {
            ...particle,
            x,
            y
        };
    });
    
    animationFrame = requestAnimationFrame(updateParticles);
};

const handleResize = () => {
    particleCount.value = window.innerWidth < 768 ? 30 : 75;
    initParticles();
};

onMounted(() => {
    initParticles();
    updateParticles();
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    if (animationFrame) {
        cancelAnimationFrame(animationFrame);
    }
    window.removeEventListener('resize', handleResize);
});
</script>

<template>
    <div class="cosmic-particles-container">
        <div 
            v-for="particle in particles" 
            :key="particle.id"
            class="cosmic-particle"
            :style="{
                left: `${particle.x}%`,
                top: `${particle.y}%`,
                width: `${particle.size}px`,
                height: `${particle.size}px`,
                opacity: particle.opacity,
                boxShadow: `0 0 ${particle.size * 2}px ${particle.size / 2}px rgba(155, 135, 245, 0.8)`
            }"
        ></div>
        
        <!-- Hexagonal grid overlay -->
        <div class="hexagonal-grid"></div>
    </div>
</template>

<style scoped>
.cosmic-particles-container {
    width: 100%;
    height: 100%;
    overflow: hidden;
    position: absolute;
    top: 0;
    left: 0;
}

.cosmic-particle {
    position: absolute;
    border-radius: 50%;
    background-color: #9b87f5;
    pointer-events: none;
}

.hexagonal-grid {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: repeating-linear-gradient(
        60deg,
        rgba(51, 195, 240, 0.03) 0,
        rgba(51, 195, 240, 0.03) 1px,
        transparent 1px,
        transparent 30px
    ),
    repeating-linear-gradient(
        120deg,
        rgba(51, 195, 240, 0.03) 0,
        rgba(51, 195, 240, 0.03) 1px,
        transparent 1px,
        transparent 30px
    );
    filter: blur(0.5px);
    opacity: 0.3;
    pointer-events: none;
}

/* Extended cosmic wrapper for desktop/tablet */
@media (min-width: 768px) {
    .cosmic-particles-container {
        margin: -10%;
        padding: 10%;
    }
}
</style>

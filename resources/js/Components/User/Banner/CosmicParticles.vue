
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    count: {
        type: Number,
        default: 30
    },
    maxSize: {
        type: Number,
        default: 3
    },
    minSize: {
        type: Number,
        default: 1
    },
    color: {
        type: String,
        default: 'primary'
    },
    speed: {
        type: Number,
        default: 50 // seconds to complete full movement
    }
});

const particles = ref([]);

onMounted(() => {
    generateParticles();
});

const generateParticles = () => {
    particles.value = [];
    for (let i = 0; i < props.count; i++) {
        particles.value.push({
            id: i,
            x: Math.random() * 100,
            y: Math.random() * 100,
            size: Math.random() * (props.maxSize - props.minSize) + props.minSize,
            opacity: Math.random() * 0.5 + 0.2,
            duration: (Math.random() * props.speed + props.speed / 2).toFixed(1),
            delay: (Math.random() * -20).toFixed(1)
        });
    }
};
</script>

<template>
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div 
            v-for="particle in particles" 
            :key="particle.id"
            class="absolute rounded-full transition-all duration-500"
            :class="{
                'bg-primary': color === 'primary',
                'bg-secondary': color === 'secondary',
                'bg-white': color === 'white'
            }"
            :style="{
                left: `${particle.x}%`,
                top: `${particle.y}%`,
                width: `${particle.size}px`,
                height: `${particle.size}px`,
                opacity: particle.opacity,
                animation: `floatParticle ${particle.duration}s infinite ease-in-out`,
                animationDelay: `${particle.delay}s`,
                boxShadow: particle.size > 2 ? `0 0 ${particle.size * 2}px var(--color-${color || 'primary'})` : 'none'
            }"
        ></div>
    </div>
</template>

<style>
@keyframes floatParticle {
    0%, 100% {
        transform: translate(0, 0);
    }
    25% {
        transform: translate(30px, 30px);
    }
    50% {
        transform: translate(0, 60px);
    }
    75% {
        transform: translate(-30px, 30px);
    }
}
</style>

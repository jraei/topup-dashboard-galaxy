
<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    isAnimated: {
        type: Boolean,
        default: false
    }
});

const rocketPosition = ref({ x: 0, y: 0 });
const particles = ref([]);
const earthRotation = ref(0);
const micrometeors = ref([]);

// Generate random meteors
onMounted(() => {
    // Create meteors with random orbits
    for (let i = 0; i < 10; i++) {
        const angle = Math.random() * Math.PI * 2;
        const distance = 40 + Math.random() * 30;
        const orbitSpeed = 0.1 + Math.random() * 0.5;
        const size = 1 + Math.random() * 2;
        
        micrometeors.value.push({
            id: i,
            angle,
            distance,
            orbitSpeed,
            size
        });
    }
    
    // Start animation loop
    animateSystem();
});

const animateSystem = () => {
    if (props.isAnimated) {
        // Earth rotation (slow)
        earthRotation.value += 0.03;
        
        // Update rocket position (elliptical orbit)
        const t = performance.now() / 1000 * 0.6; // 60s full orbit
        const a = 100; // semi-major axis
        const b = 70;  // semi-minor axis
        rocketPosition.value = {
            x: a * Math.cos(t),
            y: b * Math.sin(t)
        };
        
        // Update meteors
        micrometeors.value.forEach(meteor => {
            meteor.angle += meteor.orbitSpeed / 100;
        });
        
        // Generate engine trail particles
        if (Math.random() > 0.6) {
            particles.value.push({
                id: Date.now(),
                x: rocketPosition.value.x,
                y: rocketPosition.value.y,
                size: 1 + Math.random() * 2,
                opacity: 1,
                lifetime: 0
            });
        }
        
        // Update particles
        particles.value = particles.value.filter(particle => {
            particle.lifetime += 0.02;
            particle.opacity = 1 - particle.lifetime / 1.5;
            
            // Remove particles after lifetime
            return particle.lifetime < 1.5;
        });
    }
    
    requestAnimationFrame(animateSystem);
};

// Reset animation when visibility changes
watch(() => props.isAnimated, (newValue) => {
    if (!newValue) {
        // Reset particles when not visible to save resources
        particles.value = [];
    }
});
</script>

<template>
    <div class="relative w-64 h-64">
        <!-- Earth -->
        <div class="absolute top-1/2 left-1/2 w-32 h-32 -translate-x-1/2 -translate-y-1/2">
            <!-- Atmosphere Glow -->
            <div class="absolute inset-0 rounded-full shadow-[0_0_40px_10px_#33C3F030] opacity-60"></div>
            
            <!-- Earth Body -->
            <div class="absolute inset-0 rounded-full bg-secondary overflow-hidden" 
                :style="{ transform: `rotate(${earthRotation}deg)` }">
                
                <!-- Land Masses -->
                <div class="absolute inset-0 rounded-full bg-content_background opacity-60">
                    <div class="absolute top-1/4 left-1/4 w-1/3 h-1/4 rounded-full bg-content_background/80"></div>
                    <div class="absolute bottom-1/3 right-1/5 w-1/4 h-1/5 rounded-full bg-content_background/80"></div>
                </div>
                
                <!-- Cloud Layer (Semi-transparent) -->
                <div class="absolute inset-0 rounded-full"
                    :style="{ transform: `rotate(${earthRotation * 1.5}deg)` }">
                    <div class="absolute top-1/6 left-1/3 w-1/4 h-1/6 rounded-full bg-white opacity-20"></div>
                    <div class="absolute bottom-1/4 right-1/4 w-1/3 h-1/5 rounded-full bg-white opacity-20"></div>
                    <div class="absolute top-1/2 left-1/6 w-1/5 h-1/6 rounded-full bg-white opacity-20"></div>
                </div>
            </div>
        </div>
        
        <!-- Micro-Meteors -->
        <div v-for="meteor in micrometeors" :key="meteor.id"
            class="absolute top-1/2 left-1/2 bg-white rounded-full"
            :style="{
                width: `${meteor.size}px`,
                height: `${meteor.size}px`,
                transform: `translate(-50%, -50%) translate(${Math.cos(meteor.angle) * meteor.distance}px, ${Math.sin(meteor.angle) * meteor.distance}px)`,
                boxShadow: `0 0 ${meteor.size * 2}px ${meteor.size / 2}px rgba(255, 255, 255, 0.8)`
            }">
        </div>
        
        <!-- Rocket and Particle Trail -->
        <div class="absolute top-1/2 left-1/2 z-10"
            :style="{ transform: `translate(-50%, -50%) translate(${rocketPosition.x}px, ${rocketPosition.y}px)` }">
            <!-- Rocket -->
            <div class="w-3 h-6 bg-white rounded-t-full rotate-[30deg]">
                <div class="absolute -bottom-1 left-0 w-3 h-2 bg-primary rounded-b-full"></div>
            </div>
        </div>
        
        <!-- Particle Trail -->
        <div v-for="particle in particles" :key="particle.id"
            class="absolute top-1/2 left-1/2 rounded-full bg-primary"
            :style="{
                width: `${particle.size}px`,
                height: `${particle.size}px`,
                opacity: particle.opacity,
                transform: `translate(-50%, -50%) translate(${rocketPosition.x - (1 - particle.opacity) * 15}px, ${rocketPosition.y - (1 - particle.opacity) * 15}px)`,
                boxShadow: `0 0 ${particle.size * 3}px ${particle.size}px rgba(155, 135, 245, ${particle.opacity * 0.5})`
            }">
        </div>
    </div>
</template>

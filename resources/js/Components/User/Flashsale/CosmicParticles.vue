
<template>
    <canvas ref="canvasRef" class="absolute inset-0 w-full h-full"></canvas>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";

const props = defineProps({
    itemId: {
        type: [Number, String],
        required: true
    },
    density: {
        type: Number,
        default: 1 // 1 = normal, 0.5 = reduced, etc.
    },
    theme: {
        type: String,
        default: 'primary' // 'primary' or 'secondary'
    }
});

const canvasRef = ref(null);
let ctx = null;
let animationFrameId = null;
let particles = [];
let lastTime = 0;
let frameCount = 0;
let isVisible = false;
let isActive = true;

// Create a shared particle pool across all instances
const SHARED_POOL_SIZE = 200;
let sharedParticlePool = null;
let poolIndex = 0;

const isMobile = computed(() => window.innerWidth < 768);
const isLowPowerDevice = computed(() => {
    return navigator.hardwareConcurrency ? navigator.hardwareConcurrency < 4 : isMobile.value;
});

// Determine particle count based on device capability
const getParticleCount = computed(() => {
    // Use 12 particles on mobile as requested
    if (isMobile.value) return Math.floor(12 * props.density);
    
    const base = isLowPowerDevice.value ? 15 : 25;
    return Math.floor(base * props.density);
});

// Colors based on theme
const getColors = computed(() => {
    if (props.theme === 'secondary') {
        return {
            main: '#33C3F0',
            glow: 'rgba(51, 195, 240, 0.6)',
            faint: 'rgba(51, 195, 240, 0.3)'
        };
    }
    // Default primary theme
    return {
        main: '#9b87f5',
        glow: 'rgba(155, 135, 245, 0.6)',
        faint: 'rgba(155, 135, 245, 0.3)'
    };
});

// Initialize or reuse the shared particle pool
function getSharedPool() {
    if (!sharedParticlePool) {
        sharedParticlePool = new Array(SHARED_POOL_SIZE);
        for (let i = 0; i < SHARED_POOL_SIZE; i++) {
            sharedParticlePool[i] = {
                x: 0,
                y: 0,
                size: 0,
                color: '',
                vx: 0,
                vy: 0,
                opacity: 0,
                pulse: 0,
                pulseDirection: 1,
                inUse: false
            };
        }
    }
    return sharedParticlePool;
}

function initCanvas() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    
    ctx = canvas.getContext('2d', { alpha: true });
    resizeCanvas();
    
    // Create particles
    generateParticles();
    
    // Start animation loop
    lastTime = performance.now();
    animate();
}

function resizeCanvas() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    
    ctx.scale(dpr, dpr);
}

function generateParticles() {
    particles = [];
    const count = getParticleCount.value;
    const colors = getColors.value;
    const pool = getSharedPool();
    
    for (let i = 0; i < count; i++) {
        // Find an unused particle in the pool
        let particleIndex = -1;
        for (let j = 0; j < SHARED_POOL_SIZE; j++) {
            const idx = (poolIndex + j) % SHARED_POOL_SIZE;
            if (!pool[idx].inUse) {
                particleIndex = idx;
                break;
            }
        }
        
        // If the pool is full, skip
        if (particleIndex === -1) continue;
        
        poolIndex = (particleIndex + 1) % SHARED_POOL_SIZE;
        
        // Get particle from pool and initialize
        const particle = pool[particleIndex];
        particle.x = Math.random() * canvasRef.value.width;
        particle.y = Math.random() * canvasRef.value.height;
        particle.size = Math.random() * 2 + 1;
        particle.color = i % 3 === 0 ? colors.main : colors.faint;
        
        // Brownian motion - 2px/s drift as requested
        particle.vx = (Math.random() - 0.5) * 0.1; // Slower drift
        particle.vy = (Math.random() - 0.5) * 0.1;
        
        // Opacity pulsing in 0.6-1 range as requested
        particle.opacity = Math.random() * 0.2 + 0.6; // 0.6-0.8 initial
        particle.pulse = Math.random() * 0.01 + 0.005;
        particle.pulseDirection = Math.random() > 0.5 ? 1 : -1;
        particle.inUse = true;
        
        particles.push(particle);
    }
}

function drawParticle(particle) {
    // Pulse opacity in the 0.6-1 range
    particle.opacity += particle.pulse * particle.pulseDirection;
    if (particle.opacity > 0.9) {
        particle.pulseDirection = -1;
    } else if (particle.opacity < 0.6) {
        particle.pulseDirection = 1;
    }
    
    // Move position with Brownian motion (2px/s drift)
    particle.x += particle.vx + (Math.random() - 0.5) * 0.05;
    particle.y += particle.vy + (Math.random() - 0.5) * 0.05;
    
    // Wrap around screen edges
    if (particle.x < 0) particle.x = canvasRef.value.width;
    if (particle.x > canvasRef.value.width) particle.x = 0;
    if (particle.y < 0) particle.y = canvasRef.value.height;
    if (particle.y > canvasRef.value.height) particle.y = 0;
    
    // Draw the particle
    ctx.beginPath();
    ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
    ctx.fillStyle = particle.color;
    ctx.globalAlpha = particle.opacity;
    ctx.fill();
}

// Move text check function to ensure particles don't overlap text
function isNearTextArea(x, y) {
    // For FlashsaleCard, avoid left 50% of card where text is
    if (props.itemId.toString().includes('flashcard-')) {
        const canvas = canvasRef.value;
        const midpoint = canvas.width / 2;
        
        // Consider area near text (left side of card)
        return x < midpoint;
    }
    
    return false;
}

// Optimized animation loop with performance metrics
function animate(timestamp = 0) {
    if (!isActive || !isVisible) return;
    
    // Throttle to 30fps for low power devices
    const frameRate = isLowPowerDevice.value ? 33.3 : 16.6; // 30fps or 60fps
    const elapsed = timestamp - lastTime;
    
    frameCount++;
    if (frameCount % 60 === 0) {
        // Check CPU usage every ~1 second
        const cpuTime = performance.now() - timestamp;
        if (cpuTime > 5 && !isLowPowerDevice.value) {
            console.warn(`CosmicParticles (${props.itemId}) CPU time: ${cpuTime.toFixed(2)}ms (limit: 5ms)`);
        }
    }
    
    if (elapsed > frameRate) {
        // Clear canvas
        ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);
        
        // Draw all particles with position constraints
        for (const particle of particles) {
            // Max 3 particles near text areas
            if (isNearTextArea(particle.x, particle.y)) {
                const textAreaParticles = particles.filter(p => 
                    isNearTextArea(p.x, p.y) && p !== particle
                );
                
                if (textAreaParticles.length < 3) {
                    drawParticle(particle);
                }
            } else {
                drawParticle(particle);
            }
        }
        
        lastTime = timestamp - (elapsed % frameRate);
    }
    
    // Request next frame only if active and visible
    animationId = requestAnimationFrame(animate);
}

function checkVisibility() {
    if (!canvasRef.value) return;
    
    const rect = canvasRef.value.getBoundingClientRect();
    const wasVisible = isVisible;
    
    isVisible = 
        rect.top < window.innerHeight &&
        rect.bottom > 0 &&
        rect.left < window.innerWidth &&
        rect.right > 0;
    
    // Toggle animation based on visibility
    if (!wasVisible && isVisible && isActive) {
        lastTime = performance.now();
        animate();
    }
}

onMounted(() => {
    initCanvas();
    window.addEventListener('resize', resizeCanvas);
    window.addEventListener('scroll', checkVisibility, { passive: true });
    
    // Check initial visibility
    checkVisibility();
    
    // Pause when tab is inactive
    document.addEventListener('visibilitychange', () => {
        isActive = !document.hidden;
        
        if (isActive && isVisible) {
            lastTime = performance.now();
            animate();
        }
    });
});

onUnmounted(() => {
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
    }
    
    // Release particles back to pool
    if (particles.length > 0) {
        particles.forEach(particle => {
            particle.inUse = false;
        });
    }
    
    window.removeEventListener('resize', resizeCanvas);
    window.removeEventListener('scroll', checkVisibility);
    isActive = false;
});

// Watch for density changes
watch(() => props.density, () => {
    if (isActive && isVisible) {
        generateParticles();
    }
});
</script>


<template>
    <canvas ref="canvasRef" class="absolute inset-0 w-full h-full"></canvas>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";

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

const isMobile = computed(() => window.innerWidth < 768);
const isLowPowerDevice = computed(() => {
    return navigator.hardwareConcurrency ? navigator.hardwareConcurrency < 4 : isMobile.value;
});

// Determine particle count based on device capability
const getParticleCount = computed(() => {
    const base = isMobile.value ? 10 : (isLowPowerDevice.value ? 15 : 25);
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
    
    for (let i = 0; i < count; i++) {
        particles.push({
            x: Math.random() * canvasRef.value.width,
            y: Math.random() * canvasRef.value.height,
            size: Math.random() * 2 + 1,
            color: i % 3 === 0 ? colors.main : colors.faint,
            vx: (Math.random() - 0.5) * 0.2,
            vy: (Math.random() - 0.5) * 0.2,
            opacity: Math.random() * 0.5 + 0.3,
            pulse: Math.random() * 0.02 + 0.01,
            pulseDirection: Math.random() > 0.5 ? 1 : -1
        });
    }
}

function drawParticle(particle) {
    // Pulse opacity
    particle.opacity += particle.pulse * particle.pulseDirection;
    if (particle.opacity > 0.8) {
        particle.pulseDirection = -1;
    } else if (particle.opacity < 0.3) {
        particle.pulseDirection = 1;
    }
    
    // Move position with slight random variation
    particle.x += particle.vx + (Math.random() - 0.5) * 0.1;
    particle.y += particle.vy + (Math.random() - 0.5) * 0.1;
    
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

function animate(timestamp = 0) {
    if (!isActive || !isVisible) return;
    
    // Throttle to 30fps for low power devices
    const frameRate = isLowPowerDevice.value ? 33.3 : 16.6; // 30fps or 60fps
    const elapsed = timestamp - lastTime;
    
    if (elapsed > frameRate) {
        // Clear canvas
        ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);
        
        // Draw all particles
        for (const particle of particles) {
            drawParticle(particle);
        }
        
        lastTime = timestamp - (elapsed % frameRate);
    }
    
    // Request next frame only if active and visible
    animationFrameId = requestAnimationFrame(animate);
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
    window.removeEventListener('resize', resizeCanvas);
    window.removeEventListener('scroll', checkVisibility);
    isActive = false;
});
</script>

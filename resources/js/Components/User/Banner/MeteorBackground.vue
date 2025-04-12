<!-- We need to significantly revise MeteorBackground.vue with new trajectory and effects -->
<template>
    <div
        class="absolute inset-0 z-20 overflow-hidden pointer-events-none meteor-container"
    >
        <canvas ref="canvasRef" class="absolute inset-0 w-full h-full"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";

const canvasRef = ref(null);
let animationFrameId = null;
let ctx = null;
let meteors = [];
let lastFrameTime = 0;
const meteorPool = []; // For object pooling
const poolSize = 15; // Maximum number of meteors visible at once
let lastMeteorTime = 0;
const meteorSpawnDelay = 300; // ms between meteor spawns
let isRendering = true;

// Enhanced meteor positions with varied entry points
// All meteors come from the top area of the viewport
const positions = [
    // Top edge entries with downward angle
    { left: "20%", top: "-20px", rotation: 30, delay: 0.686975, duration: 8 },
    { left: "30%", top: "-20px", rotation: 10, delay: 0.670151, duration: 8 },
    { left: "40%", top: "-20px", rotation: 30, delay: 0.632454, duration: 9 },
    { left: "50%", top: "-20px", rotation: 10, delay: 0.524996, duration: 5 },
    { left: "60%", top: "-20px", rotation: 30, delay: 0.460272, duration: 8 },
    { left: "70%", top: "-20px", rotation: 10, delay: 0.223791, duration: 6 },
    { left: "80%", top: "-20px", rotation: 30, delay: 0.406558, duration: 4 },
    { left: "10%", top: "-20px", rotation: 10, delay: 0.475533, duration: 6 },
    { left: "25%", top: "-20px", rotation: 30, delay: 0.394929, duration: 5 },
    { left: "35%", top: "-20px", rotation: 10, delay: 0.78249, duration: 2 },
    { left: "45%", top: "-20px", rotation: 30, delay: 0.353787, duration: 5 },
    { left: "55%", top: "-20px", rotation: 10, delay: 0.309607, duration: 5 },
    { left: "65%", top: "-20px", rotation: 30, delay: 0.35162, duration: 8 },
    { left: "75%", top: "-20px", rotation: 10, delay: 0.413144, duration: 7 },
];

const isReducedMotion = computed(() => {
    return window?.matchMedia("(prefers-reduced-motion: reduce)")?.matches || false;
});

const isMobile = computed(() => window.innerWidth < 768);
const isLowPowerDevice = computed(() => {
    return navigator.hardwareConcurrency ? navigator.hardwareConcurrency < 4 : isMobile.value;
});

// Calculate responsive meteor count
const getMeteorCount = computed(() => {
    if (isReducedMotion.value) return 2;
    if (isLowPowerDevice.value || isMobile.value) return 3;
    if (window.innerWidth < 1024) return 5; // Tablet
    return 8; // Desktop
});

function initCanvas() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    
    ctx = canvas.getContext('2d', { alpha: true });
    resizeCanvas();
    
    // Init meteor pool
    initMeteorPool();
    
    // Start animation loop
    lastFrameTime = performance.now();
    animate();
}

function resizeCanvas() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    
    // Use devicePixelRatio for crisp rendering
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    
    // Scale context for high DPI displays
    ctx.scale(dpr, dpr);
}

function initMeteorPool() {
    meteorPool.length = 0;
    
    // Pre-create meteor objects for reuse
    for (let i = 0; i < poolSize; i++) {
        meteorPool.push(createMeteor());
    }
}

function createMeteor() {
    const position = positions[Math.floor(Math.random() * positions.length)];
    const baseSize = Math.random() * 3 + 2; // Base size between 2-5px
    const glowSize = Math.floor(Math.random() * 15 + 10); // Enhanced glow 10-25px
    const trailLength = Math.floor(Math.random() * 80 + 80); // 80-160px
    const useSecondary = Math.random() > 0.7;
    
    // Use tailwind colors directly
    const glowColor = useSecondary 
        ? "rgba(51, 195, 240, 0.8)"  // secondary
        : "rgba(155, 135, 245, 0.8)"; // primary
    const trailColor = useSecondary
        ? "rgba(51, 195, 240, 0.8)"
        : "rgba(155, 135, 245, 0.8)";
    
    // Parse the left position if it's a percentage
    const leftPos = position.left.toString().includes("%")
        ? (parseFloat(position.left) * canvasRef.value.width) / 100
        : parseFloat(position.left);
    
    return {
        active: false, // Initially inactive
        x: leftPos,
        y: parseFloat(position.top) || 0,
        size: baseSize,
        trailLength,
        glow: glowSize,
        glowColor,
        trailColor,
        opacity: Math.random() * 0.2 + 0.8, // Higher base opacity 0.8-1
        progress: 0, // Animation progress from 0 to 1
        duration: position.duration * 1000, // Convert to ms
        startTime: 0,
        particles: isLowPowerDevice.value ? [] : createParticles(3 + Math.floor(Math.random() * 2)),
        debris: isLowPowerDevice.value ? [] : createDebris(2 + Math.floor(Math.random() * 3)),
        rotation: position.rotation || 0
    };
}

// Generate debris particles for meteor movement
function createDebris(count) {
    const debris = [];
    if (isLowPowerDevice.value) return debris;
    
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
            color:
                i % 3 === 0 ? "#FCD34D" : i % 2 === 0 ? "#F97316" : "#EF4444", // Amber, orange, red
            duration: Math.random() * 2 + 1, // 1-3s
            opacity: Math.random() * 0.7 + 0.3, // 0.3-1
            progress: 0
        });
    }
    return debris;
}

// Generate particle effects for each meteor
function createParticles(count) {
    const particles = [];
    if (isLowPowerDevice.value) return particles;
    
    for (let i = 0; i < count; i++) {
        particles.push({
            id: i,
            size: Math.random() * 3 + 1.5, // 1.5-4.5px
            offsetX: Math.random() * 10 - 20, // Position behind meteor
            offsetY: Math.random() * 6 - 3, // Slightly offset from center
            color:
                i % 3 === 0 ? "#FCD34D" : i % 2 === 0 ? "#F97316" : "#EF4444", // Amber, orange, red
            duration: Math.random() * 1.5 + 0.5, // 0.5-2s
            opacity: Math.random() * 0.7 + 0.3, // 0.3-1
            progress: 0
        });
    }
    return particles;
}

function activateMeteor() {
    // Find inactive meteor in pool
    const inactiveMeteor = meteorPool.find(m => !m.active);
    if (!inactiveMeteor) return;
    
    // Reset and activate
    const position = positions[Math.floor(Math.random() * positions.length)];
    const leftPos = position.left.toString().includes("%")
        ? (parseFloat(position.left) * canvasRef.value.width) / 100
        : parseFloat(position.left);
    
    inactiveMeteor.x = leftPos;
    inactiveMeteor.y = parseFloat(position.top) || 0;
    inactiveMeteor.progress = 0;
    inactiveMeteor.active = true;
    inactiveMeteor.startTime = performance.now();
    inactiveMeteor.duration = position.duration * 1000;
    
    // Add to active meteors
    meteors.push(inactiveMeteor);
}

function updateMeteors(timestamp) {
    // Spawn new meteors at intervals (respecting max count)
    if (timestamp - lastMeteorTime > meteorSpawnDelay && 
        meteors.length < getMeteorCount.value) {
        activateMeteor();
        lastMeteorTime = timestamp;
    }
    
    // Update and remove finished meteors
    meteors = meteors.filter(meteor => {
        // Update progress
        const elapsed = timestamp - meteor.startTime;
        meteor.progress = Math.min(1, elapsed / meteor.duration);
        
        // Keep only active and unfinished meteors
        if (meteor.progress >= 1) {
            meteor.active = false;
            return false;
        }
        return true;
    });
}

function drawMeteor(meteor) {
    if (!meteor.active || meteor.progress >= 1) return;
    
    // Calculate current position based on progress
    const startX = meteor.x;
    const startY = meteor.y;
    const angle = (meteor.rotation * Math.PI) / 180;
    const distance = 346; // Fixed distance for 60° angle
    
    const currentX = startX + Math.cos(angle) * distance * meteor.progress * -1;
    const currentY = startY + Math.sin(angle) * distance * meteor.progress;
    
    // Fade in/out based on progress
    let opacity = meteor.opacity;
    if (meteor.progress < 0.1) {
        opacity = meteor.opacity * (meteor.progress / 0.1);
    } else if (meteor.progress > 0.7) {
        opacity = meteor.opacity * (1 - (meteor.progress - 0.7) / 0.3);
    }
    
    // Draw meteor trail
    const trailEndX = currentX + Math.cos(angle) * meteor.trailLength * -1;
    const trailEndY = currentY + Math.sin(angle) * meteor.trailLength * -1;
    
    const gradient = ctx.createLinearGradient(
        currentX, currentY, 
        trailEndX, trailEndY
    );
    
    gradient.addColorStop(0, meteor.trailColor);
    gradient.addColorStop(1, "transparent");
    
    ctx.beginPath();
    ctx.moveTo(currentX, currentY);
    ctx.lineTo(trailEndX, trailEndY);
    ctx.lineWidth = 2;
    ctx.strokeStyle = gradient;
    ctx.globalAlpha = opacity;
    ctx.stroke();
    
    // Draw meteor core with glow
    ctx.beginPath();
    ctx.arc(currentX, currentY, meteor.size, 0, Math.PI * 2);
    ctx.fillStyle = meteor.glowColor;
    ctx.fill();
    
    // Draw particles if not low power mode
    if (!isLowPowerDevice.value) {
        meteor.particles.forEach(particle => {
            const particleProgress = (performance.now() % (particle.duration * 1000)) / (particle.duration * 1000);
            
            const particleX = currentX + particle.offsetX;
            const particleY = currentY + particle.offsetY;
            
            ctx.beginPath();
            ctx.arc(particleX, particleY, particle.size * (1 - particleProgress), 0, Math.PI * 2);
            ctx.fillStyle = particle.color;
            ctx.globalAlpha = particle.opacity * (1 - particleProgress);
            ctx.fill();
        });
        
        // Draw debris
        meteor.debris.forEach(debris => {
            const debrisProgress = (performance.now() % (debris.duration * 1000)) / (debris.duration * 1000);
            
            const debrisX = currentX + debris.offsetX + debris.translateX * debrisProgress;
            const debrisY = currentY + debris.offsetY + debris.translateY * debrisProgress;
            
            ctx.beginPath();
            ctx.arc(debrisX, debrisY, debris.size, 0, Math.PI * 2);
            ctx.fillStyle = debris.color;
            ctx.globalAlpha = debris.opacity * (1 - debrisProgress);
            ctx.fill();
        });
    }
    
    // Reset global alpha
    ctx.globalAlpha = 1;
}

function animate(timestamp = 0) {
    if (!isRendering) return;
    
    // Clear canvas
    ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);
    
    // Update meteor states
    updateMeteors(timestamp);
    
    // Draw all active meteors
    meteors.forEach(meteor => drawMeteor(meteor));
    
    // Request next frame
    animationFrameId = requestAnimationFrame(animate);
}

// Check if element is in viewport
function checkVisibility() {
    if (!canvasRef.value) return;
    
    const rect = canvasRef.value.getBoundingClientRect();
    const isVisible = 
        rect.top < window.innerHeight &&
        rect.bottom > 0 &&
        rect.left < window.innerWidth &&
        rect.right > 0;
    
    // Only render when visible
    if (isVisible && !isRendering) {
        isRendering = true;
        animate(performance.now());
    } else if (!isVisible && isRendering) {
        isRendering = false;
        cancelAnimationFrame(animationFrameId);
    }
}

onMounted(() => {
    initCanvas();
    window.addEventListener("resize", resizeCanvas);
    window.addEventListener("scroll", checkVisibility);
    
    // Respect reduced motion settings
    if (isReducedMotion.value) {
        meteorSpawnDelay = 1000; // Slower spawning
    }
    
    // Initial visibility check
    checkVisibility();
    
    // Pause when tab is not visible
    document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
            isRendering = false;
            cancelAnimationFrame(animationFrameId);
        } else if (canvasRef.value) {
            checkVisibility();
        }
    });
});

onUnmounted(() => {
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
    }
    window.removeEventListener("resize", resizeCanvas);
    window.removeEventListener("scroll", checkVisibility);
});
</script>

<style scoped>
.meteor-container {
    transform: translateZ(0); /* Hardware acceleration */
    mix-blend-mode: plus-lighter; /* Enhanced blending for glow effect */
    z-index: 20;
}
</style>

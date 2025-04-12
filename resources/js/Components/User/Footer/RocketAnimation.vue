
<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useIntersectionObserver, usePreferredReducedMotion } from '@vueuse/core';

// Animation state
const rocketRef = ref(null);
const containerRef = ref(null);
const isVisible = ref(false);
const isAnimating = ref(false);
const prefersReducedMotion = usePreferredReducedMotion();

// Rocket animation parameters
const animationDuration = ref(10000); // 10 seconds
const particleLifetime = 1500; // 1.5 seconds
const particles = ref([]);
const animationInterval = ref(null);
const rafId = ref(null);

// Set up intersection observer to detect when footer is visible
onMounted(() => {
  if (containerRef.value) {
    useIntersectionObserver(
      containerRef,
      ([{ isIntersecting }]) => {
        isVisible.value = isIntersecting;
        
        // Start/stop animation based on visibility
        if (isIntersecting && !isAnimating.value && !prefersReducedMotion.value) {
          startRocketAnimation();
        } else if (!isIntersecting && isAnimating.value) {
          stopRocketAnimation();
        }
      },
      { threshold: 0.1 }
    );
    
    // Initial animation
    if (!prefersReducedMotion.value) {
      setTimeout(startRocketAnimation, 1000);
    }
  }
});

onBeforeUnmount(() => {
  stopRocketAnimation();
});

// Start the rocket animation
const startRocketAnimation = () => {
  if (isAnimating.value || prefersReducedMotion.value) return;
  
  isAnimating.value = true;
  particles.value = [];
  
  // Clear any existing animation
  if (animationInterval.value) {
    clearInterval(animationInterval.value);
  }
  
  // Animate rocket flying across the screen
  animateRocket();
  
  // Set up repeating animation
  animationInterval.value = setInterval(() => {
    if (isVisible.value && !prefersReducedMotion.value) {
      animateRocket();
    }
  }, animationDuration.value + Math.random() * 15000 + 30000); // 45-60 seconds interval
};

// Stop all animations
const stopRocketAnimation = () => {
  isAnimating.value = false;
  particles.value = [];
  
  if (animationInterval.value) {
    clearInterval(animationInterval.value);
    animationInterval.value = null;
  }
  
  if (rafId.value) {
    cancelAnimationFrame(rafId.value);
    rafId.value = null;
  }
};

// Animate the rocket in a parabolic trajectory
const animateRocket = () => {
  if (!containerRef.value || !rocketRef.value) return;
  
  const container = containerRef.value;
  const rocket = rocketRef.value;
  
  // Animation parameters
  const containerWidth = container.clientWidth;
  const containerHeight = container.clientHeight;
  
  // Randomize start and end points along the bottom of the container
  const startX = -60; // Just outside left edge
  const endX = containerWidth + 60; // Just outside right edge
  
  // Randomize the peak height (negative is up)
  const peakHeight = -(containerHeight * 0.4 + Math.random() * containerHeight * 0.3);
  
  // Animation timing
  const startTime = performance.now();
  const duration = animationDuration.value;
  
  // Reset particles array
  particles.value = [];
  
  // Animation function
  const animate = (currentTime) => {
    if (!isAnimating.value || !isVisible.value) {
      if (rafId.value) {
        cancelAnimationFrame(rafId.value);
        rafId.value = null;
      }
      return;
    }
    
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    
    if (progress < 1) {
      // Calculate position along parabolic path
      const x = startX + (endX - startX) * progress;
      const y = containerHeight * 0.7 + peakHeight * Math.sin(progress * Math.PI);
      
      // Update rocket position
      rocket.style.transform = `translate(${x}px, ${y}px) rotate(${getRotationAngle(progress)}deg)`;
      
      // Only create particles while in the visible area
      if (x > 0 && x < containerWidth) {
        createParticle(x, y);
      }
      
      // Update existing particles
      updateParticles(currentTime);
      
      // Continue animation
      rafId.value = requestAnimationFrame(animate);
    } else {
      // Animation complete
      rocket.style.transform = 'translate(-100px, -100px)'; // Hide rocket outside view
      
      // Continue updating particles until they fade out
      const particleCleanup = (time) => {
        if (particles.value.length > 0) {
          updateParticles(time);
          rafId.value = requestAnimationFrame(particleCleanup);
        } else {
          if (rafId.value) {
            cancelAnimationFrame(rafId.value);
            rafId.value = null;
          }
        }
      };
      
      rafId.value = requestAnimationFrame(particleCleanup);
    }
  };
  
  // Start animation
  rafId.value = requestAnimationFrame(animate);
};

// Calculate rocket rotation based on its trajectory
const getRotationAngle = (progress) => {
  // Angle changes from -30° to 0° to 30° through the animation
  // At start: pointing up, middle: level, end: pointing up again
  return -40 + 80 * Math.sin(progress * Math.PI);
};

// Create a new exhaust particle
const createParticle = (x, y) => {
  if (!containerRef.value || particles.value.length > 100) return;
  
  // Only create particles occasionally for performance
  if (Math.random() > 0.3) return;
  
  const now = performance.now();
  
  // Create particle with random properties
  particles.value.push({
    id: now + Math.random(),
    x: x - 10, // Offset to rocket exhaust position
    y: y,
    size: 2 + Math.random() * 4,
    opacity: 0.6 + Math.random() * 0.4,
    velocityX: -2 - Math.random() * 2,
    velocityY: (Math.random() - 0.5) * 2,
    birthtime: now,
    hue: Math.random() > 0.7 ? 280 : 290, // Purple/pink variations
  });
};

// Update all particle positions and lifetimes
const updateParticles = (currentTime) => {
  particles.value = particles.value.filter(particle => {
    const age = currentTime - particle.birthtime;
    
    // Remove particles that have lived their lifetime
    if (age > particleLifetime) return false;
    
    // Update particle properties based on age
    const lifeProgress = age / particleLifetime;
    
    // Gradually reduce opacity and size with age
    particle.opacity = particle.opacity * (1 - lifeProgress * 0.8);
    particle.size = particle.size * (1 - lifeProgress * 0.3);
    
    // Update position
    particle.x += particle.velocityX;
    particle.y += particle.velocityY;
    
    return true;
  });
};
</script>

<template>
  <div ref="containerRef" class="absolute inset-0 overflow-hidden pointer-events-none">
    <!-- Rocket -->
    <div 
      ref="rocketRef" 
      class="absolute w-12 h-12 -translate-x-full -translate-y-full sm:w-14 sm:h-14 md:w-16 md:h-16"
      aria-hidden="true"
    >
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        viewBox="0 0 24 24" 
        fill="none" 
        stroke="currentColor" 
        stroke-width="2" 
        stroke-linecap="round" 
        stroke-linejoin="round"
        class="w-full h-full text-primary transform rotate-45"
      >
        <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path>
        <path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path>
        <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path>
        <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path>
      </svg>
      
      <!-- Engine glow -->
      <div class="absolute bottom-0 left-1/2 w-4 h-4 -ml-2 rounded-full bg-primary opacity-70 blur-sm"></div>
    </div>
    
    <!-- Particles -->
    <div 
      v-for="particle in particles" 
      :key="particle.id" 
      class="absolute rounded-full" 
      :style="{
        left: `${particle.x}px`,
        top: `${particle.y}px`,
        width: `${particle.size}px`,
        height: `${particle.size}px`,
        opacity: particle.opacity,
        backgroundColor: `hsla(${particle.hue}, 90%, 65%, 1)`,
        boxShadow: `0 0 ${particle.size * 2}px hsla(${particle.hue}, 90%, 65%, 0.8)`
      }"
    ></div>
  </div>
</template>

<style>
@media (prefers-reduced-motion: reduce) {
  /* Hide animation for users who prefer reduced motion */
  .rocket-animation {
    display: none;
  }
}
</style>

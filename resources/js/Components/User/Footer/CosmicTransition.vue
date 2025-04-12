
<script setup>
import { ref, watch, onMounted } from 'vue';
import { useResizeObserver } from '@vueuse/core';

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  }
});

const transitionRef = ref(null);
const stars = ref([]);
const starCount = 50;

// Generate random stars
const generateStars = () => {
  if (!transitionRef.value) return;
  
  const width = transitionRef.value.clientWidth;
  const height = transitionRef.value.clientHeight;
  
  stars.value = Array.from({ length: starCount }, () => ({
    x: Math.random() * width,
    y: Math.random() * height,
    size: 0.5 + Math.random() * 1.5,
    opacity: 0.1 + Math.random() * 0.7,
    speed: 0.2 + Math.random() * 0.5
  }));
};

// Resize handler for stars
const handleResize = () => {
  generateStars();
};

onMounted(() => {
  generateStars();
  if (transitionRef.value) {
    useResizeObserver(transitionRef, handleResize);
  }
});

// Add shooting stars when the transition becomes visible
watch(() => props.isVisible, (newVal) => {
  if (newVal) {
    // Trigger shooting stars when becoming visible
    addShootingStar();
  }
});

// Function to add a shooting star with random timing
const addShootingStar = () => {
  if (!props.isVisible) return;
  
  // Create a shooting star at a random position
  const star = document.createElement('div');
  star.className = 'absolute w-0.5 h-0.5 bg-primary rounded-full shooting-star';
  
  // Random position and angle
  const startX = Math.random() * 80 + 10; // 10-90% width
  const startY = Math.random() * 30; // Top 30%
  const angle = 30 + Math.random() * 60; // 30-90 degrees
  const speed = 0.5 + Math.random() * 1.5;
  
  star.style.left = `${startX}%`;
  star.style.top = `${startY}%`;
  star.style.setProperty('--angle', `${angle}deg`);
  star.style.setProperty('--speed', `${speed}s`);
  
  if (transitionRef.value) {
    transitionRef.value.appendChild(star);
    
    // Remove after animation completes
    setTimeout(() => {
      if (star.parentNode) {
        star.parentNode.removeChild(star);
      }
    }, speed * 1000 + 200);
    
    // Schedule next shooting star
    setTimeout(addShootingStar, 2000 + Math.random() * 5000);
  }
};
</script>

<template>
  <div 
    ref="transitionRef"
    class="absolute bottom-full left-0 w-full h-[20vh] pointer-events-none transition-opacity duration-700"
    :class="{ 'opacity-100': isVisible, 'opacity-0': !isVisible }"
  >
    <!-- Radial gradient for space entry effect -->
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-content_background/50 to-dark-lighter"></div>
    
    <!-- Stars layer -->
    <div 
      v-for="(star, index) in stars" 
      :key="index"
      class="absolute rounded-full bg-white"
      :style="{
        left: `${star.x}px`,
        top: `${star.y}px`,
        width: `${star.size}px`,
        height: `${star.size}px`,
        opacity: star.opacity,
        transform: 'translateY(0)',
        animation: `twinkle ${1 + star.speed}s infinite alternate`
      }"
    ></div>
  </div>
</template>

<style>
@keyframes twinkle {
  0% { opacity: 0.1; }
  100% { opacity: 0.7; }
}

.shooting-star {
  position: absolute;
  opacity: 0;
  transform-origin: center;
  animation: shootingStar var(--speed) ease-out forwards;
  box-shadow: 0 0 6px 2px rgba(155, 135, 245, 0.6);
}

@keyframes shootingStar {
  0% {
    opacity: 0;
    width: 0;
    transform: rotate(var(--angle)) translateX(0) scale(0);
  }
  5% {
    opacity: 1;
    width: 50px;
    transform: rotate(var(--angle)) translateX(10px) scale(1);
  }
  80% {
    opacity: 1;
    transform: rotate(var(--angle)) translateX(calc(100vw - 100px)) scale(1);
  }
  100% {
    opacity: 0;
    transform: rotate(var(--angle)) translateX(100vw) scale(0.5);
  }
}
</style>

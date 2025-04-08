
<template>
  <div class="meteor-container absolute inset-0 overflow-hidden pointer-events-none">
    <div 
      v-for="(meteor, index) in meteors" 
      :key="index"
      class="meteor absolute rounded-full bg-white"
      :class="[`meteor-${index + 1}`]"
      :style="{
        left: `${meteor.left}px`,
        width: `${meteor.size}px`,
        height: `${meteor.size}px`,
        top: '-20px',
        transform: 'rotate(215deg)',
        animationDelay: `${meteor.delay}s`,
        animationDuration: `${meteor.duration}s`,
        boxShadow: `0 0 ${meteor.glow}px rgba(155, 135, 245, 0.8)`,
      }"
    >
      <!-- Meteor trail -->
      <div
        class="absolute top-1/2 left-0 h-[1px] transform -translate-y-1/2"
        :style="{
          width: `${meteor.trailLength}px`,
          background: 'linear-gradient(to right, white, transparent)'
        }"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const positions = [
  { left: 1130, delay: 0.686975, duration: 8 },
  { left: -350, delay: 0.670151, duration: 8 },
  { left: 563, delay: 0.632454, duration: 9 },
  { left: -969, delay: 0.524996, duration: 5 },
  { left: -1153, delay: 0.460272, duration: 8 },
  { left: -560, delay: 0.223791, duration: 6 },
  { left: -1287, delay: 0.406558, duration: 4 },
  { left: 211, delay: 0.475533, duration: 6 },
  { left: -63, delay: 0.394929, duration: 5 },
  { left: -112, delay: 0.78249, duration: 2 },
  { left: 946, delay: 0.353787, duration: 5 },
  { left: 275, delay: 0.309607, duration: 5 },
  { left: 1216, delay: 0.35162, duration: 8 },
  { left: -210, delay: 0.413144, duration: 7 },
  { left: -842, delay: 0.395388, duration: 6 },
  { left: -323, delay: 0.582248, duration: 4 },
  { left: 278, delay: 0.710367, duration: 4 },
  { left: -736, delay: 0.564896, duration: 6 },
  { left: -800, delay: 0.206357, duration: 7 },
  { left: -1118, delay: 0.628613, duration: 9 },
];

// Enhanced meteor properties
const meteors = computed(() => {
  return positions.map(position => {
    const size = Math.random() * 2 + 1; // Random size between 1-3px
    const trailLength = Math.floor(Math.random() * 60 + 40); // Random trail length
    const glow = Math.floor(Math.random() * 6 + 2); // Random glow size
    
    return {
      ...position,
      size,
      trailLength,
      glow,
      opacity: Math.random() * 0.5 + 0.5 // Random opacity
    };
  });
});
</script>

<style scoped>
.meteor-container {
  transform: translateZ(0); /* Hardware acceleration */
}

.meteor {
  animation: meteor-effect linear infinite;
  animation-fill-mode: forwards;
}

@keyframes meteor-effect {
  0% {
    opacity: 0;
    transform: rotate(215deg) translateX(0);
  }
  5% {
    opacity: 1;
  }
  10% {
    opacity: var(--opacity, 0.8);
    transform: rotate(215deg) translateX(0);
  }
  80% {
    opacity: var(--opacity, 0.8);
  }
  100% {
    opacity: 0;
    transform: rotate(215deg) translateX(1000px);
  }
}

/* Create different trajectories for some meteors */
.meteor-3, .meteor-7, .meteor-11, .meteor-15, .meteor-19 {
  animation-name: meteor-effect-curved;
}

@keyframes meteor-effect-curved {
  0% {
    opacity: 0;
    transform: rotate(215deg) translateX(0) translateY(0);
  }
  5% {
    opacity: 1;
  }
  10% {
    opacity: var(--opacity, 0.8);
    transform: rotate(215deg) translateX(50px) translateY(20px);
  }
  80% {
    opacity: var(--opacity, 0.8);
  }
  100% {
    opacity: 0;
    transform: rotate(215deg) translateX(1000px) translateY(80px);
  }
}

@media (max-width: 768px) {
  .meteor:nth-child(n+16) {
    display: none; /* Limit meteors on mobile */
  }
}
</style>


<script setup>
import { ref, onMounted } from 'vue';

const currentYear = new Date().getFullYear();
const copyrightRef = ref(null);
const meteors = ref([]);

// Create random meteor effects
onMounted(() => {
  if (copyrightRef.value) {
    addMeteors();
  }
});

// Add meteor decorations
const addMeteors = () => {
  if (!copyrightRef.value) return;
  
  const width = copyrightRef.value.clientWidth;
  
  // Create 2 random meteors
  meteors.value = Array.from({ length: 2 }, () => ({
    left: Math.random() * 80 + 10, // 10-90%
    width: 30 + Math.random() * 50, // 30-80px
    angle: -30 + Math.random() * 60, // -30 to 30 degrees
    opacity: 0.1 + Math.random() * 0.2,
    delay: Math.random() * 2 // 0-2s
  }));
};
</script>

<template>
  <div 
    ref="copyrightRef"
    class="relative mt-12 border-t border-primary/10 pt-6 text-center text-gray-500 text-sm"
  >
    <!-- Meteor decorations -->
    <div 
      v-for="(meteor, index) in meteors"
      :key="index"
      class="absolute top-0 h-px bg-gradient-to-r from-transparent via-primary/40 to-transparent"
      :style="{
        left: `${meteor.left}%`,
        width: `${meteor.width}px`,
        transform: `translateY(8px) rotate(${meteor.angle}deg)`,
        opacity: meteor.opacity,
        animationDelay: `${meteor.delay}s`
      }"
    ></div>
    
    <p class="mb-2">© {{ currentYear }} NaelStore. All rights reserved.</p>
    <p class="text-xs">Crafted with cosmic energy across the digital universe</p>
  </div>
</template>

<style>
@keyframes meteor {
  0% {
    transform: translateX(-100%) translateY(0) rotate(10deg);
    opacity: 0;
  }
  10% {
    opacity: 0.3;
  }
  100% {
    transform: translateX(100%) translateY(20px) rotate(10deg);
    opacity: 0;
  }
}
</style>

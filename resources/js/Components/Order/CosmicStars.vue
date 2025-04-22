
<template>
  <div class="cosmic-stars">
    <div v-for="i in starCount" :key="i" class="star" :style="getStarStyle(i)"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  count: {
    type: Number,
    default: 40
  },
  size: {
    type: String,
    default: 'small' // small, medium, large
  }
});

const starCount = computed(() => props.count);

const getStarStyle = (index) => {
  const size = {
    small: { min: 1, max: 2 },
    medium: { min: 2, max: 3 },
    large: { min: 3, max: 4 },
  }[props.size];
  
  const randomSize = Math.random() * (size.max - size.min) + size.min;
  const randomX = Math.random() * 100;
  const randomY = Math.random() * 100;
  const randomDelay = Math.random() * 5;
  const randomDuration = 2 + Math.random() * 3;
  
  return {
    width: `${randomSize}px`,
    height: `${randomSize}px`,
    left: `${randomX}%`,
    top: `${randomY}%`,
    animationDelay: `${randomDelay}s`,
    animationDuration: `${randomDuration}s`,
  };
};
</script>

<style scoped>
.cosmic-stars {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
  pointer-events: none;
}

.star {
  position: absolute;
  background: #ffffff;
  border-radius: 50%;
  opacity: 0.5;
  animation: twinkle ease-in-out infinite;
}

@keyframes twinkle {
  0%, 100% { opacity: 0.2; }
  50% { opacity: 0.7; }
}
</style>

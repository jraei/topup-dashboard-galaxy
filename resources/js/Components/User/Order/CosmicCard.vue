
<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  progress: {
    type: Number,
    default: 0
  }
});
</script>

<template>
  <div class="cosmic-card relative overflow-hidden rounded-lg border border-primary/30 bg-dark-card">
    <!-- Nebula Background -->
    <div class="absolute inset-0 opacity-10 pointer-events-none">
      <div class="nebula-bg"></div>
    </div>
    
    <!-- Card Header with Progress -->
    <div class="card-header relative p-4 border-b border-gray-700 bg-dark-lighter">
      <h2 class="text-lg font-medium text-white">{{ title }}</h2>
      
      <!-- Cosmic Progress Bar -->
      <div class="mt-2 h-2 w-full bg-gray-700 rounded-full overflow-hidden">
        <div 
          class="h-full rounded-full cosmic-progress transition-all duration-500 ease-out"
          :style="{ width: `${progress}%` }"
        >
          <!-- Star particles on active progress -->
          <div class="star-particles"></div>
        </div>
      </div>
    </div>
    
    <!-- Card Body -->
    <div class="card-body p-6 relative z-10">
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>
.cosmic-card {
  transition: transform 0.3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
  will-change: transform;
}

.cosmic-card:hover {
  transform: translateY(-2px);
}

.nebula-bg {
  background-image: radial-gradient(circle at 50% 50%, rgba(155, 135, 245, 0.3), transparent 70%),
                    radial-gradient(circle at 80% 20%, rgba(51, 195, 240, 0.2), transparent 50%);
  width: 100%;
  height: 100%;
  mix-blend-mode: screen;
  animation: nebula-shift 20s ease-in-out infinite alternate;
}

.cosmic-progress {
  background: linear-gradient(90deg, #9b87f5, #33C3F0);
  position: relative;
  overflow: hidden;
}

.star-particles {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    radial-gradient(circle at 20% 50%, white 1px, transparent 1px),
    radial-gradient(circle at 40% 50%, white 1px, transparent 1px),
    radial-gradient(circle at 60% 50%, white 1px, transparent 1px),
    radial-gradient(circle at 80% 50%, white 1px, transparent 1px);
  background-size: 100% 100%;
  animation: star-pulse 3s ease-in-out infinite;
}

@keyframes nebula-shift {
  0% { transform: scale(1) rotate(0deg); }
  100% { transform: scale(1.1) rotate(5deg); }
}

@keyframes star-pulse {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 1; }
}
</style>

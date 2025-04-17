
<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
  checked: {
    type: Boolean,
    default: false,
  },
});

const proxyChecked = computed({
  get() {
    return props.checked;
  },
  set(val) {
    emit('update:checked', val);
  },
});
</script>

<template>
  <div class="cosmic-checkbox">
    <input
      type="checkbox"
      id="cosmicCheckbox"
      v-model="proxyChecked"
      class="sr-only" 
    />
    <label for="cosmicCheckbox" class="inline-flex cursor-pointer">
      <div 
        class="relative w-6 h-6 flex items-center justify-center rounded border-2 transition-colors duration-300" 
        :class="[proxyChecked ? 'border-primary bg-primary/20' : 'border-gray-500 bg-dark-card']"
      >
        <!-- Binary Star System Effect -->
        <div 
          class="binary-star transition-all duration-300"
          :class="{ 'active': proxyChecked }"
        ></div>
        
        <!-- Checkmark -->
        <svg 
          class="w-3.5 h-3.5 text-white transition-opacity duration-300" 
          :class="{ 'opacity-0': !proxyChecked, 'opacity-100': proxyChecked }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24" 
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
        </svg>
      </div>
    </label>
  </div>
</template>

<style scoped>
.binary-star {
  position: absolute;
  width: 3px;
  height: 3px;
  border-radius: 50%;
  background-color: #33C3F0;
  opacity: 0;
  transform: translateX(-4px);
  box-shadow: 0 0 5px #33C3F0;
}

.binary-star.active {
  opacity: 1;
  transform: translateX(4px);
  background-color: #9b87f5;
  box-shadow: 0 0 5px #9b87f5, 0 0 10px #9b87f5;
}
</style>

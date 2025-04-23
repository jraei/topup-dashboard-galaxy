
<script setup>
defineProps({ counts: Object });
</script>
<template>
  <div class="grid grid-cols-3 gap-4">
    <div v-for="status in ['completed', 'processing', 'failed']" :key="status" class="flex flex-col items-center justify-center relative">
      <div class="w-16 h-16 rounded-full cosmic-glow cosmic-ring flex items-center justify-center text-lg font-bold"
           :class="{
             'border-green-400': status==='completed',
             'border-yellow-400': status==='processing',
             'border-supernova': status==='failed'
           }">
        {{ counts[status] ?? 0 }}
      </div>
      <div class="mt-2 uppercase text-xs text-secondary">{{ status }}</div>
    </div>
  </div>
</template>

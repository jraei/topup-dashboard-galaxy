
<script setup>
defineProps({ activities: Array });
</script>
<template>
  <ol class="flex flex-col gap-2 cosmic-feed">
    <li v-for="a in activities" :key="a.id" class="flex items-center space-x-2">
      <span :class="['rounded-full w-3 h-3', a.type === 'payment' ? 'bg-supernova' : 'bg-black']"></span>
      <span class="font-mono text-sm text-primary">{{ a.order_id }}</span>
      <span class="text-xs text-secondary">{{ a.status }}</span>
      <span class="text-xs text-secondary">{{ a.created_at }}</span>
    </li>
  </ol>
</template>

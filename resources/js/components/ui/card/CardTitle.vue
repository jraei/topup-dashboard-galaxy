
<script setup>
defineProps({
  class: {
    type: String,
    default: ''
  }
});
</script>

<template>
  <h3 class="text-2xl font-semibold leading-none tracking-tight" :class="class">
    <slot></slot>
  </h3>
</template>

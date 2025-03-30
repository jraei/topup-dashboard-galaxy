
<script setup>
defineProps({
  class: {
    type: String,
    default: ''
  }
});
</script>

<template>
  <div class="rounded-lg border shadow-sm w-full" :class="class">
    <slot></slot>
  </div>
</template>

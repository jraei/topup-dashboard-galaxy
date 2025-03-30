
<script setup>
defineProps({
  class: {
    type: String,
    default: ''
  }
});
</script>

<template>
  <div class="p-6 pt-0" :class="class">
    <slot></slot>
  </div>
</template>

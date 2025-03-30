
<script setup>
defineProps({
  class: {
    type: String,
    default: ''
  },
  variant: {
    type: String,
    default: 'default'
  }
});

const variantStyles = {
  default: 'bg-primary/10 text-primary hover:bg-primary/20',
  secondary: 'bg-secondary/10 text-secondary hover:bg-secondary/20',
  destructive: 'bg-destructive/10 text-destructive hover:bg-destructive/20',
  outline: 'border border-gray-700 bg-transparent',
  success: 'bg-green-500/10 text-green-400 hover:bg-green-500/20',
  warning: 'bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20',
  error: 'bg-red-500/10 text-red-400 hover:bg-red-500/20',
};
</script>

<template>
  <div :class="[
    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors',
    variantStyles[variant],
    class
  ]">
    <slot></slot>
  </div>
</template>

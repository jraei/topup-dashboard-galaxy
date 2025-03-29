
<script setup>
import { computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { 
  TrendingUp, 
  TrendingDown 
} from "lucide-vue-next";

// Props
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  icon: {
    type: Object,
    required: true
  },
  iconColor: {
    type: String,
    default: 'text-primary'
  },
  growthPercent: {
    type: Number,
    default: 0
  },
  isPositive: {
    type: Boolean,
    default: true
  },
  currency: {
    type: String,
    default: ''
  }
});

// Format value based on whether it's a currency or not
const formattedValue = computed(() => {
  if (props.currency) {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: props.currency
    }).format(props.value);
  }
  
  // Regular number formatting with thousands separator
  return typeof props.value === 'number' 
    ? props.value.toLocaleString()
    : props.value;
});

// Determine glow effect class based on the iconColor
const glowEffectClass = computed(() => {
  if (props.iconColor.includes('primary')) {
    return 'hover:shadow-glow-primary';
  }
  if (props.iconColor.includes('secondary')) {
    return 'hover:shadow-glow-secondary';
  }
  return 'hover:shadow-glow-primary';
});
</script>

<template>
  <Card class="bg-gradient-to-br from-dark-card to-dark-lighter border-gray-700 transition-all" :class="glowEffectClass">
    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
      <CardTitle class="text-sm font-medium text-gray-400">{{ title }}</CardTitle>
      <component :is="icon" class="h-4 w-4 text-muted-foreground" :class="iconColor" />
    </CardHeader>
    <CardContent>
      <div class="text-2xl font-bold text-white">{{ formattedValue }}</div>
      <p :class="[
        'text-xs',
        isPositive ? 'text-green-400' : 'text-red-400'
      ]">
        <TrendingUp v-if="isPositive" class="inline-block mr-1 h-3 w-3" />
        <TrendingDown v-else class="inline-block mr-1 h-3 w-3" />
        {{ Math.abs(growthPercent) }}% 
        {{ isPositive ? 'increase' : 'decrease' }}
      </p>
    </CardContent>
  </Card>
</template>

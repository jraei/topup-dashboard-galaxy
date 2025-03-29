
<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: String,
    default: null
  },
  color: {
    type: String,
    default: 'primary' // primary, secondary, success, warning, danger, info
  },
  trend: {
    type: Number,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const getIconColor = (color) => {
  const colors = {
    primary: 'bg-primary-600/30 text-primary-400',
    secondary: 'bg-secondary-600/30 text-secondary-400',
    success: 'bg-green-600/30 text-green-400',
    warning: 'bg-yellow-600/30 text-yellow-400',
    danger: 'bg-red-600/30 text-red-400',
    info: 'bg-blue-600/30 text-blue-400'
  };
  
  return colors[color] || colors.primary;
};

const getTrendColor = (trend) => {
  if (trend > 0) return 'text-green-400';
  if (trend < 0) return 'text-red-400';
  return 'text-gray-400';
};

const getTrendIcon = (trend) => {
  if (trend > 0) return 'trending-up';
  if (trend < 0) return 'trending-down';
  return 'minus';
};
</script>

<template>
  <div class="relative overflow-hidden rounded-xl bg-gradient-card border border-gray-700 shadow-lg hover:shadow-glow transition-shadow duration-300">
    <div class="absolute top-0 right-0 w-20 h-20 -mt-6 -mr-6 bg-glow opacity-30"></div>
    
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-gray-400 font-medium text-sm">{{ title }}</h3>
        
        <div v-if="icon" :class="['p-3 rounded-lg', getIconColor(color)]">
          <component :is="icon" class="h-5 w-5" />
        </div>
      </div>
      
      <div v-if="loading" class="animate-pulse">
        <div class="h-9 bg-gray-700 rounded w-3/4 mb-1"></div>
        <div class="h-4 bg-gray-700 rounded w-1/2"></div>
      </div>
      
      <div v-else>
        <div class="text-2xl md:text-3xl font-bold text-white mb-1">{{ value }}</div>
        
        <div v-if="trend !== null" class="flex items-center space-x-2">
          <span :class="['text-sm font-medium', getTrendColor(trend)]">
            {{ trend > 0 ? '+' : '' }}{{ trend }}%
          </span>
          
          <svg v-if="trend > 0" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
            <polyline points="17 6 23 6 23 12" />
          </svg>
          
          <svg v-else-if="trend < 0" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 18 13.5 8.5 8.5 13.5 1 6" />
            <polyline points="17 18 23 18 23 12" />
          </svg>
          
          <span v-if="trend !== 0" class="text-xs text-gray-400">vs last period</span>
        </div>
      </div>
    </div>
  </div>
</template>

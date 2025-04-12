
<script setup>
import { computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import CosmicIcon from '@/Components/User/Navigation/CosmicIcon.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  quickLinks: {
    type: Array,
    default: () => []
  },
  supportLinks: {
    type: Array,
    default: () => []
  },
  socialLinks: {
    type: Array,
    default: () => []
  }
});

// Responsive layout helper
const columns = computed(() => {
  return {
    logo: 'col-span-full sm:col-span-1 md:col-span-1',
    links: 'col-span-full sm:col-span-1 md:col-span-1',
    social: 'col-span-full sm:col-span-2 md:col-span-1'
  };
});
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-8">
    <!-- Logo column -->
    <div :class="columns.logo" class="flex flex-col items-center md:items-start">
      <div class="relative">
        <div class="absolute inset-0 blur-md bg-primary/10 rounded-full"></div>
        <ApplicationLogo class="relative z-10 w-28 h-auto text-primary" />
      </div>
      <p class="mt-4 text-center md:text-left text-gray-300 text-sm max-w-xs">
        Your gateway to the digital universe. Fast, secure, and stellar service guaranteed.
      </p>
    </div>
    
    <!-- Quick Links column -->
    <div :class="columns.links" class="flex flex-col">
      <h3 class="text-xl font-semibold text-primary-text mb-4 text-center md:text-left">Quick Links</h3>
      <ul class="space-y-3 flex flex-col items-center md:items-start">
        <li v-for="link in quickLinks" :key="link.name">
          <Link 
            :href="route(link.route)" 
            class="flex items-center text-gray-300 hover:text-primary-text transition-colors duration-200 group"
          >
            <CosmicIcon :name="link.icon" class="mr-2 w-5 h-5 text-primary/70 group-hover:text-primary" />
            <span>{{ link.name }}</span>
            
            <!-- Glow effect on hover -->
            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary opacity-0 group-hover:w-full group-hover:opacity-100 transition-all duration-300"></span>
          </Link>
        </li>
      </ul>
    </div>
    
    <!-- Support Links column -->
    <div :class="columns.links" class="flex flex-col">
      <h3 class="text-xl font-semibold text-primary-text mb-4 text-center md:text-left">Support</h3>
      <ul class="space-y-3 flex flex-col items-center md:items-start">
        <li v-for="link in supportLinks" :key="link.name">
          <Link 
            :href="route(link.route)" 
            class="flex items-center text-gray-300 hover:text-primary-text transition-colors duration-200 group"
          >
            <CosmicIcon :name="link.icon" class="mr-2 w-5 h-5 text-primary/70 group-hover:text-primary" />
            <span>{{ link.name }}</span>
            
            <!-- Glow effect on hover -->
            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary opacity-0 group-hover:w-full group-hover:opacity-100 transition-all duration-300"></span>
          </Link>
        </li>
      </ul>
    </div>
    
    <!-- Social Links column -->
    <div :class="columns.social" class="flex flex-col">
      <h3 class="text-xl font-semibold text-primary-text mb-4 text-center md:text-left">Connect With Us</h3>
      
      <div class="flex justify-center md:justify-start space-x-4">
        <a 
          v-for="social in socialLinks" 
          :key="social.name"
          :href="social.url"
          class="group relative"
          aria-label="social.name"
        >
          <!-- Planet-styled social button -->
          <div class="w-12 h-12 rounded-full flex items-center justify-center bg-dark-card group-hover:bg-dark-card/80 transition-colors duration-200 relative overflow-hidden">
            <!-- Orbiting satellite effect -->
            <div class="absolute w-2 h-2 rounded-full bg-secondary opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-orbit"></div>
            
            <!-- Icon -->
            <CosmicIcon :name="social.icon" class="w-6 h-6 text-primary group-hover:text-primary-text transition-colors duration-200" />
            
            <!-- Background glow -->
            <div class="absolute inset-0 rounded-full bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </div>
        </a>
      </div>
      
      <!-- Newsletter signup -->
      <div class="mt-6 w-full max-w-xs mx-auto md:mx-0">
        <h4 class="text-sm font-medium text-gray-400 mb-2 text-center md:text-left">Join our cosmic newsletter</h4>
        <div class="flex">
          <input 
            type="email" 
            placeholder="your@email.com" 
            class="px-3 py-2 bg-dark-card text-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-primary flex-grow"
          >
          <button class="px-3 py-2 bg-primary text-primary-text rounded-r-md hover:bg-primary/90 transition-colors duration-200">
            <CosmicIcon name="send" class="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

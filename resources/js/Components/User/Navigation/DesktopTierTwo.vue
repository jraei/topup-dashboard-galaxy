
<script setup>
import { ref, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
  navLinks: {
    type: Array,
    required: true
  }
});

const activeDropdown = ref(null);

const toggleDropdown = (index) => {
  if (activeDropdown.value === index) {
    activeDropdown.value = null;
  } else {
    activeDropdown.value = index;
  }
};

const closeDropdown = () => {
  activeDropdown.value = null;
};
</script>

<template>
  <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-14">
      <!-- Left: Navigation Links -->
      <div class="flex space-x-4">
        <div 
          v-for="(link, index) in navLinks" 
          :key="index"
          class="relative"
          @mouseleave="closeDropdown"
        >
          <button
            v-if="link.dropdown"
            @click="toggleDropdown(index)"
            @mouseenter="toggleDropdown(index)"
            class="flex items-center px-3 py-2 space-x-1 text-sm text-gray-200 transition-all rounded-md group hover:bg-primary/10 hover:text-primary"
            :class="{ 'text-primary bg-primary/5': link.active || activeDropdown === index }"
          >
            <span class="text-lg mr-1.5">{{ link.icon }}</span>
            <span>{{ link.name }}</span>
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': activeDropdown === index }"
              viewBox="0 0 20 20" 
              fill="currentColor"
            >
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            
            <!-- Hover effect: orbiting planets -->
            <div class="absolute -z-10 inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <div class="absolute top-1 right-1 w-1 h-1 bg-primary rounded-full animate-pulse-slow"></div>
              <div class="absolute bottom-1 left-2 w-1.5 h-1.5 bg-secondary rounded-full animate-ping"></div>
            </div>
          </button>
          
          <NavLink 
            v-else
            :href="route(link.route)"
            :active="link.active"
            class="flex items-center px-3 py-2 space-x-1 text-sm text-gray-200 transition-all rounded-md group hover:bg-primary/10 hover:text-primary relative"
          >
            <span class="text-lg mr-1.5">{{ link.icon }}</span>
            <span>{{ link.name }}</span>
            
            <!-- Hover effect: orbiting planets -->
            <div class="absolute -z-10 inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <div class="absolute top-1 right-1 w-1 h-1 bg-primary rounded-full animate-pulse-slow"></div>
              <div class="absolute bottom-1 left-2 w-1.5 h-1.5 bg-secondary rounded-full animate-ping"></div>
            </div>
          </NavLink>
          
          <!-- Dropdown menu -->
          <div
            v-if="link.dropdown && activeDropdown === index"
            class="absolute top-full mt-1 w-48 z-50 transition-all duration-300 origin-top-right"
          >
            <div class="bg-content_background rounded-md shadow-glow-primary border border-primary/20 overflow-hidden">
              <div class="py-1">
                <Link
                  v-for="(item, itemIndex) in link.dropdown"
                  :key="itemIndex"
                  :href="route(item.route)"
                  class="flex items-center gap-2 px-4 py-2 text-sm text-gray-200 transition-all hover:bg-primary/10 hover:text-primary"
                >
                  <span class="text-lg">{{ item.icon }}</span>
                  <span>{{ item.name }}</span>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Right: Auth Buttons -->
      <div class="flex items-center space-x-3">
        <Link
          :href="route('login')"
          class="flex items-center px-4 py-2 text-sm font-medium text-gray-200 transition-all rounded-md bg-dark/card hover:bg-primary/20 hover:text-white"
        >
          <span class="mr-1.5">👤</span>
          <span>Login</span>
        </Link>
        
        <Link
          :href="route('register')"
          class="flex items-center px-4 py-2 text-sm font-medium text-white transition-all rounded-md bg-primary hover:bg-primary-hover"
        >
          <span class="mr-1.5">🚀</span>
          <span>Register</span>
        </Link>
      </div>
    </div>
  </div>
</template>

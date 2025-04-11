
<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import CosmicSearch from "./CosmicSearch.vue";

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  toggleMenu: {
    type: Function,
    required: true
  },
  closeMenu: {
    type: Function,
    required: true
  },
  navLinks: {
    type: Array,
    required: true
  }
});

const showSearch = ref(false);
const isSearchFocused = ref(false);

const toggleSearch = () => {
  showSearch.value = !showSearch.value;
};
</script>

<template>
  <div>
    <!-- Mobile Header -->
    <div class="px-4 py-3 mx-auto max-w-7xl">
      <div class="flex items-center justify-between">
        <!-- Left: Logo -->
        <div class="flex items-center flex-shrink-0">
          <Link :href="route('dashboard')">
            <ApplicationLogo class="w-auto h-8 text-primary" />
          </Link>
        </div>
        
        <!-- Right: Controls -->
        <div class="flex items-center space-x-4">
          <!-- Search Icon -->
          <button
            class="flex items-center justify-center w-10 h-10 text-gray-300 transition-all rounded-full hover:bg-primary/10"
            @click="toggleSearch"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
          
          <!-- Language -->
          <button class="flex items-center text-lg">
            🇮🇩
          </button>
          
          <!-- Menu Toggle (Hamburger) -->
          <button
            class="relative flex items-center justify-center w-10 h-10 text-gray-300 transition-all rounded-full hover:bg-primary/10"
            @click="toggleMenu"
          >
            <div v-if="!isOpen" class="flex flex-col items-end space-y-1.5">
              <div class="w-6 h-0.5 bg-gray-300 rounded-full"></div>
              <div class="w-4 h-0.5 bg-gray-300 rounded-full"></div>
              <div class="w-5 h-0.5 bg-gray-300 rounded-full"></div>
            </div>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            
            <!-- Cosmic glow effect -->
            <div class="absolute inset-0 transition-opacity rounded-full opacity-0 bg-primary/20 filter blur-sm hover:opacity-100"></div>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Mobile Search Bar (when activated) -->
    <div 
      v-if="showSearch" 
      class="fixed inset-x-0 top-0 z-50 p-4 transition-all transform bg-content_background"
      :class="{ 'shadow-glow-primary': isSearchFocused }"
    >
      <div class="flex items-center">
        <button 
          class="mr-3 text-gray-300"
          @click="toggleSearch"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </button>
        <div class="flex-1">
          <CosmicSearch 
            @focus="isSearchFocused = true" 
            @blur="isSearchFocused = false"
            :is-focused="isSearchFocused"
          />
        </div>
      </div>
    </div>
    
    <!-- Mobile Menu (Breadcrumb Panel) -->
    <div 
      v-if="isOpen"
      class="fixed inset-0 z-40 flex"
    >
      <!-- Backdrop -->
      <div 
        class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"
        @click="closeMenu"
      ></div>
      
      <!-- Slide-in Panel -->
      <div 
        class="relative flex flex-col w-4/5 max-w-sm h-full overflow-y-auto transition-all duration-300 transform bg-content_background"
      >
        <!-- Panel Header -->
        <div class="flex items-center justify-between p-4 border-b border-primary/20">
          <Link :href="route('dashboard')" @click="closeMenu">
            <ApplicationLogo class="w-auto h-8 text-primary" />
          </Link>
          <button 
            class="flex items-center justify-center w-10 h-10 text-gray-300 rounded-full hover:bg-primary/10"
            @click="closeMenu"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <!-- Navigation Links -->
        <div class="flex-1 px-2 py-4 space-y-1">
          <template v-for="(link, index) in navLinks" :key="index">
            <Link
              :href="route(link.route)"
              class="flex items-center px-4 py-3 text-gray-200 transition-all rounded-md hover:bg-primary/10 hover:text-primary"
              :class="{ 'bg-primary/5 text-primary': link.active }"
              @click="closeMenu"
            >
              <span class="text-xl mr-3">{{ link.icon }}</span>
              <span class="font-medium">{{ link.name }}</span>
            </Link>
            
            <!-- Dropdown items if any -->
            <template v-if="link.dropdown">
              <Link
                v-for="(item, itemIndex) in link.dropdown"
                :key="`${index}-${itemIndex}`"
                :href="route(item.route)"
                class="flex items-center pl-12 pr-4 py-2 text-gray-200 transition-all rounded-md hover:bg-primary/10 hover:text-primary"
                @click="closeMenu"
              >
                <span class="text-lg mr-2">{{ item.icon }}</span>
                <span>{{ item.name }}</span>
              </Link>
            </template>
          </template>
        </div>
        
        <!-- Auth Buttons -->
        <div class="p-4 border-t border-primary/20">
          <div class="grid grid-cols-2 gap-3">
            <Link
              :href="route('login')"
              class="flex items-center justify-center px-4 py-2 text-sm font-medium text-center text-gray-200 transition-all rounded-md bg-dark/card hover:bg-primary/20 hover:text-white"
              @click="closeMenu"
            >
              <span class="mr-1.5">👤</span>
              <span>Login</span>
            </Link>
            
            <Link
              :href="route('register')"
              class="flex items-center justify-center px-4 py-2 text-sm font-medium text-center text-white transition-all rounded-md bg-primary hover:bg-primary-hover"
              @click="closeMenu"
            >
              <span class="mr-1.5">🚀</span>
              <span>Register</span>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<template>
  <nav class="relative z-10">
    <!-- Desktop Navigation (>=768px) -->
    <div class="hidden md:block">
      <!-- Level 1 (Top Bar) -->
      <div class="bg-header-background border-b border-primary/20">
        <div class="cosmic-container h-16 flex items-center justify-between">
          <!-- Left Section (Logo) -->
          <div class="flex-shrink-0 w-[15%]">
            <a href="/" class="block">
              <img 
                src="/storage/logos/logo_header.svg" 
                alt="Logo"
                class="h-8 cosmic-glow" 
                onerror="this.src='/storage/logos/logo_header.png'; this.onerror=null;"
              />
            </a>
          </div>
          
          <!-- Center Section (Search + Language) -->
          <div class="flex items-center space-x-4 w-[65%]">
            <div class="w-[90%]">
              <div class="relative">
                <input 
                  type="text" 
                  placeholder="Explore the cosmos..." 
                  class="cosmic-input"
                  @focus="isSearchFocused = true"
                  @blur="isSearchFocused = false"
                />
                <span 
                  v-if="isSearchFocused" 
                  class="absolute inset-0 -z-10 opacity-50"
                  :class="{'cosmic-pulse': isSearchFocused}"
                ></span>
              </div>
            </div>
            
            <!-- Language Selector -->
            <div class="w-[10%] flex justify-center">
              <div class="relative">
                <button 
                  @click="toggleLanguageDropdown" 
                  class="flex items-center space-x-1 text-white/80 hover:text-primary"
                >
                  <span class="flex h-5 w-5 items-center justify-center overflow-hidden rounded">
                    🇮🇩
                  </span>
                  <span class="sr-only">IDR</span>
                </button>
                
                <!-- Language Dropdown -->
                <div 
                  v-if="isLanguageDropdownOpen" 
                  class="absolute right-0 mt-2 w-32 rounded-md shadow-lg py-1 glass-morphism ring-1 ring-black ring-opacity-5 z-50"
                >
                  <button 
                    class="flex items-center w-full px-4 py-2 text-sm text-white/80 hover:text-primary hover:bg-white/5"
                  >
                    <span class="flex h-5 w-5 items-center justify-center overflow-hidden rounded mr-2">
                      🇮🇩
                    </span>
                    <span>IDR</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Section (Space for Level 2 alignment) -->
          <div class="w-[20%]"></div>
        </div>
      </div>
      
      <!-- Level 2 (Bottom Bar) -->
      <div class="bg-header-background py-2 space-particles">
        <div class="cosmic-container flex items-center justify-between">
          <!-- Left Section (Nav Links) -->
          <div class="flex items-center space-x-2">
            <template v-for="(link, index) in navLinks" :key="index">
              <a 
                :href="link.url" 
                class="nav-link"
                :class="{'active': link.active}"
              >
                <span class="flex items-center gap-1">
                  <span v-html="link.icon"></span>
                  <span>{{ link.label }}</span>
                </span>
                <span 
                  v-if="link.active" 
                  class="absolute bottom-0 left-0 h-0.5 w-full bg-primary/30"
                ></span>
              </a>
              
              <!-- Kalkulator Dropdown -->
              <div v-if="link.dropdown" class="relative">
                <button 
                  @click="toggleCalculatorDropdown" 
                  class="nav-link flex items-center"
                >
                  <span class="flex items-center gap-1">
                    <span v-html="link.icon"></span>
                    <span>{{ link.label }}</span>
                    <svg 
                      xmlns="http://www.w3.org/2000/svg" 
                      width="16" 
                      height="16" 
                      viewBox="0 0 24 24" 
                      fill="none" 
                      stroke="currentColor" 
                      stroke-width="2" 
                      stroke-linecap="round" 
                      stroke-linejoin="round" 
                      class="transition-transform duration-200"
                      :class="{'rotate-180': isCalculatorDropdownOpen}"
                    >
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </span>
                </button>
                
                <!-- Calculator Dropdown Content -->
                <div 
                  v-if="isCalculatorDropdownOpen" 
                  class="absolute left-0 mt-2 w-64 rounded-md shadow-lg glass-morphism ring-1 ring-black ring-opacity-5 z-50"
                >
                  <div class="p-3 space-y-2">
                    <a href="#" class="block p-2 rounded-md hover:bg-primary/10">
                      <div class="flex items-start gap-3">
                        <span class="text-primary text-lg mt-1">🏆</span>
                        <div>
                          <p class="font-medium text-white">Winrate</p>
                          <p class="text-xs text-white/70">Calculate matches needed</p>
                        </div>
                      </div>
                    </a>
                    <a href="#" class="block p-2 rounded-md hover:bg-primary/10">
                      <div class="flex items-start gap-3">
                        <span class="text-primary text-lg mt-1">🎡</span>
                        <div>
                          <p class="font-medium text-white">Magic Wheel</p>
                          <p class="text-xs text-white/70">Diamond estimate</p>
                        </div>
                      </div>
                    </a>
                    <a href="#" class="block p-2 rounded-md hover:bg-primary/10">
                      <div class="flex items-start gap-3">
                        <span class="text-primary text-lg mt-1">♊</span>
                        <div>
                          <p class="font-medium text-white">Zodiac</p>
                          <p class="text-xs text-white/70">Legendary skin cost</p>
                        </div>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </template>
          </div>
          
          <!-- Right Section (Auth Buttons) -->
          <div class="flex items-center space-x-4">
            <a href="/login" class="cosmic-button cosmic-button-outline">
              <span class="text-primary">👤</span>
              <span>Login</span>
            </a>
            <a href="/register" class="cosmic-button cosmic-button-primary">
              <span>🚀</span>
              <span>Register</span>
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mobile Navigation (<768px) -->
    <div class="block md:hidden">
      <div class="bg-header-background border-b border-primary/20">
        <div class="cosmic-container h-16 flex items-center justify-between">
          <!-- Logo (40% width) -->
          <div class="w-[40%]">
            <a href="/" class="block">
              <img 
                src="/storage/logos/logo_header.svg" 
                alt="Logo"
                class="h-8 cosmic-glow" 
                onerror="this.src='/storage/logos/logo_header.png'; this.onerror=null;"
              />
            </a>
          </div>
          
          <!-- Right-aligned icons -->
          <div class="flex items-center space-x-4">
            <button @click="showMobileSearch = true" class="text-white/80 hover:text-primary">
              <span>🔍</span>
            </button>
            <button @click="toggleLanguageDropdown" class="text-white/80 hover:text-primary">
              <span>🇮🇩</span>
            </button>
            <button @click="showMobileMenu = true" class="text-white/80 hover:text-primary">
              <span>☰</span>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Mobile Search Overlay -->
      <div 
        v-if="showMobileSearch" 
        class="fixed inset-0 bg-black/80 z-50 flex items-start pt-16 px-4 animate-fade-in"
      >
        <div class="w-full max-w-md mx-auto space-y-4">
          <div class="relative">
            <input 
              type="text" 
              placeholder="Explore the cosmos..." 
              class="cosmic-input"
              @focus="isSearchFocused = true"
              @blur="isSearchFocused = false"
              ref="mobileSearchInput"
            />
            <button 
              @click="showMobileSearch = false" 
              class="absolute right-3 top-1/2 -translate-y-1/2 text-white/80 hover:text-primary"
            >
              <span>✕</span>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Mobile Menu Overlay -->
      <div 
        v-if="showMobileMenu" 
        class="fixed inset-y-0 left-0 w-[80vw] bg-content-background z-50 animate-slide-in-right space-particles"
      >
        <!-- Header with Logo and Close button -->
        <div class="flex items-center justify-between p-4 border-b border-primary/20">
          <a href="/" class="block">
            <img 
              src="/storage/logos/logo_header.svg" 
              alt="Logo"
              class="h-8 cosmic-glow" 
              onerror="this.src='/storage/logos/logo_header.png'; this.onerror=null;"
            />
          </a>
          <button @click="showMobileMenu = false" class="text-white/80 hover:text-primary">
            <span>✕</span>
          </button>
        </div>
        
        <!-- Nav Links -->
        <div class="p-4 space-y-4">
          <a 
            v-for="(link, index) in navLinks" 
            :key="index"
            :href="link.url" 
            class="flex items-center p-3 rounded-md"
            :class="{'bg-primary/10 text-primary': link.active, 'text-white/80 hover:bg-primary/5 hover:text-primary': !link.active}"
          >
            <span class="flex items-center gap-2">
              <span v-html="link.icon"></span>
              <span>{{ link.label }}</span>
            </span>
          </a>
        </div>
        
        <!-- Auth Buttons -->
        <div class="absolute bottom-0 left-0 w-full p-4 border-t border-primary/20 bg-header-background/50">
          <div class="grid grid-cols-2 gap-4">
            <a href="/login" class="cosmic-button cosmic-button-outline text-center">
              <div class="mx-auto flex items-center justify-center gap-2">
                <span class="text-primary">👤</span>
                <span>Login</span>
              </div>
            </a>
            <a href="/register" class="cosmic-button cosmic-button-primary text-center">
              <div class="mx-auto flex items-center justify-center gap-2">
                <span>🚀</span>
                <span>Register</span>
              </div>
            </a>
          </div>
        </div>
      </div>
      
      <!-- Backdrop for mobile menu -->
      <div 
        v-if="showMobileMenu" 
        @click="showMobileMenu = false"
        class="fixed inset-0 bg-black/50 z-40 animate-fade-in"
      ></div>
      
      <!-- Language Dropdown for Mobile -->
      <div 
        v-if="isLanguageDropdownOpen" 
        class="fixed top-16 right-4 mt-2 w-32 rounded-md shadow-lg py-1 glass-morphism ring-1 ring-black ring-opacity-5 z-50 animate-fade-in"
      >
        <button 
          class="flex items-center w-full px-4 py-2 text-sm text-white/80 hover:text-primary hover:bg-white/5"
        >
          <span class="flex h-5 w-5 items-center justify-center overflow-hidden rounded mr-2">
            🇮🇩
          </span>
          <span>IDR</span>
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';

// Navigation state
const isSearchFocused = ref(false);
const isLanguageDropdownOpen = ref(false);
const isCalculatorDropdownOpen = ref(false);

// Mobile navigation state
const showMobileMenu = ref(false);
const showMobileSearch = ref(false);
const mobileSearchInput = ref(null);

// Navigation links
const navLinks = ref([
  { 
    label: 'Topup', 
    url: '#', 
    icon: '🌌', 
    active: true, 
    dropdown: false 
  },
  { 
    label: 'Cek Transaksi', 
    url: '#', 
    icon: '📊', 
    active: false, 
    dropdown: false 
  },
  { 
    label: 'Leaderboard', 
    url: '#', 
    icon: '🏆', 
    active: false, 
    dropdown: false 
  },
  { 
    label: 'Kalkulator', 
    url: '#', 
    icon: '🌀', 
    active: false, 
    dropdown: true 
  },
]);

// Toggle dropdowns
const toggleLanguageDropdown = () => {
  isLanguageDropdownOpen.value = !isLanguageDropdownOpen.value;
  if (isLanguageDropdownOpen.value) {
    isCalculatorDropdownOpen.value = false;
  }
};

const toggleCalculatorDropdown = () => {
  isCalculatorDropdownOpen.value = !isCalculatorDropdownOpen.value;
  if (isCalculatorDropdownOpen.value) {
    isLanguageDropdownOpen.value = false;
  }
};

// Close dropdowns when clicking outside
onMounted(() => {
  document.addEventListener('click', (event) => {
    const target = event.target;
    
    // Check if click is outside language dropdown
    if (isLanguageDropdownOpen.value && !target.closest('.language-dropdown')) {
      isLanguageDropdownOpen.value = false;
    }
    
    // Check if click is outside calculator dropdown
    if (isCalculatorDropdownOpen.value && !target.closest('.calculator-dropdown')) {
      isCalculatorDropdownOpen.value = false;
    }
  });
});

// Focus the mobile search input when showing mobile search
watch(showMobileSearch, (newVal) => {
  if (newVal) {
    nextTick(() => {
      mobileSearchInput.value?.focus();
    });
  }
});

// Add event listener to close language dropdown when clicked outside
onMounted(() => {
  document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-dropdown]')) {
      isLanguageDropdownOpen.value = false;
      isCalculatorDropdownOpen.value = false;
    }
  });
});
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

.animate-slide-in-right {
  animation: slideInRight 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideInRight {
  from {
    transform: translateX(-100%);
  }
  to {
    transform: translateX(0);
  }
}
</style>

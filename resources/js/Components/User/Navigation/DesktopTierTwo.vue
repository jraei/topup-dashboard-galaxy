
<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';

const props = defineProps({
  navLinks: {
    type: Array,
    default: () => [],
  },
});

// State for dropdown
const openDropdown = ref(null);
const dropdownRef = ref(null);

// Determine if a link has dropdown content
const hasDropdown = (link) => {
  return link.dropdown && link.dropdown.length > 0;
};

// Open/close dropdown
const toggleDropdown = (linkName) => {
  if (openDropdown.value === linkName) {
    openDropdown.value = null;
  } else {
    openDropdown.value = linkName;
  }
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    openDropdown.value = null;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Compute nav link classes based on active state
const getLinkClasses = (link) => {
  return {
    'px-3 py-2 rounded-md flex items-center text-sm font-medium transition-colors duration-200 relative': true,
    'text-primary bg-primary/10': link.active,
    'text-gray-300 hover:text-primary-text hover:bg-primary/10': !link.active,
    'group': true,
  };
};
</script>

<template>
  <div class="bg-dark-lighter py-2 border-b border-primary/20">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="flex items-center justify-between">
        <!-- Navigation Links -->
        <div class="flex space-x-1">
          <div v-for="link in navLinks" :key="link.name" class="relative" ref="dropdownRef">
            <Link
              v-if="!hasDropdown(link)"
              :href="route(link.route)"
              :class="getLinkClasses(link)"
            >
              <span class="mr-1">{{ link.icon }}</span>
              <span>{{ link.name }}</span>
            </Link>

            <button
              v-else
              @click="toggleDropdown(link.name)"
              :class="getLinkClasses(link)"
              aria-haspopup="true"
              :aria-expanded="openDropdown === link.name"
            >
              <span class="mr-1">{{ link.icon }}</span>
              <span>{{ link.name }}</span>
              <!-- Dropdown indicator -->
              <svg
                class="w-4 h-4 ml-1 transition-transform duration-200"
                :class="{ 'rotate-180': openDropdown === link.name }"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>

              <!-- Dropdown Menu for Kalkulator -->
              <div
                v-if="openDropdown === link.name && link.dropdown"
                class="absolute left-0 mt-2 w-56 bg-dark-card border border-primary/20 rounded-md shadow-lg z-50 overflow-hidden"
                style="top: 100%"
              >
                <div class="py-2 px-1">
                  <Link
                    v-for="item in link.dropdown"
                    :key="item.name"
                    :href="route(item.route)"
                    class="block px-3 py-2 text-sm text-gray-300 hover:text-primary-text hover:bg-primary/10 rounded-md flex items-center transition-colors duration-200 mx-1"
                  >
                    <span class="w-6 h-6 flex items-center justify-center mr-2">
                      {{ item.icon }}
                    </span>
                    <span>{{ item.name }}</span>
                  </Link>
                </div>
              </div>
            </button>
          </div>
        </div>

        <!-- Login/Register Buttons -->
        <div class="hidden md:flex space-x-2">
          <Link
            :href="route('login')"
            class="px-4 py-2 text-sm font-medium text-primary-text hover:text-white transition-colors duration-200"
          >
            Login 👤
          </Link>
          <Link
            :href="route('register')"
            class="px-4 py-2 bg-primary text-primary-text rounded-md text-sm font-medium hover:bg-primary/90 transition-colors duration-200"
          >
            Register 🚀
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

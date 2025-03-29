
<script setup>
import { ref, computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const props = defineProps({
  collapsed: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['toggle-collapse']);

const showProductsDropdown = ref(false);

const toggleProductsDropdown = () => {
  showProductsDropdown.value = !showProductsDropdown.value;
};

const isActive = (routeName) => {
  return router.currentRoute.value.name === routeName;
};

const navLinkClass = computed(() => (routeName) => {
  return isActive(routeName)
    ? "flex items-center gap-3 px-4 py-3 text-white bg-primary-600/30 rounded-lg transition-all duration-300"
    : "flex items-center gap-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-primary-800/20 rounded-lg transition-all duration-300";
});
</script>

<template>
  <aside
    :class="[
      'fixed top-0 left-0 h-full z-50 transition-all duration-300 ease-in-out shadow-glow',
      props.collapsed ? 'w-20' : 'w-64',
    ]"
  >
    <div class="h-full flex flex-col bg-dark-900 bg-opacity-90 backdrop-blur-sm rounded-r-xl border-r border-primary-800/30">
      <!-- Sidebar Header -->
      <div class="p-4 flex items-center justify-between border-b border-primary-800/30">
        <Link :href="route('admin.dashboard')" class="flex items-center space-x-3" v-show="!props.collapsed">
          <ApplicationLogo class="w-8 h-8 text-primary-400 filter drop-shadow-glow" />
          <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-400 to-secondary-400">
            VeinAdmin
          </span>
        </Link>
        <ApplicationLogo class="w-8 h-8 mx-auto text-primary-400 filter drop-shadow-glow" v-show="props.collapsed" />
        <button
          @click="$emit('toggle-collapse')"
          class="text-gray-400 hover:text-white transition-colors duration-200 p-1 rounded-md hover:bg-dark-800"
          :class="{ 'ml-auto': !props.collapsed }"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              :d="props.collapsed ? 'M13 5l7 7-7 7M5 5l7 7-7 7' : 'M11 19l-7-7 7-7M19 19l-7-7 7-7'"
            />
          </svg>
        </button>
      </div>

      <!-- Sidebar Navigation -->
      <div class="flex-1 overflow-y-auto py-4 px-3">
        <nav class="space-y-2">
          <!-- Dashboard Link -->
          <Link :href="route('admin.dashboard')" :class="navLinkClass('admin.dashboard')">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <rect width="7" height="7" x="3" y="3" rx="1" />
              <rect width="7" height="7" x="14" y="3" rx="1" />
              <rect width="7" height="7" x="14" y="14" rx="1" />
              <rect width="7" height="7" x="3" y="14" rx="1" />
            </svg>
            <span v-if="!props.collapsed">Dashboard</span>
          </Link>

          <!-- User Management -->
          <Link :href="route('userManage')" :class="navLinkClass('userManage')">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <span v-if="!props.collapsed">Users</span>
          </Link>

          <!-- Products & Services Dropdown -->
          <div class="relative">
            <button
              @click="toggleProductsDropdown"
              :class="[
                'w-full text-left',
                navLinkClass(() => false),
              ]"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M21 8V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2" />
                <path d="M4 10v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-6" />
                <rect width="10" height="6" x="7" y="4" rx="1" />
              </svg>
              <span v-if="!props.collapsed" class="flex-1">Products & Services</span>
              <svg
                v-if="!props.collapsed"
                xmlns="http://www.w3.org/2000/svg"
                :class="[
                  'h-4 w-4 transition-transform duration-200',
                  showProductsDropdown ? 'rotate-180' : '',
                ]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m6 9 6 6 6-6" />
              </svg>
            </button>
            
            <!-- Dropdown Content -->
            <div
              v-show="showProductsDropdown || props.collapsed"
              :class="[
                'space-y-1 mt-1 transition-all duration-300',
                props.collapsed ? 'absolute left-full top-0 ml-2 bg-dark-900 rounded-lg p-2 min-w-48 shadow-glow' : 'pl-4',
              ]"
            >
              <Link :href="route('admin.categories')" class="flex items-center gap-3 px-4 py-2 text-gray-300 hover:text-white hover:bg-primary-800/20 rounded-md">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect width="8" height="8" x="2" y="2" rx="1" />
                  <path d="M6 6h.01" />
                  <rect width="8" height="8" x="14" y="2" rx="1" />
                  <path d="M18 6h.01" />
                  <rect width="8" height="8" x="2" y="14" rx="1" />
                  <path d="M6 18h.01" />
                  <rect width="8" height="8" x="14" y="14" rx="1" />
                  <path d="M18 18h.01" />
                </svg>
                <span>Categories</span>
              </Link>
              <Link href="#" class="flex items-center gap-3 px-4 py-2 text-gray-300 hover:text-white hover:bg-primary-800/20 rounded-md">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M20.91 8.84 8.56 2.23a1.93 1.93 0 0 0-1.81 0L3.1 4.13a2.12 2.12 0 0 0-.05 3.69l12.22 6.93a2 2 0 0 0 1.94 0L21 12.51a2.12 2.12 0 0 0-.09-3.67Z" />
                  <path d="m3.09 8.84 12.35-6.61a1.93 1.93 0 0 1 1.81 0l3.65 1.9a2.12 2.12 0 0 1 .1 3.69L8.73 14.75a2 2 0 0 1-1.94 0L3 12.51a2.12 2.12 0 0 1 .09-3.67Z" />
                  <line x1="12" x2="12" y1="22" y2="13" />
                  <path d="M20 13.5v3.37a2.06 2.06 0 0 1-1.11 1.83l-6 3.08a1.93 1.93 0 0 1-1.78 0l-6-3.08A2.06 2.06 0 0 1 4 16.87V13.5" />
                </svg>
                <span>Products</span>
              </Link>
              <Link href="#" class="flex items-center gap-3 px-4 py-2 text-gray-300 hover:text-white hover:bg-primary-800/20 rounded-md">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="12" cy="12" r="10" />
                  <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                  <path d="M12 17h.01" />
                </svg>
                <span>Services</span>
              </Link>
              <Link href="#" class="flex items-center gap-3 px-4 py-2 text-gray-300 hover:text-white hover:bg-primary-800/20 rounded-md">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
                <span>Profit Settings</span>
              </Link>
            </div>
          </div>

          <!-- Transactions -->
          <Link href="#" :class="navLinkClass(() => false)">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <rect width="20" height="14" x="2" y="5" rx="2" />
              <line x1="2" x2="22" y1="10" y2="10" />
            </svg>
            <span v-if="!props.collapsed">Transactions</span>
          </Link>

          <!-- Payment Methods -->
          <Link href="#" :class="navLinkClass(() => false)">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M2.5 18.5A9 9 0 1 1 21.5 9" />
              <path d="M2.5 4.5 6 8l-3.5 3.5" />
            </svg>
            <span v-if="!props.collapsed">Payment Methods</span>
          </Link>

          <!-- Vouchers -->
          <Link href="#" :class="navLinkClass(() => false)">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M10 10a2 2 0 1 1 4 0c0 1.87-.3 2.5-2 3-1.7-.5-2-1.13-2-3Z" />
              <path d="M11.168 3.112A4.895 4.895 0 0 0 7 8c0 .92.164 1.797.465 2.613" />
              <path d="M16.535 10.613A8.21 8.21 0 0 0 17 8a4.895 4.895 0 0 0-4.168-4.888" />
              <path d="M11 21h2" />
              <path d="M8 17.2c0 4.8 8 4.8 8 0v-2.2H8Z" />
            </svg>
            <span v-if="!props.collapsed">Vouchers</span>
          </Link>

          <!-- Settings -->
          <Link :href="route('admin.settings')" :class="navLinkClass('admin.settings')">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
            <span v-if="!props.collapsed">Settings</span>
          </Link>
        </nav>
      </div>

      <!-- Sidebar Footer -->
      <div class="p-4 border-t border-primary-800/30">
        <Link href="/" class="flex items-center gap-3 px-4 py-2 text-gray-300 hover:text-white hover:bg-primary-800/20 rounded-lg">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          <span v-if="!props.collapsed">Back to Site</span>
        </Link>
      </div>
    </div>
  </aside>
</template>

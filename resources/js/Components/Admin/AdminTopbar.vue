
<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

const props = defineProps({
  toggleSidebar: {
    type: Function,
    required: true
  }
});

const page = usePage();
</script>

<template>
  <nav class="bg-dark-800 border-b border-primary-800/30 backdrop-blur-sm sticky top-0 z-40">
    <div class="px-4 mx-auto w-full">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Mobile Hamburger -->
          <div class="sm:hidden mr-4">
            <button
              @click="toggleSidebar"
              class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white"
            >
              <svg
                class="w-6 h-6"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 24 24"
              >
                <path
                  class="inline-flex"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
            </button>
          </div>

          <!-- Search Bar -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 text-gray-400" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2" 
                stroke-linecap="round" 
                stroke-linejoin="round"
              >
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
              </svg>
            </div>
            <input
              type="search"
              class="block w-full p-2 pl-10 text-sm text-white border border-gray-700 rounded-lg bg-dark-900 focus:ring-primary-500 focus:border-primary-500"
              placeholder="Search..."
            />
          </div>
        </div>

        <!-- User Dropdown -->
        <div class="flex items-center">
          <div class="relative ml-3">
            <Dropdown align="right" width="48">
              <template #trigger>
                <span class="inline-flex rounded-md">
                  <button
                    type="button"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-300 transition duration-150 ease-in-out bg-dark-800 border border-gray-700 rounded-md hover:text-white focus:outline-none"
                  >
                    {{ $page.props.auth.user.username }}

                    <svg
                      class="ml-2 -mr-0.5 h-4 w-4"
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
                  </button>
                </span>
              </template>

              <template #content>
                <DropdownLink :href="route('profile.edit')" class="text-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                  Profile
                </DropdownLink>
                <DropdownLink
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="text-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                  Log Out
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

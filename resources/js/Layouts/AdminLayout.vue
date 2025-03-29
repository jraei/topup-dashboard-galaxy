
<script setup>
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const showingNavigationDropdown = ref(false);
const props = defineProps({
  title: {
    type: String,
    default: null,
  },
});

const page = usePage();

const toggleSidebar = () => {
  showingNavigationDropdown.value = !showingNavigationDropdown.value;
};
</script>

<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Top Navigation -->
    <nav class="bg-gray-800 border-b border-gray-700">
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="flex items-center shrink-0">
              <Link :href="route('admin.dashboard')">
                <ApplicationLogo
                  class="block w-auto text-indigo-400 fill-current h-9"
                />
              </Link>
            </div>

            <!-- Navigation Links - Desktop -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <NavLink
                :href="route('admin.dashboard')"
                :active="route().current('admin.dashboard')"
                class="text-gray-300 hover:text-white"
              >
                Dashboard
              </NavLink>
              <NavLink
                :href="route('userManage')"
                :active="route().current('userManage')"
                class="text-gray-300 hover:text-white"
              >
                Users
              </NavLink>
              <NavLink
                :href="route('admin.settings')"
                :active="route().current('admin.settings')"
                class="text-gray-300 hover:text-white"
              >
                Settings
              </NavLink>
            </div>
          </div>

          <!-- User Dropdown - Desktop -->
          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <div class="relative ml-3">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <span class="inline-flex rounded-md">
                    <button
                      type="button"
                      class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-300 transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:text-white focus:outline-none"
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
                  <DropdownLink :href="route('profile.edit')">
                    Profile
                  </DropdownLink>
                  <DropdownLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                  >
                    Log Out
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>

          <!-- Hamburger -->
          <div class="flex items-center -mr-2 sm:hidden">
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
                  :class="{
                    hidden: showingNavigationDropdown,
                    'inline-flex': !showingNavigationDropdown,
                  }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  :class="{
                    hidden: !showingNavigationDropdown,
                    'inline-flex': showingNavigationDropdown,
                  }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div
        :class="{
          block: showingNavigationDropdown,
          hidden: !showingNavigationDropdown,
        }"
        class="sm:hidden"
      >
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink
            :href="route('admin.dashboard')"
            :active="route().current('admin.dashboard')"
            class="text-gray-300 hover:text-white"
          >
            Dashboard
          </ResponsiveNavLink>
          <ResponsiveNavLink
            :href="route('userManage')"
            :active="route().current('userManage')"
            class="text-gray-300 hover:text-white"
          >
            Users
          </ResponsiveNavLink>
          <ResponsiveNavLink
            :href="route('admin.settings')"
            :active="route().current('admin.settings')"
            class="text-gray-300 hover:text-white"
          >
            Settings
          </ResponsiveNavLink>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-700">
          <div class="px-4">
            <div class="text-base font-medium text-white">
              {{ $page.props.auth.user.username }}
            </div>
            <div class="text-sm font-medium text-gray-400">
              {{ $page.props.auth.user.email }}
            </div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.edit')">
              Profile
            </ResponsiveNavLink>
            <ResponsiveNavLink
              :href="route('logout')"
              method="post"
              as="button"
            >
              Log Out
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="py-4 sm:py-6 lg:py-8">
      <!-- Page Heading -->
      <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <h1 v-if="props.title" class="text-3xl font-semibold text-white">
          {{ props.title }}
        </h1>
        <slot name="header" />
      </header>

      <!-- Page Content -->
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-gray-800 shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-700">
            <slot />
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

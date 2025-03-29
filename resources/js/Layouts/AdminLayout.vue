
<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import AdminSidebar from "@/Components/Admin/AdminSidebar.vue";
import AdminTopbar from "@/Components/Admin/AdminTopbar.vue";

const showingNavigationDropdown = ref(false);
const isSidebarCollapsed = ref(false);

const props = defineProps({
  title: {
    type: String,
    default: null,
  },
});

const toggleSidebar = () => {
  showingNavigationDropdown.value = !showingNavigationDropdown.value;
};

const toggleSidebarCollapse = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
};
</script>

<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Floating Sidebar -->
    <AdminSidebar 
      :collapsed="isSidebarCollapsed" 
      @toggle-collapse="toggleSidebarCollapse"
      :class="[showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full sm:translate-x-0']"
    />

    <!-- Main Content -->
    <div :class="[
      'transition-all duration-300',
      isSidebarCollapsed ? 'sm:ml-20' : 'sm:ml-64'
    ]">
      <!-- Top Navigation -->
      <AdminTopbar :toggle-sidebar="toggleSidebar" />

      <!-- Page Content -->
      <main class="py-6">
        <!-- Page Heading -->
        <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
          <h1 v-if="props.title" class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-primary">
            {{ props.title }}
          </h1>
          <slot name="header" />
        </header>

        <!-- Page Content -->
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<style>
.sidebar-backdrop {
  @apply fixed inset-0 bg-black bg-opacity-50 z-40;
}
</style>

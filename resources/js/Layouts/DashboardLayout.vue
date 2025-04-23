
<script setup>
import { watch, ref } from "vue";
import DashboardSidebar from "@/Components/DashboardSidebar.vue";

const collapsed = ref(localStorage.getItem("dashboardSidebarCollapsed") === "true");
watch(collapsed, (value) => localStorage.setItem("dashboardSidebarCollapsed", value));
</script>

<template>
  <div class="flex min-h-screen bg-content-background">
    <DashboardSidebar v-model:collapsed="collapsed" />
    <div class="flex-1 w-0 min-w-0">
      <slot />
    </div>
  </div>
</template>

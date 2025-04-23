
<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import MutationsTable from "@/Components/Dashboard/MutationsTable.vue";
defineProps(["mutations"]);
</script>
<template>
  <DashboardLayout>
    <div class="max-w-6xl mx-auto p-4 fade-in">
      <MutationsTable :mutations="mutations"/>
    </div>
  </DashboardLayout>
</template>

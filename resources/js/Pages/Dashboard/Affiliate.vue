
<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import AffiliatePanel from "@/Components/Dashboard/AffiliatePanel.vue";
defineProps(["affiliate"]);
</script>
<template>
  <DashboardLayout>
    <div class="max-w-4xl mx-auto p-4 fade-in">
      <AffiliatePanel :data="affiliate" />
    </div>
  </DashboardLayout>
</template>


<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import TransactionsTable from "@/Components/Dashboard/TransactionsTable.vue";
defineProps(["transactions"]);
</script>
<template>
  <DashboardLayout>
    <div class="max-w-6xl mx-auto p-4 fade-in">
      <TransactionsTable :transactions="transactions" />
    </div>
  </DashboardLayout>
</template>

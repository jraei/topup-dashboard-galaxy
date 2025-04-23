
<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import UserProfileCard from "@/Components/Dashboard/UserProfileCard.vue";
import NaelcoinBalanceCard from "@/Components/Dashboard/NaelcoinBalanceCard.vue";
import TransactionOverview from "@/Components/Dashboard/TransactionOverview.vue";
import RecentActivityFeed from "@/Components/Dashboard/RecentActivityFeed.vue";
defineProps([
  "userData",
  "statusCounts",
  "recentBalances",
  "recentActivities",
  "affiliate"
]);
</script>
<template>
  <DashboardLayout>
    <div class="max-w-5xl mx-auto p-4 grid gap-8 grid-cols-1 lg:grid-cols-4 cosmic-starfield">
      <div class="lg:col-span-1 flex flex-col gap-6 cosmic fade-in">
        <UserProfileCard :user="userData" />
        <NaelcoinBalanceCard :balances="recentBalances" />
      </div>
      <div class="lg:col-span-3 flex flex-col gap-6">
        <TransactionOverview :counts="statusCounts" />
        <RecentActivityFeed :activities="recentActivities" />
      </div>
    </div>
  </DashboardLayout>
</template>

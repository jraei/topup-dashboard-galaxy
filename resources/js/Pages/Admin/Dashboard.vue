
<script setup>
import { ref, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import StatCard from "@/Components/Admin/StatCard.vue";
import DataTable from "@/Components/Admin/DataTable.vue";

// Dummy data for demonstration
const stats = ref({
  users: {
    count: 1254,
    trend: 12.5
  },
  deposits: {
    count: 'Rp 8,456,000',
    trend: 8.2
  },
  transactions: {
    count: 3478,
    trend: -2.7
  },
  products: {
    count: 86,
    trend: 0
  }
});

// Dummy data for recent transactions
const recentTransactions = ref([
  { id: 'INV-001', user: 'john_doe', amount: 'Rp 150,000', status: 'completed', date: '2023-06-15 14:30' },
  { id: 'INV-002', user: 'jane_smith', amount: 'Rp 85,000', status: 'pending', date: '2023-06-15 12:45' },
  { id: 'INV-003', user: 'mike123', amount: 'Rp 220,000', status: 'completed', date: '2023-06-14 18:20' },
  { id: 'INV-004', user: 'sarah_j', amount: 'Rp 75,000', status: 'failed', date: '2023-06-14 16:15' },
  { id: 'INV-005', user: 'alex_gamer', amount: 'Rp 300,000', status: 'completed', date: '2023-06-14 10:30' }
]);

// Dummy data for top services
const topServices = ref([
  { id: 1, name: 'Mobile Legends: Weekly Diamond Pass', sold: 128, profit: 'Rp 1,280,000' },
  { id: 2, name: 'Free Fire: 500 Diamonds', sold: 96, profit: 'Rp 864,000' },
  { id: 3, name: 'PUBG Mobile: UC Pack Medium', sold: 84, profit: 'Rp 756,000' },
  { id: 4, name: 'Valorant: 1000 Points', sold: 76, profit: 'Rp 684,000' },
  { id: 5, name: 'Genshin Impact: Blessing', sold: 65, profit: 'Rp 585,000' }
]);

// Recent transactions table columns
const transactionColumns = [
  { key: 'id', label: 'Invoice ID', sortable: true },
  { key: 'user', label: 'User', sortable: true },
  { key: 'amount', label: 'Amount', sortable: true },
  { 
    key: 'status', 
    label: 'Status', 
    sortable: true,
    render: (item) => {
      const statusClasses = {
        completed: 'bg-green-900/30 text-green-400 border-green-800',
        pending: 'bg-yellow-900/30 text-yellow-400 border-yellow-800',
        failed: 'bg-red-900/30 text-red-400 border-red-800'
      };
      
      return {
        template: `
          <span class="px-2 py-1 text-xs font-medium rounded-full border ${statusClasses[item.status]}">
            ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
          </span>
        `
      };
    }
  },
  { key: 'date', label: 'Date', sortable: true }
];

// Top services table columns
const serviceColumns = [
  { key: 'name', label: 'Service Name', sortable: true },
  { key: 'sold', label: 'Units Sold', sortable: true },
  { key: 'profit', label: 'Total Profit', sortable: true }
];

// Simulated chart data (would be replaced with a real chart library in production)
const chartData = {
  revenue: [5800, 6200, 5500, 7000, 6800, 8200, 7500, 9000, 8500, 10500, 9800, 11200, 10500, 12000]
};
</script>

<template>
  <Head title="Admin Dashboard" />

  <AdminLayout title="Admin Dashboard">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatCard
        title="Total Users"
        :value="stats.users.count"
        :trend="stats.users.trend"
        color="primary"
      >
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-6 h-6"
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
        </template>
      </StatCard>

      <StatCard
        title="Total Deposits"
        :value="stats.deposits.count"
        :trend="stats.deposits.trend"
        color="secondary"
      >
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <rect width="20" height="14" x="2" y="5" rx="2" />
            <line x1="2" y1="10" x2="22" y2="10" />
          </svg>
        </template>
      </StatCard>

      <StatCard
        title="Total Transactions"
        :value="stats.transactions.count"
        :trend="stats.transactions.trend"
        color="info"
      >
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
          </svg>
        </template>
      </StatCard>

      <StatCard
        title="Active Products"
        :value="stats.products.count"
        :trend="stats.products.trend"
        color="success"
      >
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-1.5" />
            <path d="M16 2v4" />
            <path d="M8 2v4" />
            <path d="M3 10h18" />
            <path d="M18 16.5v-7" />
            <path d="M18 14.25h-4.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5H18v1.5" />
          </svg>
        </template>
      </StatCard>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-dark-800 rounded-xl border border-gray-700 shadow-lg overflow-hidden p-6 mb-8">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h3 class="text-lg font-semibold text-white">Revenue Trends</h3>
        <div class="flex items-center mt-2 md:mt-0">
          <button class="bg-dark-700 hover:bg-dark-600 text-gray-300 px-3 py-1 rounded-l-lg border border-gray-600">Day</button>
          <button class="bg-primary-600 text-white px-3 py-1 border border-primary-500">Week</button>
          <button class="bg-dark-700 hover:bg-dark-600 text-gray-300 px-3 py-1 rounded-r-lg border border-gray-600">Month</button>
        </div>
      </div>
      
      <!-- Simulated Chart (this would be replaced by a real chart component) -->
      <div class="relative h-80 w-full">
        <div class="absolute inset-0 bg-gradient-to-b from-dark-800 to-dark-900 flex items-end">
          <div v-for="(value, index) in chartData.revenue" :key="index" :style="{ height: `${(value / 12000) * 100}%` }" class="w-full h-full bg-gradient-to-t from-primary-600/50 to-primary-400/10 border-t border-primary-500/50 mx-0.5 rounded-t"></div>
        </div>
        
        <!-- X-Axis Labels -->
        <div class="absolute bottom-0 left-0 right-0 flex justify-between px-2 py-4">
          <div v-for="index in 14" :key="index" class="text-xs text-gray-500">{{ index }}</div>
        </div>
      </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Recent Transactions -->
      <div>
        <h3 class="text-xl font-semibold text-white mb-4">Recent Transactions</h3>
        <DataTable
          :data="recentTransactions"
          :columns="transactionColumns"
          :items-per-page="5"
        />
      </div>

      <!-- Top Services -->
      <div>
        <h3 class="text-xl font-semibold text-white mb-4">Top Services</h3>
        <DataTable
          :data="topServices"
          :columns="serviceColumns"
          :items-per-page="5"
        />
      </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="mt-8">
      <h3 class="text-xl font-semibold text-white mb-4">Quick Actions</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a
          href="#"
          class="flex items-center p-4 bg-dark-800 rounded-lg shadow-md hover:shadow-glow border border-gray-700 hover:border-primary-600 transition-all duration-300"
        >
          <div
            class="flex-shrink-0 p-3 bg-primary-900/50 border border-primary-700/50 rounded-md mr-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-primary-400"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M21 8v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8" />
              <path d="M3 3h18a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" />
              <path d="M9.998 13h4v4h-4v-4Z" />
            </svg>
          </div>
          <div>
            <h4 class="text-lg font-medium text-white mb-1">Add New Product</h4>
            <p class="text-sm text-gray-400">Create a new product listing</p>
          </div>
        </a>

        <a
          href="#"
          class="flex items-center p-4 bg-dark-800 rounded-lg shadow-md hover:shadow-glow border border-gray-700 hover:border-primary-600 transition-all duration-300"
        >
          <div
            class="flex-shrink-0 p-3 bg-secondary-900/50 border border-secondary-700/50 rounded-md mr-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-secondary-400"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M2 6a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v12a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V6Z" />
              <path d="M12 12v-4" />
              <path d="M8 12h8" />
            </svg>
          </div>
          <div>
            <h4 class="text-lg font-medium text-white mb-1">Manage Orders</h4>
            <p class="text-sm text-gray-400">View and update order status</p>
          </div>
        </a>

        <a
          href="#"
          class="flex items-center p-4 bg-dark-800 rounded-lg shadow-md hover:shadow-glow border border-gray-700 hover:border-primary-600 transition-all duration-300"
        >
          <div
            class="flex-shrink-0 p-3 bg-green-900/50 border border-green-700/50 rounded-md mr-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-green-400"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M2 9V5c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v4" />
              <path d="M2 13v6c0 1.1.9 2 2 2h16a2 2 0 0 0 2-2v-6" />
              <path d="M2 9h20" />
              <path d="M9 13v6" />
              <path d="M15 13v6" />
            </svg>
          </div>
          <div>
            <h4 class="text-lg font-medium text-white mb-1">Website Settings</h4>
            <p class="text-sm text-gray-400">Update website configuration</p>
          </div>
        </a>
      </div>
    </div>
  </AdminLayout>
</template>

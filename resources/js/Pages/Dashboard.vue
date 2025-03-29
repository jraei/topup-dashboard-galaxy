
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

// Sample data for dashboard stats
const stats = ref({
  balance: {
    total: 2500000,
    growthPercent: 15.2,
    isPositive: true
  },
  transactions: {
    total: 376,
    growthPercent: 8.4,
    isPositive: true
  },
  users: {
    total: 1250,
    growthPercent: -2.7,
    isPositive: false
  },
  products: {
    total: 124,
    growthPercent: 5.8,
    isPositive: true
  }
});

// Sample data for recent transactions
const recentTransactions = ref([
  { id: 'TX-7825', user: 'John Doe', game: 'Mobile Legends', amount: 150000, status: 'completed', date: '2023-11-15' },
  { id: 'TX-7824', user: 'Sarah Smith', game: 'PUBG Mobile', amount: 75000, status: 'pending', date: '2023-11-15' },
  { id: 'TX-7823', user: 'Michael Lee', game: 'Free Fire', amount: 50000, status: 'failed', date: '2023-11-14' },
  { id: 'TX-7822', user: 'Emma Wilson', game: 'Genshin Impact', amount: 200000, status: 'completed', date: '2023-11-14' }
]);

// Format currency - assuming IDR
const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value);
};

// Get status styling
const getStatusClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-accent-green/20 text-accent-green';
    case 'pending':
      return 'bg-accent-yellow/20 text-accent-yellow';
    case 'failed':
      return 'bg-accent-red/20 text-accent-red';
    default:
      return 'bg-gray-500/20 text-gray-400';
  }
};
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <div class="py-6">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-white mb-2">Welcome back, <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">{{ $page.props.auth.user.name }}</span></h1>
          <p class="text-gray-400">Here's what's happening with your account today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
          <!-- Balance Card -->
          <div class="p-6 rounded-lg border border-gray-700 bg-gradient-to-br from-dark-card to-dark-lighter shadow-lg hover:shadow-glow-primary transition-all duration-300">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-gray-400 text-sm font-medium">Balance</p>
                <h2 class="text-3xl font-bold text-white mt-2">{{ formatCurrency(stats.balance.total) }}</h2>
                <div :class="[
                  'flex items-center mt-2 text-sm',
                  stats.balance.isPositive ? 'text-green-400' : 'text-red-400'
                ]">
                  <svg v-if="stats.balance.isPositive" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ Math.abs(stats.balance.growthPercent) }}% {{ stats.balance.isPositive ? 'increase' : 'decrease' }}</span>
                </div>
              </div>
              <div class="bg-primary/20 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="h-2 w-full bg-gray-700 mt-4 rounded-full overflow-hidden">
              <div class="bg-gradient-to-r from-primary to-secondary h-full rounded-full" :style="{ width: `${stats.balance.growthPercent * 3}%` }"></div>
            </div>
          </div>

          <!-- Transactions Card -->
          <div class="p-6 rounded-lg border border-gray-700 bg-gradient-to-br from-dark-card to-dark-lighter shadow-lg hover:shadow-glow-secondary transition-all duration-300">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-gray-400 text-sm font-medium">Transactions</p>
                <h2 class="text-3xl font-bold text-white mt-2">{{ stats.transactions.total }}</h2>
                <div :class="[
                  'flex items-center mt-2 text-sm',
                  stats.transactions.isPositive ? 'text-green-400' : 'text-red-400'
                ]">
                  <svg v-if="stats.transactions.isPositive" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ Math.abs(stats.transactions.growthPercent) }}% {{ stats.transactions.isPositive ? 'increase' : 'decrease' }}</span>
                </div>
              </div>
              <div class="bg-secondary/20 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
              </div>
            </div>
            <div class="h-2 w-full bg-gray-700 mt-4 rounded-full overflow-hidden">
              <div class="bg-gradient-to-r from-secondary to-primary h-full rounded-full" :style="{ width: `${stats.transactions.growthPercent * 3}%` }"></div>
            </div>
          </div>

          <!-- Users Card -->
          <div class="p-6 rounded-lg border border-gray-700 bg-gradient-to-br from-dark-card to-dark-lighter shadow-lg hover:shadow-glow-primary transition-all duration-300">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-gray-400 text-sm font-medium">Total Users</p>
                <h2 class="text-3xl font-bold text-white mt-2">{{ stats.users.total }}</h2>
                <div :class="[
                  'flex items-center mt-2 text-sm',
                  stats.users.isPositive ? 'text-green-400' : 'text-red-400'
                ]">
                  <svg v-if="stats.users.isPositive" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ Math.abs(stats.users.growthPercent) }}% {{ stats.users.isPositive ? 'increase' : 'decrease' }}</span>
                </div>
              </div>
              <div class="bg-purple-500/20 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
            </div>
            <div class="h-2 w-full bg-gray-700 mt-4 rounded-full overflow-hidden">
              <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-full rounded-full" :style="{ width: `${Math.max(2, Math.abs(stats.users.growthPercent) * 3)}%` }"></div>
            </div>
          </div>

          <!-- Products Card -->
          <div class="p-6 rounded-lg border border-gray-700 bg-gradient-to-br from-dark-card to-dark-lighter shadow-lg hover:shadow-glow-secondary transition-all duration-300">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-gray-400 text-sm font-medium">Products</p>
                <h2 class="text-3xl font-bold text-white mt-2">{{ stats.products.total }}</h2>
                <div :class="[
                  'flex items-center mt-2 text-sm',
                  stats.products.isPositive ? 'text-green-400' : 'text-red-400'
                ]">
                  <svg v-if="stats.products.isPositive" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ Math.abs(stats.products.growthPercent) }}% {{ stats.products.isPositive ? 'increase' : 'decrease' }}</span>
                </div>
              </div>
              <div class="bg-accent-blue/20 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-accent-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
            </div>
            <div class="h-2 w-full bg-gray-700 mt-4 rounded-full overflow-hidden">
              <div class="bg-gradient-to-r from-blue-500 to-cyan-400 h-full rounded-full" :style="{ width: `${stats.products.growthPercent * 3}%` }"></div>
            </div>
          </div>
        </div>

        <!-- Chart Section (Placeholder for actual chart integration) -->
        <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
          <!-- Revenue Chart -->
          <div class="p-6 rounded-lg border border-gray-700 bg-dark-card shadow-lg">
            <h3 class="text-xl font-semibold text-white mb-6">Revenue Overview</h3>
            <div class="h-64 w-full">
              <!-- This would be replaced with an actual chart component -->
              <div class="flex items-center justify-center h-full w-full bg-dark-lighter rounded-lg border border-gray-700 p-4">
                <div class="text-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  <p class="text-gray-400">Revenue Chart</p>
                  <p class="text-gray-500 text-sm mt-2">Historical revenue data visualization</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Game Population Chart -->
          <div class="p-6 rounded-lg border border-gray-700 bg-dark-card shadow-lg">
            <h3 class="text-xl font-semibold text-white mb-6">Top Games Distribution</h3>
            <div class="h-64 w-full">
              <!-- This would be replaced with an actual chart component -->
              <div class="flex items-center justify-center h-full w-full bg-dark-lighter rounded-lg border border-gray-700 p-4">
                <div class="text-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                  </svg>
                  <p class="text-gray-400">Game Distribution Chart</p>
                  <p class="text-gray-500 text-sm mt-2">Visual breakdown of top game populations</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Transactions -->
        <div class="rounded-lg border border-gray-700 bg-dark-card shadow-lg overflow-hidden mb-8">
          <div class="p-6 border-b border-gray-700 flex items-center justify-between">
            <h3 class="text-xl font-semibold text-white">Recent Transactions</h3>
            <button class="text-secondary hover:text-secondary-hover transition-colors">View All</button>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full min-w-full">
              <thead>
                <tr class="bg-dark-lighter">
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Transaction ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Game</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-700">
                <tr v-for="transaction in recentTransactions" :key="transaction.id" class="hover:bg-dark-lighter transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ transaction.id }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ transaction.user }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ transaction.game }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">{{ formatCurrency(transaction.amount) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[getStatusClass(transaction.status), 'px-2 py-1 text-xs rounded-full']">
                      {{ transaction.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ transaction.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Quick Actions -->
        <div>
          <h3 class="text-xl font-semibold text-white mb-6">Quick Actions</h3>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Add Product -->
            <div class="p-6 rounded-lg border border-gray-700 bg-gradient-to-br from-dark-card to-dark-lighter shadow-lg hover:shadow-glow-primary cursor-pointer transition-all duration-300 flex items-center space-x-4">
              <div class="p-3 bg-primary/20 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <div>
                <h4 class="text-white font-medium">Add Product</h4>
                <p class="text-sm text-gray-400 mt-1">Create a new game top-up option</p>
              </div>
            </div>

            <!-- New Transaction -->
            <div class="p-6 rounded-lg border border-gray-700 bg-gradient-to-br from-dark-card to-dark-lighter shadow-lg hover:shadow-glow-secondary cursor-pointer transition-all duration-300 flex items-center space-x-4">
              <div class="p-3 bg-secondary/20 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div>
                <h4 class="text-white font-medium">New Transaction</h4>
                <p class="text-sm text-gray-400 mt-1">Process a manual transaction</p>
              </div>
            </div>

            <!-- View Reports -->
            <div class="p-6 rounded-lg border border-gray-700 bg-gradient-to-br from-dark-card to-dark-lighter shadow-lg hover:shadow-glow-primary cursor-pointer transition-all duration-300 flex items-center space-x-4">
              <div class="p-3 bg-purple-500/20 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <h4 class="text-white font-medium">View Reports</h4>
                <p class="text-sm text-gray-400 mt-1">Generate financial reports</p>
              </div>
            </div>

            <!-- Website Settings -->
            <div class="p-6 rounded-lg border border-gray-700 bg-gradient-to-br from-dark-card to-dark-lighter shadow-lg hover:shadow-glow-secondary cursor-pointer transition-all duration-300 flex items-center space-x-4">
              <div class="p-3 bg-accent-blue/20 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <h4 class="text-white font-medium">Settings</h4>
                <p class="text-sm text-gray-400 mt-1">Update website configuration</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

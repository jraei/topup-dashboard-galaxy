
<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { 
  TrendingUp, 
  TrendingDown, 
  Users, 
  DollarSign, 
  ShoppingCart, 
  Package 
} from "lucide-vue-next";

// Mock data for charts and statistics (to be replaced with actual backend data)
const stats = ref({
  users: {
    total: 1250,
    growthPercent: 12.5,
    isPositive: true
  },
  revenue: {
    total: 18500,
    currency: "USD",
    growthPercent: 8.2,
    isPositive: true
  },
  orders: {
    total: 3784,
    growthPercent: -2.4,
    isPositive: false
  },
  products: {
    total: 152,
    growthPercent: 5.1,
    isPositive: true
  }
});

const recentTransactions = ref([
  { id: 'TX-1234', user: 'John Doe', amount: 25.99, status: 'completed', date: '2023-09-15', game: 'Mobile Legends' },
  { id: 'TX-1235', user: 'Jane Smith', amount: 49.99, status: 'pending', date: '2023-09-15', game: 'Free Fire' },
  { id: 'TX-1236', user: 'Robert Brown', amount: 15.00, status: 'completed', date: '2023-09-14', game: 'PUBG Mobile' },
  { id: 'TX-1237', user: 'Alice Johnson', amount: 100.00, status: 'failed', date: '2023-09-14', game: 'Genshin Impact' },
]);

// Helper function for formatting currency
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(value);
};

// Status class generator
const getStatusClass = (status) => {
  switch (status) {
    case 'completed': return 'bg-green-500/20 text-green-400';
    case 'pending': return 'bg-yellow-500/20 text-yellow-400';
    case 'failed': return 'bg-red-500/20 text-red-400';
    default: return 'bg-gray-500/20 text-gray-400';
  }
};
</script>

<template>
  <Head title="Admin Dashboard" />

  <AdminLayout title="Dashboard">
    <div class="p-6 space-y-8">
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Users Stat Card -->
        <Card class="bg-gradient-to-br from-dark-card to-dark-lighter border-gray-700 hover:shadow-glow-primary transition-all">
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium text-gray-400">Total Users</CardTitle>
            <Users class="h-4 w-4 text-muted-foreground text-primary" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-white">{{ stats.users.total.toLocaleString() }}</div>
            <p :class="[
              'text-xs',
              stats.users.isPositive ? 'text-green-400' : 'text-red-400'
            ]">
              <TrendingUp v-if="stats.users.isPositive" class="inline-block mr-1 h-3 w-3" />
              <TrendingDown v-else class="inline-block mr-1 h-3 w-3" />
              {{ Math.abs(stats.users.growthPercent) }}% 
              {{ stats.users.isPositive ? 'increase' : 'decrease' }}
            </p>
          </CardContent>
        </Card>

        <!-- Revenue Stat Card -->
        <Card class="bg-gradient-to-br from-dark-card to-dark-lighter border-gray-700 hover:shadow-glow-secondary transition-all">
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium text-gray-400">Total Revenue</CardTitle>
            <DollarSign class="h-4 w-4 text-muted-foreground text-secondary" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-white">{{ formatCurrency(stats.revenue.total) }}</div>
            <p :class="[
              'text-xs',
              stats.revenue.isPositive ? 'text-green-400' : 'text-red-400'
            ]">
              <TrendingUp v-if="stats.revenue.isPositive" class="inline-block mr-1 h-3 w-3" />
              <TrendingDown v-else class="inline-block mr-1 h-3 w-3" />
              {{ Math.abs(stats.revenue.growthPercent) }}% 
              {{ stats.revenue.isPositive ? 'increase' : 'decrease' }}
            </p>
          </CardContent>
        </Card>

        <!-- Orders Stat Card -->
        <Card class="bg-gradient-to-br from-dark-card to-dark-lighter border-gray-700 hover:shadow-glow-primary transition-all">
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium text-gray-400">Total Orders</CardTitle>
            <ShoppingCart class="h-4 w-4 text-muted-foreground text-pink-400" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-white">{{ stats.orders.total.toLocaleString() }}</div>
            <p :class="[
              'text-xs',
              stats.orders.isPositive ? 'text-green-400' : 'text-red-400'
            ]">
              <TrendingUp v-if="stats.orders.isPositive" class="inline-block mr-1 h-3 w-3" />
              <TrendingDown v-else class="inline-block mr-1 h-3 w-3" />
              {{ Math.abs(stats.orders.growthPercent) }}% 
              {{ stats.orders.isPositive ? 'increase' : 'decrease' }}
            </p>
          </CardContent>
        </Card>

        <!-- Products Stat Card -->
        <Card class="bg-gradient-to-br from-dark-card to-dark-lighter border-gray-700 hover:shadow-glow-secondary transition-all">
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium text-gray-400">Active Products</CardTitle>
            <Package class="h-4 w-4 text-muted-foreground text-purple-400" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-white">{{ stats.products.total }}</div>
            <p :class="[
              'text-xs',
              stats.products.isPositive ? 'text-green-400' : 'text-red-400'
            ]">
              <TrendingUp v-if="stats.products.isPositive" class="inline-block mr-1 h-3 w-3" />
              <TrendingDown v-else class="inline-block mr-1 h-3 w-3" />
              {{ Math.abs(stats.products.growthPercent) }}% 
              {{ stats.products.isPositive ? 'increase' : 'decrease' }}
            </p>
          </CardContent>
        </Card>
      </div>

      <!-- Recent Transactions -->
      <div class="bg-dark-card rounded-lg border border-gray-700 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
          <h3 class="text-xl font-semibold text-white">Recent Transactions</h3>
          <Badge variant="secondary">View All</Badge>
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
    </div>
  </AdminLayout>
</template>

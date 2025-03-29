
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

// Import the newly created components
import StatCard from "@/Components/AdminDashboard/StatCard.vue";
import RevenueChart from "@/Components/AdminDashboard/RevenueChart.vue";
import OrdersChart from "@/Components/AdminDashboard/OrdersChart.vue";
import PopularGames from "@/Components/AdminDashboard/PopularGames.vue";

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
        <StatCard 
          title="Total Users" 
          :value="stats.users.total" 
          :icon="Users" 
          icon-color="text-primary" 
          :growth-percent="stats.users.growthPercent"
          :is-positive="stats.users.isPositive"
        />

        <!-- Revenue Stat Card -->
        <StatCard 
          title="Total Revenue" 
          :value="stats.revenue.total" 
          :icon="DollarSign" 
          icon-color="text-secondary" 
          :growth-percent="stats.revenue.growthPercent"
          :is-positive="stats.revenue.isPositive"
          :currency="stats.revenue.currency"
        />

        <!-- Orders Stat Card -->
        <StatCard 
          title="Total Orders" 
          :value="stats.orders.total" 
          :icon="ShoppingCart" 
          icon-color="text-pink-400" 
          :growth-percent="stats.orders.growthPercent"
          :is-positive="stats.orders.isPositive"
        />

        <!-- Products Stat Card -->
        <StatCard 
          title="Active Products" 
          :value="stats.products.total" 
          :icon="Package" 
          icon-color="text-purple-400" 
          :growth-percent="stats.products.growthPercent"
          :is-positive="stats.products.isPositive"
        />
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Revenue Chart - Takes 2/3 of the space on large screens -->
        <div class="lg:col-span-2">
          <RevenueChart />
        </div>
        
        <!-- Popular Games - Takes 1/3 of the space -->
        <div>
          <PopularGames />
        </div>
      </div>

      <!-- Orders Chart -->
      <div>
        <OrdersChart />
      </div>

      <!-- Recent Transactions -->
      <div class="bg-dark-card rounded-lg border border-gray-700 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
          <h3 class="text-xl font-semibold text-white">Recent Transactions</h3>
          <Badge variant="secondary" class="cursor-pointer hover:bg-secondary/80 transition-colors">View All</Badge>
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

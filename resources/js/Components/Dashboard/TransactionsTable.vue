
<script setup>
defineProps({ transactions: Object });
</script>
<template>
  <div class="cosmic-table shadow rounded overflow-x-auto">
    <table class="min-w-full">
      <thead>
        <tr>
          <th>Date</th><th>ID</th><th>Item</th><th>Status</th><th>Amount</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="tx in transactions.data" :key="tx.id">
          <td>{{ tx.created_at }}</td>
          <td class="font-mono">{{ tx.order_id }}</td>
          <td>{{ tx.layanan?.name ?? '-' }}</td>
          <td>{{ tx.status }}</td>
          <td>Rp{{ tx.price }}</td>
          <td><!-- Actions go here --></td>
        </tr>
      </tbody>
    </table>
    <!-- Pagination placeholder -->
    <div class="flex justify-between items-center mt-2">
      <span class="text-xs text-secondary">Showing {{ transactions.from }}-{{ transactions.to }} of {{ transactions.total }}</span>
    </div>
  </div>
</template>

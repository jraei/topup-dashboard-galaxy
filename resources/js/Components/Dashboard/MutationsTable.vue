
<script setup>
defineProps({ mutations: Object });
</script>
<template>
  <div class="cosmic-table shadow rounded overflow-x-auto">
    <table class="min-w-full">
      <thead>
        <tr>
          <th>Type</th><th>Before</th><th>After</th><th>Txn Ref</th><th>Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="m in mutations.data" :key="m.id">
          <td><!-- type icon based --></td>
          <td>{{ m.before_balance }}</td>
          <td>{{ m.after_balance }}</td>
          <td class="font-mono">{{ m.payment_reference }}</td>
          <td>{{ m.created_at }}</td>
        </tr>
      </tbody>
    </table>
    <!-- Pagination placeholder -->
    <div class="flex justify-between items-center mt-2">
      <span class="text-xs text-secondary">Showing {{ mutations.from }}-{{ mutations.to }} of {{ mutations.total }}</span>
    </div>
  </div>
</template>


<script setup>
defineProps({ data: Object });
</script>
<template>
  <div class="rounded-xl p-6 cosmic-card shadow cosmic-starfield space-y-3">
    <h2 class="font-bold text-primary mb-2">Referral Dashboard</h2>
    <div class="rounded bg-content-background/80 p-2">
      <div>Your link: <span class="font-mono text-primary cursor-pointer">{{ data?.referral_code ?? '-' }}</span></div>
      <div>Referrals: {{ data?.referredUsers?.length ?? 0 }}</div>
    </div>
    <div>
      <h3 class="font-semibold text-secondary mb-1">Earnings Breakdown</h3>
      <div class="text-sm text-secondary">[Tiered commissions and withdrawal UI]</div>
    </div>
  </div>
</template>

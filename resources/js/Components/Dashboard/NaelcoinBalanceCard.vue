
<script setup>
defineProps({ balances: Object });
</script>
<template>
  <div class="rounded-xl p-6 cosmic-card relative shadow flex flex-col items-center cosmic-starfield space-y-2">
    <div class="text-center">
      <div class="text-xs font-light text-secondary">Your NaelCoin Balance</div>
      <div class="flex items-center justify-center space-x-2">
        <span class="text-3xl font-bold text-primary animate-pulse cosmic-glow">Rp{{ balances[Object.keys(balances).at(-1)] ?? '0' }}</span>
        <span class="ml-1 text-xs text-secondary font-medium">IDR</span>
      </div>
    </div>
    <button class="mt-3 px-4 py-2 rounded cosmic-button bg-primary text-white font-bold hover:animate-warp inline-flex items-center">
      <span>Top-up</span>
    </button>
    <div class="mt-3 w-full">
      <!-- Replace with mini-graph of 7-day balance -->
      <div class="bg-gradient-to-r from-primary/10 to-content-background/80 h-8 rounded cosmic-starfield"></div>
    </div>
  </div>
</template>

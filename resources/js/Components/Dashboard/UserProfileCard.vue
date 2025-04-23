
<script setup>
defineProps({ user: Object });
</script>
<template>
  <div class="rounded-xl p-6 cosmic-card flex flex-col items-center shadow cosmic-starfield">
    <div class="w-24 h-24 rounded-full border-4 border-gradient-primary mb-4 cosmic-glow">
      <img :src="user.avatar || '/img/default-avatar.png'" alt="User avatar" class="w-full h-full rounded-full" />
    </div>
    <h2 class="text-xl font-bold text-primary mb-1">{{ user.name }}</h2>
    <div class="text-sm text-secondary mb-2">Joined: {{ user.created_at }}</div>
    <div>
      <span v-if="user.email_verified_at" class="px-2 py-1 bg-green-700 text-white text-xs rounded cosmic-glow">Verified</span>
      <span v-else class="px-2 py-1 bg-supernova text-white text-xs rounded">Not Verified</span>
    </div>
  </div>
</template>

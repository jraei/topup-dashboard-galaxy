<template>
  <div class="min-h-screen bg-content-background text-white flex flex-col">
    <!-- Implement the cosmic navigation bar -->
    <NavigationBar />
    
    <!-- Main content -->
    <main class="flex-grow">
      <slot />
    </main>
    
    <!-- Footer (keeping the original footer if it exists) -->
    <footer class="bg-footer-background py-6 border-t border-primary/10">
      <div class="cosmic-container">
        <div class="text-center text-white/60 text-sm">
          <p>© 2025 Cosmic Top-Up Platform. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import NavigationBar from '@/Components/NavigationBar.vue';
</script>

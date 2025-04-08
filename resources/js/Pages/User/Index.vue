
<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Carousel from "@/Components/User/Banner/Carousel.vue";

defineProps({
  banners: {
    type: Array,
    default: () => [],
  },
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String
});
</script>

<template>
  <GuestLayout>
    <div class="min-h-screen bg-content">
      <section class="py-8">
        <h1 class="text-center text-3xl font-bold text-primary mb-6 cyberpunk-text">
          Welcome to <span class="text-secondary">CyberTopUp</span>
        </h1>
        
        <Carousel :banners="banners" />
      </section>
      
      <!-- Additional content will go here -->
    </div>
  </GuestLayout>
</template>

<style scoped>
.cyberpunk-text {
  text-shadow: 0 0 5px rgba(155, 135, 245, 0.8);
  letter-spacing: 1px;
}
</style>

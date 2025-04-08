
<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BannerCarousel from "@/Components/User/Banner/BannerCarousel.vue";
import MeteorBackground from "@/Components/User/Banner/MeteorBackground.vue";

defineProps({
    banners: {
        type: Array,
        default: () => [],
    },
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <GuestLayout>
        <div class="bg-content_background">
            <section class="relative min-h-[500px] overflow-hidden py-8 mx-auto">
                <MeteorBackground />
                <div class="mx-auto max-w-7xl">
                    <BannerCarousel :banners="banners" />
                </div>
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

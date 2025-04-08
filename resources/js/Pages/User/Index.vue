<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Carousel from "@/Components/User/Banner/Carousel.vue";
import MeteorEffects from "@/Components/User/Banner/MeteorEffects.vue";
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
            <section class="max-w-full py-8 mx-auto">
                <div class="mx-auto max-w-7xl">
                    <Carousel :banners="banners" />
                    <MeteorEffects />
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

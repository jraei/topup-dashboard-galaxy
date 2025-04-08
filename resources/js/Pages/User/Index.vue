<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BannerCarousel from "@/Components/User/Banner/BannerCarousel.vue";
import MeteorBackground from "@/Components/User/Banner/MeteorBackground.vue";
import CyberpunkGrid from "@/Components/User/Banner/CyberpunkGrid.vue";
import NebulasLayer from "@/Components/User/Banner/NebulasLayer.vue";
import PlanetsLayer from "@/Components/User/Banner/PlanetsLayer.vue";

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
            <section
                class="relative overflow-hidden w-full min-h-[400px] md:min-h-[500px] lg:min-h-[600px] py-8"
            >
                <!-- Background Layers -->
                <div class="absolute inset-0 z-0">
                    <CyberpunkGrid class="z-2" />
                    <NebulasLayer class="z-5" />
                    <MeteorBackground class="z-15" />
                    <PlanetsLayer class="z-10" />
                </div>

                <!-- Carousel Content -->
                <div class="relative z-30 max-w-6xl mx-auto">
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

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BannerCarousel from "@/Components/User/Banner/BannerCarousel.vue";
import MeteorBackground from "@/Components/User/Banner/MeteorBackground.vue";
import CyberpunkGrid from "@/Components/User/Banner/CyberpunkGrid.vue";
import NebulasLayer from "@/Components/User/Banner/NebulasLayer.vue";
import PlanetsLayer from "@/Components/User/Banner/PlanetsLayer.vue";
import StarfieldLayer from "@/Components/User/Banner/StarfieldLayer.vue";
import CometLayer from "@/Components/User/Banner/CometLayer.vue";
import FlashsaleSection from "@/Components/User/Flashsale/FlashsaleSection.vue";
import TrendingProducts from "@/Components/User/Product/TrendingProducts.vue";
import ProductCatalogSection from "@/Components/User/Product/ProductCatalogSection.vue";

const props = defineProps({
    banners: {
        type: Array,
        default: () => [],
    },
    flashsaleEvent: {
        type: Object,
        default: null,
    },
    serverTime: {
        type: String,
        default: null,
    },
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    popularProducts: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    catalogProducts: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <GuestLayout>
        <div class="bg-content_background">
            <section
                class="relative overflow-hidden w-full min-h-[200px] md:min-h-[400px] lg:min-h-[500px] py-8"
            >
                <!-- Enhanced Background Layers with Visual Priority System -->
                <div class="absolute inset-0 z-0">
                    <StarfieldLayer class="z-5" />
                    <PlanetsLayer class="z-15" />
                    <MeteorBackground class="z-20" />
                </div>

                <!-- Carousel Content -->
                <div class="relative z-30 mx-auto max-w-7xl">
                    <BannerCarousel :banners="banners" />
                </div>
            </section>

            <!-- Flash Sale Section -->
            <FlashsaleSection
                v-if="flashsaleEvent"
                :event="flashsaleEvent"
                :server-time="serverTime"
            />

            <!-- Trending Products Section -->
            <TrendingProducts
                v-if="popularProducts && popularProducts.length > 0"
                :products="popularProducts"
            />

            <!-- Product Catalog Section -->
            <!-- <ProductCatalogSection
                v-if="categories && categories.length > 0"
                :categories="categories"
                :products="catalogProducts"
            /> -->
        </div>
    </GuestLayout>
</template>

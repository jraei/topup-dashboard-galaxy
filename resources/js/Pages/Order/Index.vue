
<script setup>
import { ref, computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import CosmicParticles from "@/Components/User/Flashsale/CosmicParticles.vue";
import ProductBanner from "@/Components/Order/ProductBanner.vue";
import ProductInfoPanel from "@/Components/Order/ProductInfoPanel.vue";
import UserDataCard from "@/Components/Order/UserDataCard.vue";
import CheckoutSummary from "@/Components/Order/CheckoutSummary.vue";
import HelpContact from "@/Components/Order/HelpContact.vue";

const props = defineProps({
    produk: Object,
    layanans: Object,
    user: Object,
    inputFields: Array,
    waNumber: String,
});

// Calculate minimum price from layanans
const getMinimumPrice = () => {
    if (!props.layanans || props.layanans.length === 0) return 0;
    return Math.min(...props.layanans.map((item) => item.harga_jual));
};

const minimumPrice = computed(() => getMinimumPrice());
</script>

<template>
    <GuestLayout>
        <!-- Product Information Section -->
        <section class="relative w-full overflow-hidden">
            <!-- Banner -->
            <ProductBanner :banner="produk.banner" />

            <!-- Cosmic Product Panel -->
            <ProductInfoPanel :produk="produk" :min-price="minimumPrice" />
        </section>

        <!-- User Data Section -->
        <section class="relative px-4 py-8 overflow-hidden bg-content_background">
            <div class="absolute inset-0 z-0">
                <CosmicParticles />
            </div>
            
            <!-- Two-column layout on MD+ screens -->
            <div class="relative z-10 grid max-w-7xl grid-cols-1 mx-auto md:grid-cols-5 md:gap-6">
                <!-- Main column (80%) -->
                <div class="md:col-span-4 md:pr-8">
                    <UserDataCard :input-fields="inputFields" :produk="produk" />
                </div>
                
                <!-- Sidebar column (20%) -->
                <div class="space-y-4 md:col-span-1">
                    <div class="sticky top-24 space-y-4">
                        <HelpContact :wa-number="waNumber" />
                        <CheckoutSummary :produk="produk" :min-price="minimumPrice" />
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

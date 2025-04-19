
<script setup>
import { ref, computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import CosmicParticles from "@/Components/User/Flashsale/CosmicParticles.vue";
import ProductBanner from "@/Components/Order/ProductBanner.vue";
import ProductInfoPanel from "@/Components/Order/ProductInfoPanel.vue";
import UserDataCard from "@/Components/Order/UserDataCard.vue";
import ServiceList from "@/Components/Order/ServiceList.vue";
import QuantitySelector from "@/Components/Order/QuantitySelector.vue";
import CheckoutSummary from "@/Components/Order/CheckoutSummary.vue";
import HelpContact from "@/Components/Order/HelpContact.vue";
import { useToast } from "@/Composables/useToast";

const props = defineProps({
    produk: Object,
    layanans: Object,
    user: Object,
    inputFields: Array,
    waNumber: String,
    flashsaleEvents: Array,
});

// Initialize reactive state
const selectedService = ref(null);
const quantity = ref(1);
const isSticky = ref(false);
const { toast } = useToast();

// Handle service selection - quantity persists
const handleServiceSelection = (service) => {
    selectedService.value = service;
};

// Handle quantity update
const handleQuantityUpdate = (newQuantity) => {
    quantity.value = newQuantity;
};

// Handle checkout
const handleCheckout = () => {
    if (!selectedService.value) {
        toast.error("Please select a service first");
        return;
    }

    // TODO: Implement checkout functionality
    toast.success(
        `Processing order for ${quantity.value} x ${selectedService.value.nama_layanan}`
    );
};

// Handle sticky sidebar
const handleScroll = () => {
    const navbar = document.querySelector("nav"); // Adjust selector as needed
    const sidebar = document.querySelector(".sidebar");
    
    if (sidebar && navbar) {
        const navBottom = navbar.getBoundingClientRect().bottom;
        const sidebarTop = sidebar.getBoundingClientRect().top;
        const buffer = 10;

        isSticky.value = sidebarTop <= navBottom + buffer;
    }
};

// Event listeners for scroll
onMounted(() => {
    window.addEventListener("scroll", handleScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <GuestLayout>
        <!-- Product Information Section -->
        <section class="relative">
            <div class="relative w-full overflow-hidden">
                <ProductBanner :banner="produk.banner" />
            </div>
            <ProductInfoPanel :produk="produk" />
        </section>

        <!-- Main Content Section -->
        <section class="relative px-4 py-8 overflow-hidden bg-content_background">
            <div class="absolute inset-0 z-0">
                <CosmicParticles />
            </div>

            <!-- Two-column layout on MD+ screens -->
            <div class="relative z-10 grid grid-cols-1 mx-auto max-w-7xl md:grid-cols-5 md:gap-6">
                <!-- Main column (80%) -->
                <div class="md:col-span-4 md:pr-8">
                    <!-- User Data Card -->
                    <UserDataCard :input-fields="inputFields" :produk="produk" />

                    <!-- Service Selection -->
                    <ServiceList
                        :services="layanans"
                        :flashsale-events="flashsaleEvents"
                        @select-service="handleServiceSelection"
                    />

                    <!-- Purchase Quantity -->
                    <QuantitySelector
                        :disabled="!selectedService"
                        :max-quantity="1000"
                        :initial-quantity="quantity"
                        @update:quantity="handleQuantityUpdate"
                    />
                </div>

                <!-- Sidebar column (20%) -->
                <div class="space-y-4 md:col-span-1">
                    <div 
                        class="sidebar transition-transform duration-300"
                        :class="{ 'md:sticky md:top-[70px]': isSticky }"
                    >
                        <HelpContact :wa-number="waNumber" />
                        <CheckoutSummary
                            :produk="produk"
                            :selected-service="selectedService"
                            :quantity="quantity"
                            @checkout="handleCheckout"
                        />
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import CosmicCard from "./CosmicCard.vue";
import ServiceCard from "./ServiceCard.vue";
import { ShoppingBag, BadgePercent, Package } from "lucide-vue-next";
import Modal from "@/Components/Modal.vue";
import { Info } from "lucide-vue-next";

const props = defineProps({
    services: {
        type: Array,
        required: true,
    },
    flashsaleItems: {
        type: Array,
        default: () => [],
    },
    flashsaleEvents: {
        type: Array,
        default: () => [],
    },
    paketLayanans: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["selectService"]);

const selectedService = ref(null);

const selectService = (service) => {
    selectedService.value = service;
    emit("selectService", service);
};

// Group services by flashsale events or regular
const regularServices = computed(() => {
    return props.services;
});

const flashsaleServiceGroups = computed(() => {
    const groups = [];

    if (!props.flashsaleItems || props.flashsaleItems.length === 0) {
        return groups;
    }

    // Group flashsale items by event ID
    const groupedByEvent = {};

    props.flashsaleItems.forEach((service) => {
        const eventId = service.flashSaleItem.flashsale_event_id;

        if (!groupedByEvent[eventId]) {
            groupedByEvent[eventId] = {
                event:
                    props.flashsaleEvents.find((e) => e?.id === eventId) ?? {},
                services: [],
            };
        }

        groupedByEvent[eventId].services.push(service);
    });

    // Convert the object to an array
    Object.values(groupedByEvent).forEach((group) => {
        if (group.event && group.services.length > 0) {
            groups.push(group);
        }
    });

    return groups;
});

// Group services by packages
const packageServiceGroups = computed(() => {
    const groups = [];

    if (!props.paketLayanans || props.paketLayanans.length === 0) {
        return groups;
    }

    // Sort packages by display_order
    const sortedPackages = [...props.paketLayanans].sort((a, b) => 
        (a.display_order || 0) - (b.display_order || 0)
    );

    sortedPackages.forEach((paket) => {
        if ((paket.layanans && paket.layanans.length > 0) || 
            (paket.fusionServices && paket.fusionServices.length > 0)) {
            groups.push({
                package: paket,
                services: paket.layanans || [],
                fusionServices: paket.fusionServices || [],
            });
        }
    });

    return groups;
});

const openHelpModal = ref(false);
</script>

<template>
    <CosmicCard title="Pilih Layanan" :stepNumber="2">
        <div class="space-y-6">
            <!-- Service Packages -->
            <div
                v-for="group in packageServiceGroups"
                :key="group.package.id"
                class="space-y-3"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Package class="w-5 h-5 text-secondary" />
                        <h4 class="text-white">
                            {{ group.package.judul_paket }}
                        </h4>
                    </div>
                    <!-- <span
                        class="px-3 py-1 text-xs rounded-full bg-primary/50 text-primary-text/80"
                    >
                        {{ group.services.length }} services
                    </span> -->
                    <Info
                        class="inline-block w-6 h-6 cursor-pointer text-primary"
                        @click="openHelpModal = true"
                    />
                </div>

                <Modal
                    :show="openHelpModal"
                    @close="openHelpModal = false"
                    max-width="2xl"
                >
                    <div
                        class="p-6 border shadow-xl rounded-2xl bg-content_background/30 border-secondary/20 backdrop-blur"
                    >
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-white">
                                Panduan
                            </h2>
                            <button
                                @click="openHelpModal = false"
                                class="text-gray-400 hover:text-white"
                            >
                                &times;
                            </button>
                        </div>
                        <p class="text-gray-400">
                            {{ group.package.informasi }}
                        </p>
                    </div>
                </Modal>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4">
                    <ServiceCard
                        v-for="service in group.services"
                        :key="service.id"
                        :service="service"
                        :isSelected="
                            selectedService && selectedService.id === service.id
                        "
                        :isPackage="true"
                        @select="selectService"
                    />
                    <!-- Fusion Services -->
                    <ServiceCard
                        v-for="fusion in group.fusionServices"
                        :key="`fusion-${fusion.id}`"
                        :service="{
                            id: `fusion-${fusion.id}`,
                            nama_layanan: fusion.nama_fusion,
                            deskripsi: fusion.deskripsi,
                            harga_jual: fusion.calculated_price,
                            isFusion: true,
                            fusionData: fusion
                        }"
                        :isSelected="
                            selectedService && selectedService.id === `fusion-${fusion.id}`
                        "
                        :isFusion="true"
                        @select="selectService"
                    />
                </div>
            </div>

            <!-- Flashsale Services -->
            <div
                v-for="group in flashsaleServiceGroups"
                :key="group.event.id"
                class="space-y-3"
            >
                <div class="flex items-center space-x-2">
                    <BadgePercent
                        class="w-5 h-5 text-secondary animate-pulse"
                    />
                    <h4 class="text-white">{{ group.event.event_name }}</h4>
                </div>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4">
                    <ServiceCard
                        v-for="service in group.services"
                        :key="service.id"
                        :service="service"
                        :isSelected="
                            selectedService && selectedService.id === service.id
                        "
                        :isFlashsale="true"
                        @select="selectService"
                    />
                </div>
            </div>

            <!-- Regular Services -->
            <div v-if="regularServices.length > 0" class="space-y-3">
                <div class="flex items-center space-x-2">
                    <ShoppingBag class="w-5 h-5 text-secondary" />
                    <h4 class="text-white">Best Seller</h4>
                </div>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4">
                    <ServiceCard
                        v-for="service in regularServices"
                        :key="service.id"
                        :service="service"
                        :isSelected="
                            selectedService && selectedService.id === service.id
                        "
                        @select="selectService"
                    />
                </div>
            </div>

            <!-- No services message -->
            <div
                v-if="
                    regularServices.length === 0 &&
                    flashsaleServiceGroups.length === 0 &&
                    packageServiceGroups.length === 0
                "
                class="p-4 text-center text-primary-text/80"
            >
                No services available at the moment
            </div>
        </div>
    </CosmicCard>
</template>

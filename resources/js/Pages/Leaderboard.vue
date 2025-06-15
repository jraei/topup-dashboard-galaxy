<template>
    <GuestLayout>
        <div class="relative">
            <!-- Cosmic Navbar -->
            <!-- <CosmicNavbar /> -->

            <main
                class="min-h-screen pt-16 pb-32 overflow-hidden bg-gradient-to-b from-content_background to-transparent backdrop-blur-sm"
            >
                <!-- Cosmic Leaderboard Container -->
                <div class="container relative px-4 py-12 mx-auto">
                    <!-- Cosmic Decorations -->
                    <CosmicStars />

                    <h1
                        class="mb-4 text-2xl font-bold text-center text-white md:text-4xl"
                    >
                        <span
                            class="text-transparent bg-gradient-to-r from-primary to-secondary bg-clip-text"
                        >
                            Top 10 Pembelian Terbanyak di NAELSTORE
                        </span>
                    </h1>
                    <p class="mb-8 text-center text-primary-text/80">
                        Berikut ini adalah daftar 10 pembelian terbanyak yang
                        dilakukan oleh pelanggan kami. Data ini diambil dari
                        sistem kami dan selalu diperbaharui.
                    </p>

                    <!-- Tabs Container -->
                    <CosmicTabs
                        :time-periods="['Hari Ini', 'Minggu Ini', 'Bulan Ini']"
                        :active-tab="activeTab"
                        @tab-change="handleTabChange"
                    />

                    <!-- Leaderboard Rankings -->
                    <CosmicLeaderboardRankings
                        :rankings="currentRankings"
                        :loading="loading"
                    />

                    <!-- Footer Navigation -->
                    <div class="mt-12 text-center">
                        <Link
                            :href="route('index')"
                            class="inline-flex items-center px-6 py-3 space-x-2 text-lg text-white transition-all duration-300 border rounded-full bg-primary/40 hover:bg-primary/60 backdrop-blur-sm border-primary/30"
                        >
                            <span>Kembali</span>
                        </Link>
                    </div>
                </div>
            </main>

            <!-- Cosmic Footer -->
            <!-- <CosmicFooter /> -->
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import CosmicNavbar from "@/Components/User/Navigation/CosmicNavbar.vue";
import CosmicFooter from "@/Components/User/Footer/CosmicFooter.vue";
import CosmicTabs from "@/Components/Leaderboard/CosmicTabs.vue";
import CosmicLeaderboardRankings from "@/Components/Leaderboard/CosmicLeaderboardRankings.vue";
import CosmicStars from "@/Components/Leaderboard/CosmicStars.vue";
import { useToast } from "@/Composables/useToast";
import GuestLayout from "@/Layouts/GuestLayout.vue";

// Define props
const props = defineProps({
    daily: Array,
    weekly: Array,
    monthly: Array,
    serverTime: String,
});

const { toast } = useToast();
const loading = ref(false);
const activeTab = ref("Hari Ini");

// Compute current rankings based on active tab
const currentRankings = computed(() => {
    switch (activeTab.value) {
        case "Hari Ini":
            return props.daily || [];
        case "Minggu Ini":
            return props.weekly || [];
        case "Bulan Ini":
            return props.monthly || [];
        default:
            return [];
    }
});

// Handle tab change
const handleTabChange = (tab) => {
    loading.value = true;
    setTimeout(() => {
        activeTab.value = tab;
        loading.value = false;
    }, 300);
};
</script>

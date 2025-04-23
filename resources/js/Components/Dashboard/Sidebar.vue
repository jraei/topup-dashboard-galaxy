
<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

// Lucide icon names allowed
import { Compass, Ledger, "EnergyTransfer", "OrbitalNetwork" } from "lucide-vue-next"; 

const props = defineProps({
    collapsed: Boolean,
});
const emit = defineEmits(["toggle"]);

// Route list for navigation
const navItems = [
    {
        name: "Dashboard",
        route: "dashboard",
        icon: Compass,
    },
    {
        name: "Transaksi",
        route: "dashboard.transactions",
        icon: Ledger,
    },
    {
        name: "Mutasi",
        route: "dashboard.mutations",
        icon: "EnergyTransfer",
    },
    {
        name: "Afiliasi",
        route: "dashboard.affiliate",
        icon: "OrbitalNetwork",
    }
];

const page = usePage();
const currentRoute = computed(() => page.url);
</script>

<template>
    <aside
        :class="[
            'hidden lg:flex flex-col bg-[#161F3A] min-h-screen border-r border-primary/20 shadow-xl z-30 transition-all duration-300',
            props.collapsed ? 'w-20' : 'w-56'
        ]"
    >
        <!-- VSCredits Logo Box -->
        <div class="flex items-center gap-2 px-5 py-4">
            <img src="/favicon.ico" class="w-7 h-7" />
            <span v-if="!props.collapsed" class="font-bold text-white text-lg">VSCredits</span>
        </div>
        <!-- Nav -->
        <nav class="flex-1 mt-3 space-y-2">
            <div v-for="item in navItems" :key="item.name">
                <Link
                    :href="route(item.route)"
                    :class="[
                        'flex items-center px-4 py-2 rounded-lg transition-all font-medium gap-3 group',
                        route().current(item.route)
                            ? 'bg-gradient-to-r from-primary/50 to-secondary/40 text-white ring-2 ring-primary/70 animate-pulse'
                            : 'text-primary-text/80 hover:bg-primary/10 hover:text-secondary'
                    ]"
                >
                    <component :is="item.icon" class="w-6 h-6" aria-hidden="true" />
                    <span v-if="!props.collapsed" class="text-base">{{ item.name }}</span>
                </Link>
            </div>
        </nav>
        <!-- Collapse Button -->
        <button
            class="mx-auto my-4 p-2 bg-none text-secondary hover:text-primary transition"
            @click="$emit('toggle')"
            aria-label="Toggle sidebar"
        >
            <svg :class="['w-6 h-6', props.collapsed ? 'rotate-180' : '']" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </aside>
</template>

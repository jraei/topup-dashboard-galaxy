
<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import CosmicNavbar from "@/Components/User/Navigation/CosmicNavbar.vue";
import CosmicFooter from "@/Components/User/Footer/CosmicFooter.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-content_background">
            <!-- Cosmic Navigation System -->
            <CosmicNavbar />

            <!-- Page Heading -->
            <header
                class="bg-white shadow dark:bg-gray-800"
                v-if="$slots.header"
            >
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
            
            <!-- Cosmic Footer -->
            <CosmicFooter />
        </div>
    </div>
</template>


<template>
    <nav
        class="hidden py-2 border-t-2 md:block border-primary/60 text-primary-text"
    >
        <div class="flex items-center justify-between max-w-6xl px-3 mx-auto">
            <ul class="flex space-x-4">
                <li v-for="(link, index) in navLinks" :key="index">
                    <component
                        :is="link.dropdown ? 'div' : Link"
                        :to="link.dropdown ? undefined : route(link.route)"
                        class="relative flex items-center px-3 py-1 space-x-2 transition-all rounded-lg cursor-pointer group hover:bg-primary/10"
                        :class="{ 'text-primary': link.active }"
                    >
                        <span class="text-xl">{{ link.icon }}</span>
                        <span>{{ link.name }}</span>
                        <span v-if="link.dropdown" class="ml-1">▼</span>

                        <!-- Dropdown menu for desktop - This is the element we need to fix -->
                        <div 
                            v-if="link.dropdown" 
                            class="absolute left-0 z-50 hidden p-2 mt-1 space-y-1 bg-header_background border border-primary/20 rounded-lg shadow-lg group-hover:block top-full min-w-[150px]"
                            style="transform: none; z-index: 999;"
                        >
                            <Link
                                v-for="(item, i) in link.dropdown"
                                :key="i"
                                :to="route(item.route)"
                                class="flex items-center p-2 space-x-2 rounded-md hover:bg-primary/10"
                            >
                                <span class="text-lg">{{ item.icon }}</span>
                                <span>{{ item.name }}</span>
                            </Link>
                        </div>
                    </component>
                </li>
            </ul>

            <div class="hidden md:block">
                <!-- Search component -->
                <CosmicSearch />
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import CosmicSearch from "./CosmicSearch.vue";

defineProps({
    navLinks: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped>
/* Fix for dropdown menu positioning */
.group:hover .group-hover\:block {
    display: block;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 999;
    /* Improved shadow for better visibility */
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 
                0 8px 10px -6px rgba(0, 0, 0, 0.2);
}
</style>

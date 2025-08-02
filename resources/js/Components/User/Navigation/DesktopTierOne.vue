<script setup>
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import ProductSearch from "./ProductSearch.vue";
import CosmicIcon from "./CosmicIcon.vue";

defineProps({
    isScrolled: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const logoHeader = page.props.web_details.logo_header;

const isSearchFocused = ref(false);
</script>

<template>
    <div class="border-b border-primary/20">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo -->
                <div class="flex items-center flex-shrink-0">
                    <Link :href="route('index')" class="relative group">
                        <div
                            class="absolute inset-0 transition-all duration-300 rounded-full opacity-0 bg-primary/10 group-hover:opacity-100 filter blur-md group-hover:scale-110"
                        ></div>
                        <img :src="logoHeader" alt="logo" class="w-12 h-12" />
                    </Link>
                </div>

                <!-- Center: Search -->
                <div class="flex-1 max-w-3xl mx-6">
                    <ProductSearch
                        @focus="isSearchFocused = true"
                        @blur="isSearchFocused = false"
                        :is-focused="isSearchFocused"
                    />
                </div>

                <!-- Right: Language Selector -->
                <div class="flex items-center">
                    <div
                        class="inline-flex items-center space-x-2 bg-gray-800 text-white px-4 py-2 rounded-full"
                    >
                        <!-- Bendera Indonesia -->
                        <div
                            class="w-5 h-5 rounded-full overflow-hidden border border-white"
                        >
                            <div class="h-1/2 bg-red-600"></div>
                            <div class="h-1/2 bg-white"></div>
                        </div>

                        <!-- Teks -->
                        <span class="text-sm font-semibold tracking-wide"
                            >ID / IDR</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

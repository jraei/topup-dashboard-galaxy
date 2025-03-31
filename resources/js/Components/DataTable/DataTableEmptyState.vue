
<script setup>
import { computed, inject } from 'vue';

const searchQuery = inject('searchQuery', '');

const emptyStateMessage = computed(() => {
    return searchQuery ? 'Try adjusting your search query' : 'No records exist yet';
});
</script>

<template>
    <div class="flex flex-col items-center">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-12 h-12 mb-4 text-gray-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
            />
        </svg>
        <p class="text-lg text-gray-400">No data found</p>
        <p class="mt-1 text-sm text-gray-500">{{ emptyStateMessage }}</p>
    </div>
</template>

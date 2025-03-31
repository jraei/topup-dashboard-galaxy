
<script setup>
defineProps({
    searchable: {
        type: Boolean,
        default: true,
    },
});

defineEmits(['update:searchQuery']);

defineModel('searchQuery');
</script>

<template>
    <div class="flex flex-col items-center justify-between p-4 space-y-4 border-b border-gray-700 bg-dark-lighter sm:flex-row sm:space-y-0">
        <h2 class="text-lg font-semibold text-white">
            <slot name="title">Data Table</slot>
        </h2>

        <div class="flex w-full space-x-2 sm:w-auto">
            <div
                v-if="searchable"
                class="relative flex-grow sm:flex-grow-0 sm:w-64"
            >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </div>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search..."
                    class="block w-full py-2 pl-10 pr-3 text-gray-200 placeholder-gray-400 border border-gray-700 rounded-lg bg-dark-sidebar focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                />
            </div>

            <slot name="addButton">
                <button class="flex items-center px-4 py-2 space-x-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        />
                    </svg>
                    <span>Add New</span>
                </button>
            </slot>
        </div>
    </div>
</template>

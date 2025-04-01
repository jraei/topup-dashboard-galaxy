
<script setup>
import { ref, watch, computed } from "vue";

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    searchable: {
        type: Boolean,
        default: true,
    },
    pagination: {
        type: Object,
        default: () => ({ current_page: 1, per_page: 10, total: 0 }),
    },
});

const emit = defineEmits(["edit", "delete", "view", "page-change", "search"]);

const searchQuery = ref("");
const sortColumn = ref("");
const sortDirection = ref("asc");

const filteredData = computed(() => {
    let data = [...props.data];

    if (props.searchable && searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        data = data.filter((item) => {
            return props.columns.some((column) => {
                const value = item[column.key];
                return value && String(value).toLowerCase().includes(query);
            });
        });
    }

    if (sortColumn.value) {
        data.sort((a, b) => {
            const valueA = a[sortColumn.value];
            const valueB = b[sortColumn.value];

            if (valueA == null || valueB == null) return 0;

            const valA = isNaN(valueA)
                ? String(valueA).toLowerCase()
                : Number(valueA);
            const valB = isNaN(valueB)
                ? String(valueB).toLowerCase()
                : Number(valueB);

            if (valA < valB) return sortDirection.value === "asc" ? -1 : 1;
            if (valA > valB) return sortDirection.value === "asc" ? 1 : -1;
            return 0;
        });
    }

    return data;
});

const handleSearch = () => {
    emit("search", searchQuery.value);
};

const handleSort = (column) => {
    if (sortColumn.value === column.key) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortColumn.value = column.key;
        sortDirection.value = "asc";
    }
};

const handleAction = (action, item) => {
    emit(action, item);
};

let searchTimeout;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        handleSearch();
    }, 300);
});
</script>

<template>
    <div class="w-full">
        <!-- Table Header with Search -->
        <div
            class="flex flex-col items-center justify-between p-4 space-y-4 border-b border-gray-700 bg-dark-lighter sm:flex-row sm:space-y-0"
        >
            <h2 class="text-lg font-semibold text-white">
                <slot name="title">Data Table</slot>
            </h2>

            <div class="flex w-full space-x-2 sm:w-auto">
                <div
                    v-if="searchable"
                    class="relative flex-grow sm:flex-grow-0 sm:w-64"
                >
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                    >
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
                        class="block w-full py-2 pl-10 pr-3 text-gray-200 placeholder-gray-400 border rounded-lg border-secondary/50 bg-dark-sidebar focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                </div>

                <slot name="addButton">
                    <button
                        class="flex items-center px-4 py-2 space-x-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                    >
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

        <!-- Table Body - Improved horizontal scrolling container -->
        <div class="overflow-x-auto w-full scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full table-auto divide-y divide-gray-700">
                        <thead class="bg-dark-lighter">
                            <tr>
                                <!-- ID Column -->
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-300 uppercase whitespace-nowrap"
                                >
                                    ID
                                </th>
                                <th
                                    v-for="column in columns.filter(
                                        (col) => col.key !== 'id'
                                    )"
                                    :key="column.key"
                                    scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-300 uppercase transition-colors cursor-pointer hover:text-white whitespace-nowrap"
                                    @click="handleSort(column)"
                                >
                                    <div class="flex items-center space-x-1">
                                        <span>{{ column.label }}</span>
                                        <!-- Sort Direction Indicator -->
                                        <div
                                            v-if="sortColumn === column.key"
                                            class="ml-1"
                                        >
                                            <svg
                                                v-if="sortDirection === 'asc'"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 15l7-7 7 7"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 9l-7 7-7-7"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-300 uppercase sticky right-0 bg-dark-lighter whitespace-nowrap z-10"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-dark-card">
                            <tr
                                v-for="(item, index) in filteredData"
                                :key="index"
                                class="transition-colors hover:bg-dark-lighter"
                            >
                                <!-- ID Column -->
                                <td class="px-6 py-4 text-gray-200 whitespace-nowrap">
                                    {{ index + 1 }}
                                </td>
                                <td
                                    v-for="column in columns.filter(
                                        (col) => col.key !== 'id'
                                    )"
                                    :key="`${index}-${column.key}`"
                                    class="px-6 py-4 whitespace-nowrap"
                                >
                                    <slot
                                        :name="`cell(${column.key})`"
                                        :item="item"
                                        :column="column"
                                    >
                                        <div
                                            v-if="column.format"
                                            v-html="
                                                column.format(item[column.key], item)
                                            "
                                        ></div>
                                        <span v-else class="text-gray-200 truncate max-w-[150px] inline-block">{{
                                            item[column.key]
                                        }}</span>
                                    </slot>
                                </td>
                                <td
                                    class="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap sticky right-0 bg-dark-card z-10"
                                >
                                    <slot name="actions" :item="item">
                                        <button
                                            @click="handleAction('view', item)"
                                            class="transition-colors text-secondary hover:text-secondary-hover"
                                        >
                                            View
                                        </button>
                                        <button
                                            @click="handleAction('edit', item)"
                                            class="transition-colors text-primary hover:text-primary-hover"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="handleAction('delete', item)"
                                            class="text-red-400 transition-colors hover:text-red-500"
                                        >
                                            Delete
                                        </button>
                                    </slot>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="filteredData.length === 0">
                                <td
                                    :colspan="columns.length + 1"
                                    class="px-6 py-12 text-center"
                                >
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
                                        <p class="text-lg text-gray-400">
                                            No data found
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            <span v-if="searchQuery"
                                                >Try adjusting your search query</span
                                            >
                                            <span v-else>No records exist yet</span>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Custom styling for scrollbars */
.scrollbar-thin::-webkit-scrollbar {
    height: 6px;
}

.scrollbar-thumb-gray-700::-webkit-scrollbar-thumb {
    background-color: rgba(55, 65, 81, 0.5);
    border-radius: 3px;
}

.scrollbar-thumb-gray-700::-webkit-scrollbar-thumb:hover {
    background-color: rgba(75, 85, 99, 0.7);
}

.scrollbar-track-transparent::-webkit-scrollbar-track {
    background-color: transparent;
}

/* Use CSS to ensure the sticky action column works properly */
.sticky {
    position: sticky;
    z-index: 1;
    right: 0;
}

/* Ensure proper truncation for cell content */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>


<script setup>
defineProps({
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    },
    sortColumn: {
        type: String,
        default: "",
    },
    sortDirection: {
        type: String,
        default: "asc",
    },
});

const emit = defineEmits(["sort", "action"]);

const handleSort = (column) => {
    emit("sort", column);
};

const handleAction = (action, item) => {
    emit("action", action, item);
};
</script>

<template>
    <div class="w-full overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-dark-lighter">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-300 uppercase transition-colors cursor-pointer hover:text-white whitespace-nowrap"
                        @click="handleSort(column)"
                    >
                        <div class="flex items-center space-x-1">
                            <span>{{ column.label }}</span>
                            <!-- Sort Direction Indicator -->
                            <div v-if="sortColumn === column.key" class="ml-1">
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
                        class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-300 uppercase whitespace-nowrap"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700 bg-dark-card">
                <tr
                    v-for="(item, index) in data"
                    :key="index"
                    class="transition-colors hover:bg-dark-lighter"
                >
                    <td
                        v-for="column in columns"
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
                                v-html="column.format(item[column.key], item)"
                            ></div>
                            <span v-else class="text-gray-200">{{ item[column.key] }}</span>
                        </slot>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
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
                <tr v-if="data.length === 0">
                    <td :colspan="columns.length + 1" class="px-6 py-12 text-center">
                        <DataTableEmptyState />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

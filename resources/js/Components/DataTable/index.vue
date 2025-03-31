
<script setup>
import DataTableHeader from './DataTableHeader.vue';
import DataTableBody from './DataTableBody.vue';
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

// If the component is handling search itself (not via parent)
const filteredData = computed(() => {
    if (!props.searchable || !searchQuery.value) return props.data;

    const query = searchQuery.value.toLowerCase();
    return props.data.filter((item) => {
        return props.columns.some((column) => {
            const value = item[column.key];
            return value && String(value).toLowerCase().includes(query);
        });
    });
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

// Debounce search input
let searchTimeout;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        handleSearch();
    }, 300);
});
</script>

<template>
    <div class="overflow-hidden rounded-lg border border-gray-700 bg-dark-card">
        <!-- Table Header with Search -->
        <DataTableHeader 
            :searchable="searchable" 
            v-model:search-query="searchQuery"
        >
            <template #title>
                <slot name="title">Data Table</slot>
            </template>
            <template #addButton>
                <slot name="addButton"></slot>
            </template>
        </DataTableHeader>

        <!-- Table Body with Horizontal Scroll on Small Screens -->
        <DataTableBody 
            :columns="columns" 
            :data="filteredData" 
            :sort-column="sortColumn"
            :sort-direction="sortDirection"
            @sort="handleSort"
            @action="handleAction"
        >
            <template v-for="column in columns" #[`cell(${column.key})`]="slotProps">
                <slot :name="`cell(${column.key})`" v-bind="slotProps"></slot>
            </template>
            <template #actions="slotProps">
                <slot name="actions" v-bind="slotProps"></slot>
            </template>
        </DataTableBody>
    </div>
</template>

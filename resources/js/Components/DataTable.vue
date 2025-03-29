
<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  columns: {
    type: Array,
    required: true
  },
  searchable: {
    type: Boolean,
    default: true
  },
  pagination: {
    type: Object,
    default: () => ({ current_page: 1, per_page: 10, total: 0 })
  }
});

const emit = defineEmits(['edit', 'delete', 'view', 'page-change', 'search']);

const searchQuery = ref('');
const sortColumn = ref('');
const sortDirection = ref('asc');

// If the component is handling search itself (not via parent)
const filteredData = computed(() => {
  if (!props.searchable || !searchQuery.value) return props.data;
  
  const query = searchQuery.value.toLowerCase();
  return props.data.filter(item => {
    return props.columns.some(column => {
      const value = item[column.key];
      return value && String(value).toLowerCase().includes(query);
    });
  });
});

const handleSearch = () => {
  emit('search', searchQuery.value);
};

const handleSort = (column) => {
  if (sortColumn.value === column.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column.key;
    sortDirection.value = 'asc';
  }
};

const handleAction = (action, item) => {
  emit(action, item);
};

const handlePageChange = (page) => {
  emit('page-change', page);
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
  <div class="overflow-hidden">
    <!-- Table Header with Search -->
    <div class="p-4 bg-dark-lighter border-b border-gray-700 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
      <h2 class="text-lg font-semibold text-white">
        <slot name="title">Data Table</slot>
      </h2>
      
      <div class="flex w-full sm:w-auto space-x-2">
        <div v-if="searchable" class="relative flex-grow sm:flex-grow-0 sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search..."
            class="block w-full pl-10 pr-3 py-2 border border-gray-700 rounded-lg bg-dark-sidebar text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          >
        </div>
        
        <slot name="actions">
          <button class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg shadow-lg hover:shadow-glow-primary transition-all duration-200 flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>Add New</span>
          </button>
        </slot>
      </div>
    </div>
    
    <!-- Table Body -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-700">
        <thead class="bg-dark-lighter">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:text-white transition-colors"
              @click="handleSort(column)"
            >
              <div class="flex items-center space-x-1">
                <span>{{ column.label }}</span>
                <!-- Sort Direction Indicator -->
                <div v-if="sortColumn === column.key" class="ml-1">
                  <svg v-if="sortDirection === 'asc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-dark-card divide-y divide-gray-700">
          <tr v-for="(item, index) in filteredData" :key="index" class="hover:bg-dark-lighter transition-colors">
            <td v-for="column in columns" :key="`${index}-${column.key}`" class="px-6 py-4 whitespace-nowrap">
              <slot :name="`cell(${column.key})`" :item="item" :column="column">
                <div v-if="column.format" v-html="column.format(item[column.key], item)"></div>
                <span v-else class="text-gray-200">{{ item[column.key] }}</span>
              </slot>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <slot name="actions" :item="item">
                <button 
                  @click="handleAction('view', item)" 
                  class="text-secondary hover:text-secondary-hover transition-colors"
                >
                  View
                </button>
                <button 
                  @click="handleAction('edit', item)" 
                  class="text-primary hover:text-primary-hover transition-colors"
                >
                  Edit
                </button>
                <button 
                  @click="handleAction('delete', item)" 
                  class="text-red-400 hover:text-red-500 transition-colors"
                >
                  Delete
                </button>
              </slot>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="filteredData.length === 0">
            <td :colspan="columns.length + 1" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-lg text-gray-400">No data found</p>
                <p class="text-sm text-gray-500 mt-1">
                  <span v-if="searchQuery">Try adjusting your search query</span>
                  <span v-else>No records exist yet</span>
                </p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="px-6 py-4 border-t border-gray-700 flex items-center justify-between">
      <div class="text-sm text-gray-400">
        Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }} to 
        {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of 
        {{ pagination.total }} results
      </div>
      
      <div class="flex space-x-2">
        <button 
          @click="handlePageChange(pagination.current_page - 1)" 
          :disabled="pagination.current_page === 1" 
          :class="[
            'px-3 py-1 rounded-md text-sm',
            pagination.current_page === 1 
              ? 'bg-dark-lighter text-gray-500 cursor-not-allowed' 
              : 'bg-dark-lighter text-gray-300 hover:bg-primary/20 hover:text-white'
          ]"
        >
          Previous
        </button>
        
        <button 
          @click="handlePageChange(pagination.current_page + 1)" 
          :disabled="pagination.current_page * pagination.per_page >= pagination.total" 
          :class="[
            'px-3 py-1 rounded-md text-sm',
            pagination.current_page * pagination.per_page >= pagination.total 
              ? 'bg-dark-lighter text-gray-500 cursor-not-allowed' 
              : 'bg-dark-lighter text-gray-300 hover:bg-primary/20 hover:text-white'
          ]"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

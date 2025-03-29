
<script setup>
import { ref, computed, watch } from 'vue';

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
  sortable: {
    type: Boolean,
    default: true
  },
  pagination: {
    type: Boolean,
    default: true
  },
  itemsPerPage: {
    type: Number,
    default: 10
  }
});

const emit = defineEmits(['edit', 'delete', 'view']);

const search = ref('');
const currentPage = ref(1);
const rowsPerPage = ref(props.itemsPerPage);
const sortColumn = ref('');
const sortDirection = ref('asc');

const handleSort = (column) => {
  if (!props.sortable || !column.sortable) return;
  
  if (sortColumn.value === column.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column.key;
    sortDirection.value = 'asc';
  }
};

const handleEdit = (item) => {
  emit('edit', item);
};

const handleDelete = (item) => {
  emit('delete', item);
};

const handleView = (item) => {
  emit('view', item);
};

const filteredData = computed(() => {
  let result = [...props.data];
  
  if (search.value && props.searchable) {
    const searchLower = search.value.toLowerCase();
    result = result.filter(item => {
      return props.columns.some(column => {
        const value = item[column.key];
        return value && value.toString().toLowerCase().includes(searchLower);
      });
    });
  }
  
  if (sortColumn.value && props.sortable) {
    result.sort((a, b) => {
      const aValue = a[sortColumn.value];
      const bValue = b[sortColumn.value];
      
      if (sortDirection.value === 'asc') {
        return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
      } else {
        return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
      }
    });
  }
  
  return result;
});

const paginatedData = computed(() => {
  if (!props.pagination) return filteredData.value;
  
  const start = (currentPage.value - 1) * rowsPerPage.value;
  const end = start + rowsPerPage.value;
  
  return filteredData.value.slice(start, end);
});

const totalPages = computed(() => {
  if (!props.pagination) return 1;
  return Math.ceil(filteredData.value.length / rowsPerPage.value);
});

const pageNumbers = computed(() => {
  if (totalPages.value <= 5) {
    return Array.from({ length: totalPages.value }, (_, i) => i + 1);
  }
  
  const current = currentPage.value;
  const total = totalPages.value;
  
  if (current <= 3) {
    return [1, 2, 3, 4, 5, '...', total];
  } else if (current >= total - 2) {
    return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
  } else {
    return [1, '...', current - 1, current, current + 1, '...', total];
  }
});

watch(filteredData, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value || 1;
  }
}, { deep: true });

watch(rowsPerPage, () => {
  currentPage.value = 1;
});
</script>

<template>
  <div class="bg-dark-800 rounded-xl shadow-lg border border-gray-700 overflow-hidden">
    <!-- Table Header -->
    <div class="p-4 bg-gradient-to-r from-primary-700/30 to-primary-600/10 border-b border-gray-700">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h3 class="text-lg font-semibold text-white">Results <span v-if="filteredData.length" class="text-sm font-normal text-gray-400">({{ filteredData.length }})</span></h3>
        
        <!-- Search Input -->
        <div v-if="searchable" class="relative w-full md:w-64">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </div>
          <input
            type="search"
            v-model="search"
            class="block w-full p-2 pl-10 text-sm text-white border border-gray-600 rounded-lg bg-dark-900 focus:ring-primary-500 focus:border-primary-500"
            placeholder="Search..."
          />
        </div>
      </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-700">
        <thead class="bg-dark-900">
          <tr>
            <th 
              v-for="column in columns" 
              :key="column.key"
              scope="col" 
              class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider"
              :class="{ 
                'cursor-pointer hover:bg-dark-800 transition-colors duration-200': sortable && column.sortable,
              }"
              @click="handleSort(column)"
            >
              <div class="flex items-center space-x-1">
                <span>{{ column.label }}</span>
                <span v-if="sortable && column.sortable">
                  <svg 
                    v-if="sortColumn === column.key" 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-4 w-4 transition-all duration-200 filter drop-shadow-glow"
                    :class="{ 'rotate-180': sortDirection === 'desc' }"
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                  >
                    <path d="m18 15-6-6-6 6"/>
                  </svg>
                  <svg 
                    v-else
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-4 w-4 text-gray-500"
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                  >
                    <path d="m18 15-6-6-6 6"/>
                  </svg>
                </span>
              </div>
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-dark-800 divide-y divide-gray-700">
          <tr v-for="(item, index) in paginatedData" :key="index" class="hover:bg-dark-700 transition-colors duration-200">
            <td 
              v-for="column in columns" 
              :key="`${index}-${column.key}`" 
              class="px-6 py-4 whitespace-nowrap text-sm"
            >
              <component 
                v-if="column.render" 
                :is="column.render(item)" 
              />
              <template v-else>
                {{ item[column.key] }}
              </template>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end space-x-2">
                <button @click="handleView(item)" class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                </button>
                <button @click="handleEdit(item)" class="text-green-400 hover:text-green-300 transition-colors duration-200">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                    <path d="m15 5 4 4" />
                  </svg>
                </button>
                <button @click="handleDelete(item)" class="text-red-400 hover:text-red-300 transition-colors duration-200">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18" />
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                    <line x1="10" x2="10" y1="11" y2="17" />
                    <line x1="14" x2="14" y1="11" y2="17" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="paginatedData.length === 0">
            <td :colspan="columns.length + 1" class="px-6 py-4 text-center text-sm text-gray-400">
              No results found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && filteredData.length > 0" class="px-4 py-3 border-t border-gray-700 bg-dark-900 flex flex-col sm:flex-row items-center justify-between">
      <div class="flex items-center mb-4 sm:mb-0">
        <label class="text-sm text-gray-400 mr-2">Rows per page:</label>
        <select 
          v-model="rowsPerPage" 
          class="bg-dark-800 border border-gray-600 text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      
      <div class="flex items-center space-x-1">
        <button 
          @click="currentPage = 1" 
          :disabled="currentPage === 1"
          class="p-2 rounded-lg hover:bg-dark-700 text-gray-400 hover:text-white"
          :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m11 17-5-5 5-5" />
            <path d="m18 17-5-5 5-5" />
          </svg>
        </button>
        <button 
          @click="currentPage--" 
          :disabled="currentPage === 1"
          class="p-2 rounded-lg hover:bg-dark-700 text-gray-400 hover:text-white"
          :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6" />
          </svg>
        </button>
        
        <div v-for="page in pageNumbers" :key="page" class="hidden md:block">
          <button 
            v-if="page !== '...'" 
            @click="currentPage = page"
            class="px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200"
            :class="currentPage === page 
              ? 'bg-primary-600 text-white shadow-glow' 
              : 'text-gray-400 hover:bg-dark-700 hover:text-white'"
          >
            {{ page }}
          </button>
          <span v-else class="px-1 text-gray-500">...</span>
        </div>
        
        <div class="md:hidden text-sm text-gray-400">
          Page {{ currentPage }} of {{ totalPages }}
        </div>
        
        <button 
          @click="currentPage++" 
          :disabled="currentPage === totalPages"
          class="p-2 rounded-lg hover:bg-dark-700 text-gray-400 hover:text-white"
          :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m9 18 6-6-6-6" />
          </svg>
        </button>
        <button 
          @click="currentPage = totalPages" 
          :disabled="currentPage === totalPages"
          class="p-2 rounded-lg hover:bg-dark-700 text-gray-400 hover:text-white"
          :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m13 17 5-5-5-5" />
            <path d="m6 17 5-5-5-5" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

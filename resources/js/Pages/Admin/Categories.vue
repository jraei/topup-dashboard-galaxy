<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

// Category data
const categories = ref([]);
const isLoading = ref(true);
const modalOpen = ref(false);
const editMode = ref(false);
const currentCategory = ref({
  id: null,
  name: '',
  slug: '',
  description: '',
  icon: '',
  status: true
});

// Pagination
const pagination = ref({
  current_page: 1,
  per_page: 10,
  total: 0
});

// Column definitions for DataTable
const columns = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'slug', label: 'Slug' },
  { key: 'description', label: 'Description' },
  { 
    key: 'status', 
    label: 'Status',
    format: (value) => {
      return value ? 
        '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs">Active</span>' : 
        '<span class="px-2 py-1 bg-red-500/20 text-red-400 rounded-full text-xs">Inactive</span>';
    }
  }
];

// Sample data - in a real app this would come from the backend API
onMounted(() => {
  // Simulate API fetch
  setTimeout(() => {
    categories.value = [
      { id: 1, name: 'Mobile Games', slug: 'mobile-games', description: 'Top up for mobile games', icon: 'game-icons', status: true },
      { id: 2, name: 'PC Games', slug: 'pc-games', description: 'Top up for PC games', icon: 'desktop', status: true },
      { id: 3, name: 'Console Games', slug: 'console-games', description: 'Top up for console games', icon: 'controller', status: false },
      { id: 4, name: 'Digital Vouchers', slug: 'digital-vouchers', description: 'Various digital vouchers', icon: 'ticket', status: true },
      { id: 5, name: 'E-Money', slug: 'e-money', description: 'Electronic money top up', icon: 'wallet', status: true }
    ];
    
    pagination.value = {
      current_page: 1,
      per_page: 10,
      total: categories.value.length
    };
    
    isLoading.value = false;
  }, 1000);
});

// Action handlers
const handleView = (category) => {
  console.log('View category:', category);
  // Implement view logic
};

const handleEdit = (category) => {
  currentCategory.value = {...category};
  editMode.value = true;
  modalOpen.value = true;
};

const handleDelete = (category) => {
  if (confirm(`Are you sure you want to delete "${category.name}"?`)) {
    console.log('Delete category:', category);
    // Implement delete logic
  }
};

const handleAddNew = () => {
  currentCategory.value = {
    id: null,
    name: '',
    slug: '',
    description: '',
    icon: '',
    status: true
  };
  editMode.value = false;
  modalOpen.value = true;
};

const handleSave = () => {
  console.log('Save category:', currentCategory.value);
  // Implement save logic
  
  modalOpen.value = false;
};

const handlePageChange = (page) => {
  pagination.value.current_page = page;
  // Implement page change logic
};

const handleSearch = (query) => {
  console.log('Search query:', query);
  // Implement search logic
};

// Generate slug from name
const generateSlug = (name) => {
  return name.toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w\-]+/g, '')
    .replace(/\-\-+/g, '-')
    .replace(/^-+/, '')
    .replace(/-+$/, '');
};
</script>

<template>
  <Head title="Categories" />

  <AdminLayout title="Categories">
    <div>
      <!-- DataTable Component -->
      <DataTable 
        :data="categories"
        :columns="columns"
        :pagination="pagination"
        @view="handleView"
        @edit="handleEdit"
        @delete="handleDelete"
        @page-change="handlePageChange"
        @search="handleSearch"
      >
        <template #title>Game Categories</template>
        
        <template #tableActions>
          <button 
            @click="handleAddNew"
            class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg shadow-lg hover:shadow-glow-primary transition-all duration-200 flex items-center space-x-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>Add Category</span>
          </button>
        </template>
        
        <!-- Custom Cell Templates -->
        <template #cell(name)="{ item }">
          <div class="flex items-center">
            <span class="font-medium text-white">{{ item.name }}</span>
          </div>
        </template>
        
        <template #cell(description)="{ item }">
          <div class="max-w-xs truncate text-gray-300">{{ item.description }}</div>
        </template>
        
        <!-- Custom Actions Template -->
        <template #rowActions="{ item }">
          <div class="flex space-x-3 justify-end">
            <button 
              @click="handleView(item)" 
              class="text-secondary hover:text-secondary-hover transition-colors"
              title="View Details"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
            <button 
              @click="handleEdit(item)" 
              class="text-primary hover:text-primary-hover transition-colors"
              title="Edit"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <button 
              @click="handleDelete(item)" 
              class="text-red-400 hover:text-red-500 transition-colors"
              title="Delete"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </template>
      </DataTable>

      <!-- Category Modal (Add/Edit) -->
      <div v-if="modalOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-black/75 transition-opacity" @click="modalOpen = false"></div>

          <div class="inline-block align-bottom bg-dark-card rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-700">
            <div class="px-6 py-5 border-b border-gray-700 flex justify-between items-center">
              <h3 class="text-xl font-medium text-white">{{ editMode ? 'Edit Category' : 'Add New Category' }}</h3>
              <button @click="modalOpen = false" class="text-gray-400 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="px-6 py-4">
              <form @submit.prevent="handleSave">
                <div class="mb-4">
                  <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Category Name</label>
                  <input 
                    type="text" 
                    id="name" 
                    v-model="currentCategory.name" 
                    class="w-full px-4 py-2 rounded-lg bg-dark-lighter border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    @input="currentCategory.slug = generateSlug(currentCategory.name)"
                    required
                  >
                </div>
                
                <div class="mb-4">
                  <label for="slug" class="block text-sm font-medium text-gray-300 mb-1">Slug</label>
                  <input 
                    type="text" 
                    id="slug" 
                    v-model="currentCategory.slug" 
                    class="w-full px-4 py-2 rounded-lg bg-dark-lighter border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                  >
                </div>
                
                <div class="mb-4">
                  <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                  <textarea 
                    id="description" 
                    v-model="currentCategory.description" 
                    class="w-full px-4 py-2 rounded-lg bg-dark-lighter border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    rows="3"
                  ></textarea>
                </div>
                
                <div class="mb-4">
                  <label for="icon" class="block text-sm font-medium text-gray-300 mb-1">Icon</label>
                  <input 
                    type="text" 
                    id="icon" 
                    v-model="currentCategory.icon" 
                    class="w-full px-4 py-2 rounded-lg bg-dark-lighter border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  >
                </div>
                
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                  <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                      <input 
                        type="radio" 
                        v-model="currentCategory.status" 
                        :value="true"
                        class="text-primary focus:ring-primary h-4 w-4"
                      >
                      <span class="ml-2 text-white">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                      <input 
                        type="radio" 
                        v-model="currentCategory.status" 
                        :value="false"
                        class="text-red-500 focus:ring-red-500 h-4 w-4"
                      >
                      <span class="ml-2 text-white">Inactive</span>
                    </label>
                  </div>
                </div>
              </form>
            </div>
            <div class="px-6 py-4 bg-dark-lighter border-t border-gray-700 flex justify-end space-x-3">
              <button 
                @click="modalOpen = false" 
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors"
              >
                Cancel
              </button>
              <button 
                @click="handleSave" 
                class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg shadow-md hover:shadow-glow-primary transition-all"
              >
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>


<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";

// Mock data for categories
// In a real application, this would come from the backend
const categories = ref([
  {
    id: 1,
    name: "Mobile Legends",
    slug: "mobile-legends",
    icon: "🎮",
    product_count: 15,
    status: "active",
    created_at: "2023-08-15"
  },
  {
    id: 2,
    name: "Free Fire",
    slug: "free-fire",
    icon: "🔫",
    product_count: 12,
    status: "active",
    created_at: "2023-08-16"
  },
  {
    id: 3,
    name: "PUBG Mobile",
    slug: "pubg-mobile",
    icon: "🧩",
    product_count: 8,
    status: "active",
    created_at: "2023-08-17"
  },
  {
    id: 4,
    name: "Genshin Impact",
    slug: "genshin-impact",
    icon: "⚔️",
    product_count: 10,
    status: "active",
    created_at: "2023-08-18"
  },
  {
    id: 5,
    name: "Valorant",
    slug: "valorant",
    icon: "🏆",
    product_count: 6,
    status: "active",
    created_at: "2023-08-19"
  }
]);

// Column definitions for the table
const columns = [
  { key: 'id', label: 'ID' },
  { key: 'icon', label: 'Icon' },
  { key: 'name', label: 'Name' },
  { key: 'slug', label: 'Slug' },
  { key: 'product_count', label: 'Products' },
  { 
    key: 'status', 
    label: 'Status',
    format: (value) => {
      const statusClasses = 
        value === 'active' 
          ? 'bg-green-500/20 text-green-400' 
          : 'bg-red-500/20 text-red-400';
          
      return `<span class="${statusClasses} px-2 py-1 rounded-full text-xs">${value}</span>`;
    }
  },
  { key: 'created_at', label: 'Created At' }
];

// Handle actions
const handleView = (item) => {
  console.log('View', item);
  // In a real app, you would redirect to the view page or show a modal
};

const handleEdit = (item) => {
  console.log('Edit', item);
  // In a real app, you would redirect to the edit page or show a modal
};

const handleDelete = (item) => {
  console.log('Delete', item);
  // In a real app, you would show a confirmation dialog before deleting
};

// Form modal states
const showForm = ref(false);
const formMode = ref('add'); // 'add' or 'edit'
const currentCategory = ref(null);

const openAddForm = () => {
  formMode.value = 'add';
  currentCategory.value = {
    name: '',
    slug: '',
    icon: '',
    status: 'active'
  };
  showForm.value = true;
};

const openEditForm = (category) => {
  formMode.value = 'edit';
  currentCategory.value = { ...category };
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
};

const saveCategory = () => {
  // In a real app, you would send a request to the backend
  console.log('Save category', currentCategory.value);
  
  if (formMode.value === 'add') {
    // Simulate adding a new category
    const newCategory = {
      ...currentCategory.value,
      id: categories.value.length + 1,
      product_count: 0,
      created_at: new Date().toISOString().split('T')[0]
    };
    categories.value.push(newCategory);
  } else {
    // Simulate updating an existing category
    const index = categories.value.findIndex(c => c.id === currentCategory.value.id);
    if (index !== -1) {
      categories.value[index] = { ...currentCategory.value };
    }
  }
  
  closeForm();
};
</script>

<template>
  <Head title="Categories" />

  <AdminLayout title="Categories">
    <DataTable 
      :data="categories" 
      :columns="columns"
      @view="handleView"
      @edit="openEditForm"
      @delete="handleDelete"
    >
      <template #title>
        Game Categories
      </template>
      
      <template #actions>
        <button 
          @click="openAddForm"
          class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg shadow-lg hover:shadow-glow-primary transition-all duration-200 flex items-center space-x-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          <span>Add Category</span>
        </button>
      </template>
      
      <template #cell(icon)="{ item }">
        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary/20 text-white">
          {{ item.icon }}
        </div>
      </template>
    </DataTable>
    
    <!-- Add/Edit Category Modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center">
      <div class="relative bg-dark-card border border-gray-700 rounded-lg shadow-lg max-w-md w-full p-6 m-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-white">
            {{ formMode === 'add' ? 'Add New Category' : 'Edit Category' }}
          </h3>
          <button @click="closeForm" class="text-gray-400 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="saveCategory">
          <div class="space-y-4">
            <!-- Name Field -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Name</label>
              <input
                id="name"
                v-model="currentCategory.name"
                type="text"
                class="w-full px-3 py-2 bg-dark-sidebar border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="Category Name"
                required
              />
            </div>
            
            <!-- Slug Field -->
            <div>
              <label for="slug" class="block text-sm font-medium text-gray-300 mb-1">Slug</label>
              <input
                id="slug"
                v-model="currentCategory.slug"
                type="text"
                class="w-full px-3 py-2 bg-dark-sidebar border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="category-slug"
                required
              />
            </div>
            
            <!-- Icon Field -->
            <div>
              <label for="icon" class="block text-sm font-medium text-gray-300 mb-1">Icon (Emoji)</label>
              <input
                id="icon"
                v-model="currentCategory.icon"
                type="text"
                class="w-full px-3 py-2 bg-dark-sidebar border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="🎮"
              />
            </div>
            
            <!-- Status Field -->
            <div>
              <label for="status" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
              <select
                id="status"
                v-model="currentCategory.status"
                class="w-full px-3 py-2 bg-dark-sidebar border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeForm"
                class="px-4 py-2 bg-dark-lighter text-gray-300 hover:text-white rounded-lg"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg shadow-lg hover:shadow-glow-primary transition-all duration-200"
              >
                {{ formMode === 'add' ? 'Create Category' : 'Update Category' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

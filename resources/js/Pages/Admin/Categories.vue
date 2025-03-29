
<script setup>
import { ref, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from '@inertiajs/vue3';
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import Modal from "@/Components/Modal.vue";

// Dummy data for categories
const categories = ref([
  { id: 1, kategori_name: 'Mobile Games', products_count: 15, created_at: '2023-05-10 08:30:45' },
  { id: 2, kategori_name: 'PC Games', products_count: 8, created_at: '2023-05-12 14:22:18' },
  { id: 3, kategori_name: 'Console Games', products_count: 6, created_at: '2023-05-15 09:45:33' },
  { id: 4, kategori_name: 'E-Wallet', products_count: 12, created_at: '2023-05-18 16:17:52' },
  { id: 5, kategori_name: 'Vouchers', products_count: 9, created_at: '2023-05-20 11:05:27' },
  { id: 6, kategori_name: 'Data Plans', products_count: 4, created_at: '2023-05-22 13:40:19' },
  { id: 7, kategori_name: 'Streaming Services', products_count: 7, created_at: '2023-05-25 10:12:38' },
]);

// Modal states
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const currentCategory = ref(null);

// Table columns configuration
const columns = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'kategori_name', label: 'Category Name', sortable: true },
  { key: 'products_count', label: 'Products', sortable: true },
  { key: 'created_at', label: 'Created At', sortable: true },
];

// Forms
const createForm = useForm({
  kategori_name: '',
});

const editForm = useForm({
  id: '',
  kategori_name: '',
});

// CRUD handlers
const handleCreate = () => {
  createForm.post('/admin/categories', {
    preserveScroll: true,
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
      // In a real app, we would refetch or update the categories list
    },
  });
};

const handleEdit = (category) => {
  currentCategory.value = category;
  editForm.id = category.id;
  editForm.kategori_name = category.kategori_name;
  showEditModal.value = true;
};

const handleUpdate = () => {
  editForm.put(`/admin/categories/${editForm.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      // In a real app, we would refetch or update the categories list
    },
  });
};

const handleDelete = (category) => {
  currentCategory.value = category;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (!currentCategory.value) return;
  
  // In a real app, we would call the delete API
  categories.value = categories.value.filter(
    (item) => item.id !== currentCategory.value.id
  );
  
  showDeleteModal.value = false;
  currentCategory.value = null;
};

// Reset forms when modals are closed
watch(showCreateModal, (isOpen) => {
  if (!isOpen) createForm.reset();
});

watch(showEditModal, (isOpen) => {
  if (!isOpen) editForm.reset();
});
</script>

<template>
  <Head title="Categories - Admin" />

  <AdminLayout title="Categories">
    <div class="mb-6 flex justify-between items-center">
      <p class="text-gray-400">
        Manage game categories. Each category can contain multiple products.
      </p>
      
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-gradient-primary text-white rounded-lg shadow-md hover:shadow-glow transition-all duration-300 flex items-center"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M12 5v14M5 12h14" />
        </svg>
        Add Category
      </button>
    </div>

    <!-- Categories Table -->
    <DataTable
      :data="categories"
      :columns="columns"
      @edit="handleEdit"
      @delete="handleDelete"
    />

    <!-- Floating Action Button (Mobile) -->
    <button
      @click="showCreateModal = true"
      class="fixed bottom-6 right-6 p-4 rounded-full bg-gradient-primary text-white shadow-glow sm:hidden z-20"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M12 5v14M5 12h14" />
      </svg>
    </button>

    <!-- Create Category Modal -->
    <Modal :show="showCreateModal" @close="showCreateModal = false" maxWidth="md">
      <div class="p-6 bg-dark-800 border-b border-gray-700">
        <h2 class="text-xl font-semibold text-white mb-1">Add New Category</h2>
        <p class="text-gray-400 text-sm mb-4">Create a new game category for your products</p>
        
        <form @submit.prevent="handleCreate">
          <div class="mb-4">
            <label for="kategori_name" class="block text-sm font-medium text-gray-300 mb-2">
              Category Name
            </label>
            <input
              id="kategori_name"
              v-model="createForm.kategori_name"
              type="text"
              class="w-full px-4 py-2 bg-dark-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              placeholder="Enter category name"
              required
            />
            <p v-if="createForm.errors.kategori_name" class="mt-1 text-sm text-red-400">
              {{ createForm.errors.kategori_name }}
            </p>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button
              type="button"
              @click="showCreateModal = false"
              class="px-4 py-2 bg-dark-700 text-gray-300 rounded-lg hover:bg-dark-600 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-gradient-primary text-white rounded-lg shadow-md hover:shadow-glow transition-all duration-300"
              :disabled="createForm.processing"
            >
              <svg
                v-if="createForm.processing"
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Save Category
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Edit Category Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false" maxWidth="md">
      <div class="p-6 bg-dark-800 border-b border-gray-700">
        <h2 class="text-xl font-semibold text-white mb-1">Edit Category</h2>
        <p class="text-gray-400 text-sm mb-4">Update the category information</p>
        
        <form @submit.prevent="handleUpdate">
          <div class="mb-4">
            <label for="edit_kategori_name" class="block text-sm font-medium text-gray-300 mb-2">
              Category Name
            </label>
            <input
              id="edit_kategori_name"
              v-model="editForm.kategori_name"
              type="text"
              class="w-full px-4 py-2 bg-dark-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              placeholder="Enter category name"
              required
            />
            <p v-if="editForm.errors.kategori_name" class="mt-1 text-sm text-red-400">
              {{ editForm.errors.kategori_name }}
            </p>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button
              type="button"
              @click="showEditModal = false"
              class="px-4 py-2 bg-dark-700 text-gray-300 rounded-lg hover:bg-dark-600 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-gradient-primary text-white rounded-lg shadow-md hover:shadow-glow transition-all duration-300"
              :disabled="editForm.processing"
            >
              <svg
                v-if="editForm.processing"
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Update Category
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false" maxWidth="sm">
      <div class="p-6 bg-dark-800">
        <div class="flex items-center justify-center mb-4">
          <div class="p-3 rounded-full bg-red-900/30 border border-red-800">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8 text-red-400"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M3 6h18" />
              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
              <line x1="10" y1="11" x2="10" y2="17" />
              <line x1="14" y1="11" x2="14" y2="17" />
            </svg>
          </div>
        </div>
        
        <h3 class="text-lg font-medium text-white text-center mb-2">Delete Category</h3>
        <p class="text-gray-400 text-center mb-6">
          Are you sure you want to delete "{{ currentCategory?.kategori_name }}"?
          This action cannot be undone.
        </p>
        
        <div class="flex justify-center space-x-4">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 bg-dark-700 text-gray-300 rounded-lg hover:bg-dark-600 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="confirmDelete"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 transition-colors shadow-md"
          >
            Delete
          </button>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

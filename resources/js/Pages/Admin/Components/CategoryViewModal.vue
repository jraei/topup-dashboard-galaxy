
<script setup>
import Modal from "@/Components/Modal.vue";
import { ref, defineProps, defineEmits } from "vue";

const props = defineProps({
    show: Boolean,
    category: Object,
    isLoading: Boolean,
});

const emit = defineEmits(['close', 'edit']);

const closeModal = () => {
    emit('close');
};

const editCategory = () => {
    emit('edit', props.category);
};
</script>

<template>
    <Modal 
        :show="show" 
        @close="closeModal" 
        max-width="md"
        class="overflow-y-auto"
    >
        <div class="p-4 bg-dark-card rounded-lg border border-gray-700 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-white">Category Details</h3>
                <button @click="closeModal" class="text-gray-400 hover:text-white">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <div v-if="isLoading" class="flex justify-center py-8">
                <div class="animate-spin w-10 h-10 border-4 border-primary border-t-transparent rounded-full"></div>
            </div>

            <div v-else-if="category" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="bg-dark-lighter p-3 rounded-lg">
                        <p class="text-gray-400 text-sm">Category ID</p>
                        <p class="text-white font-medium">{{ category.id }}</p>
                    </div>
                    <div class="bg-dark-lighter p-3 rounded-lg">
                        <p class="text-gray-400 text-sm">Name</p>
                        <p class="text-white font-medium">{{ category.kategori_name }}</p>
                    </div>
                    <div class="bg-dark-lighter p-3 rounded-lg">
                        <p class="text-gray-400 text-sm">Status</p>
                        <p>
                            <span 
                                :class="category.status === 'active' ? 
                                    'bg-green-500/20 text-green-400' : 
                                    'bg-red-500/20 text-red-400'" 
                                class="px-2 py-1 rounded-full text-xs"
                            >
                                {{ category.status }}
                            </span>
                        </p>
                    </div>
                    <div class="bg-dark-lighter p-3 rounded-lg">
                        <p class="text-gray-400 text-sm">Product Count</p>
                        <p class="text-white font-medium">{{ category.product_count || 0 }}</p>
                    </div>
                </div>
                
                <div class="bg-dark-lighter p-3 rounded-lg">
                    <p class="text-gray-400 text-sm">Created At</p>
                    <p class="text-white">{{ new Date(category.created_at).toLocaleString() }}</p>
                </div>
                
                <div class="bg-dark-lighter p-3 rounded-lg">
                    <p class="text-gray-400 text-sm">Last Updated</p>
                    <p class="text-white">{{ new Date(category.updated_at).toLocaleString() }}</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button
                        @click="editCategory"
                        class="px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                    >
                        Edit Category
                    </button>
                    <button
                        @click="closeModal"
                        class="px-4 py-2 text-gray-300 rounded-lg bg-dark-lighter hover:text-white"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>

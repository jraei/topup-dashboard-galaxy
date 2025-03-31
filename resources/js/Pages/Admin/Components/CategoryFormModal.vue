
<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    show: Boolean,
    formMode: String,
    category: Object,
});

const emit = defineEmits(['close', 'save']);

const closeForm = () => {
    emit('close');
};

const saveCategory = () => {
    emit('save');
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        @click.self="closeForm"
    >
        <div
            class="relative w-full max-w-md p-4 mx-4 my-6 border border-gray-700 rounded-lg shadow-lg bg-dark-card sm:p-6"
            @click.stop
        >
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-white">
                    {{ formMode === "add" ? "Add New Category" : "Edit Category" }}
                </h3>
                <button
                    @click="closeForm"
                    class="text-gray-400 hover:text-white"
                >
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

            <form @submit.prevent="saveCategory">
                <div class="space-y-4">
                    <div>
                        <label
                            for="kategori_name"
                            class="block mb-1 text-sm font-medium text-gray-300"
                        >Kategori</label>
                        <input
                            id="kategori_name"
                            v-model="category.kategori_name"
                            type="text"
                            class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Category Name"
                            name="kategori_name"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="status"
                            class="block mb-1 text-sm font-medium text-gray-300"
                        >Status</label>
                        <select
                            id="status"
                            name="status"
                            v-model="category.status"
                            class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-end pt-4 space-x-3">
                        <button
                            type="button"
                            @click="closeForm"
                            class="px-4 py-2 text-gray-300 rounded-lg bg-dark-lighter hover:text-white"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                        >
                            {{ formMode === "add" ? "Create Category" : "Update Category" }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

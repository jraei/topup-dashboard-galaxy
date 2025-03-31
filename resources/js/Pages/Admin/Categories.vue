<script setup>
import { ref, defineProps, computed, getCurrentInstance } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";

const props = defineProps({
    categories: Array,
    errors: Object,
});

const { proxy } = getCurrentInstance();

// flash message
// nyalakan kalau mau pake alert biasa
// const page = usePage();
// const successMessage = computed(() => page.props.flash?.success || null);

// Mock data for categories
// In a real application, this would come from the backend
const categories = computed(() => props.categories);

// Column definitions for the table
const columns = [
    { key: "id", label: "ID" },
    { key: "kategori_name", label: "Kategori" },
    {
        key: "status",
        label: "Status",
        format: (value) => {
            const statusClasses =
                value === "active"
                    ? "bg-green-500/20 text-green-400"
                    : "bg-red-500/20 text-red-400";

            return `<span class="${statusClasses} px-2 py-1 rounded-full text-xs">${value}</span>`;
        },
    },
];

// Handle actions
const handleView = (item) => {
    console.log("View", item);
    // In a real app, you would redirect to the view page or show a modal
};

const handleEdit = (item) => {
    console.log("Edit", item);
    // In a real app, you would redirect to the edit page or show a modal
};

const handleDelete = (item) => {
    proxy.$showSwalConfirm({
        onConfirm: () => {
            // Logika penghapusan data
            router.delete(route("categories.destroy", item.id), {
                preserveScroll: true,
            });
        },
    });

    // In a real app, you would show a confirmation dialog before deleting
};

// Form modal states
const showForm = ref(false);
const formMode = ref("add"); // 'add' or 'edit'
const currentCategory = ref(null);

const openAddForm = () => {
    formMode.value = "add";
    currentCategory.value = {
        kategori_name: "",
        status: "active",
    };
    showForm.value = true;
};

const openEditForm = (category) => {
    formMode.value = "edit";
    currentCategory.value = { ...category };
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
};

const saveCategory = () => {
    // In a real app, you would send a request to the backend
    console.log("Save category", currentCategory.value);

    if (formMode.value === "add") {
        // Simulate adding a new category
        router.post(route("categories.store"), currentCategory.value, {
            // preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                // successMessage.value = page.props.flash.success;
                currentCategory.value.category_name = "";
            },
        });
    } else {
        // Simulate updating an existing category
        router.put(
            route("categories.update", currentCategory.value.id),
            currentCategory.value,
            {
                preserveScroll: true,
            }
        );
    }

    closeForm();
};
</script>

<template>
    <Head title="Categories" />

    <AdminLayout title="Categories">
        <!-- nyalakan kalau mau pake alert biasa -->
        <!-- <div
            v-if="successMessage"
            class="px-8 py-3 mb-4 text-sm text-white bg-green-500/80"
        >
            {{ successMessage }}
        </div> -->
        <div
            v-if="Object.keys(errors).length > 0"
            class="px-8 py-3 mb-4 text-sm text-white bg-red-500/80"
        >
            <ul v-for="error in errors">
                <li>{{ error }}</li>
            </ul>
        </div>
        <DataTable
            :data="categories"
            :columns="columns"
            @view="handleView"
            @edit="openEditForm"
            @delete="handleDelete"
        >
            <template #title> Game Categories </template>

            <template #addButton>
                <button
                    @click="openAddForm"
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
                    <span>Add Category</span>
                </button>
            </template>

            <template #cell(icon)="{ item }">
                <div
                    class="flex items-center justify-center w-8 h-8 text-white rounded-full bg-primary/20"
                >
                    {{ item.icon }}
                </div>
            </template>
        </DataTable>

        <!-- Add/Edit Category Modal -->
        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
        >
            <div
                class="relative w-full max-w-md p-6 m-4 border border-gray-700 rounded-lg shadow-lg bg-dark-card"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">
                        {{
                            formMode === "add"
                                ? "Add New Category"
                                : "Edit Category"
                        }}
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
                        <!-- Name Field -->
                        <div>
                            <label
                                for="kategori_name"
                                class="block mb-1 text-sm font-medium text-gray-300"
                                >Kategori</label
                            >
                            <input
                                id="kategori_name"
                                v-model="currentCategory.kategori_name"
                                type="text"
                                class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Category Name"
                                name="kategori_name"
                                required
                            />
                        </div>

                        <!-- Status Field -->
                        <div>
                            <label
                                for="status"
                                class="block mb-1 text-sm font-medium text-gray-300"
                                >Status</label
                            >
                            <select
                                id="status"
                                name="status"
                                v-model="currentCategory.status"
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
                                {{
                                    formMode === "add"
                                        ? "Create Category"
                                        : "Update Category"
                                }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

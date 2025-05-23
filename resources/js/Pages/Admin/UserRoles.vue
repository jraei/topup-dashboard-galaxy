
<script setup>
import { ref, computed, getCurrentInstance } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    userRoles: Object,
    errors: Object,
    filters: Object,
});

const { proxy } = getCurrentInstance();

const userRoles = computed(() => props.userRoles.data || []);

const columns = [
    { key: "id", label: "ID" },
    { key: "role_name", label: "Role Name" },
    { key: "display_name", label: "Display Name" },
    {
        key: "is_default",
        label: "Default Role",
        format: (value, item) => {
            const isSystem = item.is_system ?? false;
            const disabled = isSystem ? 'opacity-50 cursor-not-allowed' : '';
            
            const toggleClasses = value
                ? "bg-primary relative inline-flex h-6 w-11 items-center rounded-full"
                : "bg-secondary/30 relative inline-flex h-6 w-11 items-center rounded-full";

            const buttonClasses = value
                ? "translate-x-6 inline-block h-4 w-4 transform rounded-full bg-white transition"
                : "translate-x-1 inline-block h-4 w-4 transform rounded-full bg-white transition";

            return `
                <div class="flex items-center space-x-3">
                    <button 
                        type="button" 
                        data-id="${item.id}" 
                        data-action="toggle-default" 
                        class="${toggleClasses} ${disabled}"
                        ${isSystem ? 'disabled' : ''}
                    >
                        <span class="${buttonClasses}"></span>
                    </button>
                    ${value ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/20 text-primary">Default</span>' : ''}
                </div>
            `;
        },
    },
];

// Handle deletion of a user role
const handleDelete = (item) => {
    proxy.$showSwalConfirm({
        title: 'Delete User Role',
        text: `Are you sure you want to delete "${item.display_name}"? This action cannot be undone.`,
        onConfirm: () => {
            router.delete(route("user-roles.destroy", item.id), {
                preserveScroll: true,
            });
        },
    });
};

// Handle toggle of default status
const handleToggleDefault = (item) => {
    if (item.is_system) {
        proxy.$swal.fire({
            title: 'Cannot Modify System Roles',
            text: 'System roles cannot be modified.',
            icon: 'warning',
        });
        return;
    }
    
    proxy.$showSwalConfirm({
        title: 'Change Default Role',
        text: `Set "${item.display_name}" as the default role? This will remove default status from the current default role.`,
        onConfirm: () => {
            router.patch(route("user-roles.toggle-default", item.id), {}, {
                preserveScroll: true,
            });
        },
    });
};

// Form handling
const showForm = ref(false);
const formMode = ref("add");
const currentData = ref({
    role_name: "",
    display_name: "",
    description: "",
    is_default: false
});

const openAddForm = () => {
    formMode.value = "add";
    currentData.value = {
        role_name: "",
        display_name: "",
        description: "",
        is_default: false
    };
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
};

const saveDataForm = () => {
    router.post(route("user-roles.store"), currentData.value, {
        preserveScroll: true,
        onSuccess: () => {
            closeForm();
        },
    });
};

// Event delegation for table actions
const handleTableAction = (event) => {
    const button = event.target.closest('[data-action]');
    if (!button) return;
    
    const action = button.dataset.action;
    const id = parseInt(button.dataset.id);
    
    if (action === 'toggle-default') {
        const role = userRoles.value.find(role => role.id === id);
        if (role && !role.is_default) {
            handleToggleDefault(role);
        }
    }
};
</script>

<template>
    <Head title="User Roles" />

    <AdminLayout title="User Roles Management">
        <div
            v-if="Object.keys(errors).length > 0"
            class="px-4 py-3 mb-4 text-sm text-white rounded-lg bg-red-500/80"
        >
            <ul>
                <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
            </ul>
        </div>
        <div
            class="w-full border rounded-lg shadow-lg border-primary/40 bg-dark-card"
            @click="handleTableAction"
        >
            <!-- Table container with explicit scrolling -->
            <div class="max-w-full overflow-x-auto">
                <DataTable
                    :data="userRoles"
                    :columns="columns"
                    :filters="filters"
                    route="user-roles.index"
                    class="w-full whitespace-nowrap"
                >
                    <template #title>User Role Management</template>

                    <template #addButton>
                        <button
                            @click="openAddForm"
                            class="flex items-center px-3 py-2 space-x-2 text-white transition-all duration-200 rounded-lg shadow-lg sm:px-4 bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 sm:w-5 sm:h-5"
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
                            <span>Add Role</span>
                        </button>
                    </template>

                    <template #actions="{ item }">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    class="inline-flex items-center justify-center p-2 text-gray-400 transition-colors rounded-full hover:text-white hover:bg-dark-lighter"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                        />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <div
                                    class="py-1 border border-gray-700 rounded-lg shadow-lg bg-dark-card"
                                >
                                    <button
                                        v-if="!item.is_default && !item.is_system"
                                        @click="handleToggleDefault(item)"
                                        class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-primary"
                                    >
                                        Make Default
                                    </button>
                                    <button
                                        v-if="!item.is_system"
                                        @click="handleDelete(item)"
                                        class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-red-400"
                                    >
                                        Delete
                                    </button>
                                    <div 
                                        v-if="item.is_system" 
                                        class="block w-full px-4 py-2 text-sm text-gray-500 italic"
                                    >
                                        System Role (Protected)
                                    </div>
                                </div>
                            </template>
                        </Dropdown>
                    </template>

                    <!-- Empty state template -->
                    <template #empty>
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="p-3 mb-4 rounded-full bg-primary/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="mb-1 text-lg font-medium text-white">No User Roles Found</h3>
                            <p class="max-w-sm mb-4 text-center text-gray-400">
                                User roles help you manage permissions and access levels in your application.
                            </p>
                            <button
                                @click="openAddForm"
                                class="flex items-center px-4 py-2 space-x-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                            >
                                <span>Create First Role</span>
                            </button>
                        </div>
                    </template>
                </DataTable>
            </div>
            <Pagination :links="props.userRoles.links" />
        </div>

        <!-- Role Creation Modal -->
        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black/50"
            @click.self="closeForm"
        >
            <div
                class="relative w-full max-w-md mx-4 p-3 border border-gray-700 rounded-lg shadow-lg sm:p-4 md:p-6 md:max-w-xl bg-dark-card max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white sm:text-xl">
                        Create New User Role
                    </h3>
                    <button
                        @click="closeForm"
                        class="text-gray-400 hover:text-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 sm:w-6 sm:h-6"
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

                <form @submit.prevent="saveDataForm" class="overflow-visible">
                    <div class="space-y-3 sm:space-y-4">
                        <div>
                            <label
                                for="role_name"
                                class="block mb-1 text-sm font-medium text-gray-300"
                            >Role Name</label>
                            <input
                                id="role_name"
                                v-model="currentData.role_name"
                                type="text"
                                class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="admin"
                                required
                            />
                            <p class="mt-1 text-xs text-gray-400">
                                Unique identifier used in the system (no spaces)
                            </p>
                        </div>

                        <div>
                            <label
                                for="display_name"
                                class="block mb-1 text-sm font-medium text-gray-300"
                            >Display Name</label>
                            <input
                                id="display_name"
                                v-model="currentData.display_name"
                                type="text"
                                class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Administrator"
                                required
                            />
                            <p class="mt-1 text-xs text-gray-400">
                                Human-readable name shown in the interface
                            </p>
                        </div>

                        <div>
                            <label
                                for="description"
                                class="block mb-1 text-sm font-medium text-gray-300"
                            >Description (Optional)</label>
                            <textarea
                                id="description"
                                v-model="currentData.description"
                                class="w-full h-24 px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Role description and capabilities"
                            ></textarea>
                        </div>

                        <div class="flex items-center">
                            <input
                                id="is_default"
                                v-model="currentData.is_default"
                                type="checkbox"
                                class="rounded shadow-sm border-primary/20 text-primary focus:border-primary"
                            />
                            <label
                                for="is_default"
                                class="ml-2 text-sm font-medium text-gray-300"
                            >Make Default Role</label>
                            <div class="ml-2" v-if="currentData.is_default">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary/20 text-primary">
                                    Will replace current default
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex flex-col justify-end pt-3 space-y-2 sm:flex-row sm:pt-4 sm:space-y-0 sm:space-x-3"
                        >
                            <button
                                type="button"
                                @click="closeForm"
                                class="w-full px-4 py-2 text-gray-300 rounded-lg bg-dark-lighter hover:text-white sm:w-auto"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="w-full px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary sm:w-auto"
                            >
                                Create Role
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

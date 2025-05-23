
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
        label: "Default",
        format: (value) => {
            const statusClasses = value
                ? "bg-primary/20 text-primary"
                : "bg-gray-500/20 text-gray-400";

            return `<span class="${statusClasses} px-2 py-1 rounded-full text-xs">${
                value ? "Yes" : "No"
            }</span>`;
        },
    },
];

const handleDelete = (item) => {
    proxy.$showSwalConfirm({
        onConfirm: () => {
            router.delete(route("user-roles.destroy", item.id), {
                preserveScroll: true,
            });
        },
    });
};

const handleToggleDefault = (item) => {
    const action = item.is_default ? "remove default status from" : "set as default";
    
    proxy.$showSwalConfirm({
        title: `Toggle Default Role`,
        text: `Are you sure you want to ${action} this role?`,
        onConfirm: () => {
            router.patch(route("user-roles.toggle-default", item.id), {}, {
                preserveScroll: true,
            });
        },
    });
};

const showForm = ref(false);
const formMode = ref("add");
const currentData = ref({});

const openAddForm = () => {
    formMode.value = "add";
    currentData.value = {
        role_name: "",
        display_name: "",
    };
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
};

const saveDataForm = () => {
    if (formMode.value === "add") {
        router.post(route("user-roles.store"), currentData.value, {
            preserveScroll: true,
            onSuccess: () => {
                currentData.value.role_name = "";
                currentData.value.display_name = "";
                closeForm();
            },
        });
    }
};
</script>

<template>
    <Head title="User Roles" />

    <AdminLayout title="User Roles">
        <div
            v-if="Object.keys(errors).length > 0"
            class="px-4 py-3 mb-4 text-sm text-white rounded-lg bg-red-500/80"
        >
            <ul v-for="error in errors">
                <li>{{ error }}</li>
            </ul>
        </div>

        <div
            class="w-full border rounded-lg shadow-lg border-primary/40 bg-dark-card"
        >
            <div class="max-w-full overflow-x-auto">
                <DataTable
                    :data="userRoles"
                    :columns="columns"
                    :filters="filters"
                    route="user-roles.index"
                    class="w-full whitespace-nowrap"
                >
                    <template #title> User Roles Management </template>

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
                                        @click="handleToggleDefault(item)"
                                        class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-primary"
                                    >
                                        {{ item.is_default ? "Remove Default" : "Set as Default" }}
                                    </button>
                                    <button
                                        @click="handleDelete(item)"
                                        class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-red-400"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </template>
                        </Dropdown>
                    </template>
                </DataTable>
            </div>
            <Pagination :links="props.userRoles.links" />
        </div>

        <!-- Form Modal -->
        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black/50"
            @click.self="closeForm"
        >
            <div
                class="relative w-full max-w-md mx-4 p-3 border border-gray-700 rounded-lg shadow-lg sm:p-4 md:p-6 md:max-w-xl lg:max-w-2xl bg-dark-card max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white sm:text-xl">
                        Add New User Role
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
                            >
                                Role Name
                            </label>
                            <input
                                id="role_name"
                                v-model="currentData.role_name"
                                type="text"
                                class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="admin, moderator, etc."
                                name="role_name"
                                required
                            />
                        </div>

                        <div>
                            <label
                                for="display_name"
                                class="block mb-1 text-sm font-medium text-gray-300"
                            >
                                Display Name
                            </label>
                            <input
                                id="display_name"
                                v-model="currentData.display_name"
                                type="text"
                                class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Administrator, Moderator, etc."
                                name="display_name"
                                required
                            />
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

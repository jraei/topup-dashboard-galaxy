<script setup>
import { ref, computed, watch, getCurrentInstance } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import Modal from "@/Components/Modal.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Pagination from "@/Components/Pagination.vue";
import PricePreview from "@/Components/PricePreview.vue";
import BulkAssignModal from "@/Components/Admin/ProfitPaketLayanan/BulkAssignModal.vue";
import axios from "axios";

const props = defineProps({
    profitPaketLayanans: Object,
    packages: Array,
    roles: Array,
    errors: Object,
    filters: Object,
});

// Data
const profitPaketLayanans = computed(
    () => props.profitPaketLayanans.data || []
);
const services = ref([]);

const { proxy } = getCurrentInstance();

// Column definitions for the table
const columns = [
    { key: "id", label: "ID" },
    {
        key: "package_name",
        label: "Service Package",
        format: (_, item) => {
            const product = item.paket_layanan?.produk;
            const packageName =
                item.paket_layanan?.judul_paket || "Unknown Package";
            const productName = product?.nama || "No Product";
            const thumbnail = product?.thumbnail
                ? `/storage/${product.thumbnail}`
                : "/img/placeholder.png";

            return `
                <div class="flex items-center space-x-2">
                    <img src="${thumbnail}" alt="${packageName}" class="object-cover w-8 h-8 rounded-sm">
                    <div class="flex flex-col">
                        <span class="font-medium">${packageName}</span>
                        <span class="text-xs text-gray-400">${productName}</span>
                    </div>
                </div>
            `;
        },
    },
    {
        key: "role_name",
        label: "User Role",
        format: (_, item) => {
            return `<span>${item.user_role.display_name}</span>`;
        },
    },
    {
        key: "type",
        label: "Type",
        format: (value) => {
            return `<span class="px-2 py-1 rounded-xl text-xs 
                    ${
                        value === "percent"
                            ? "bg-blue-500/20 text-blue-400"
                            : "bg-purple-500/20 text-purple-400"
                    }">
                    ${value === "percent" ? "Percentage" : "Multiplier"}
                </span>`;
        },
    },
    {
        key: "value",
        label: "Value",
        format: (value, item) => {
            return `<span>${value}${
                item.type === "percent" ? "%" : "x"
            }</span>`;
        },
    },
];

// View modal states
const showViewModal = ref(false);
const selectedProfitPaketLayanan = ref(null);
const isLoading = ref(false);

// Bulk assign modal state
const showBulkModal = ref(false);

// Handle view action
const handleView = async (item) => {
    isLoading.value = true;
    selectedProfitPaketLayanan.value = { ...item, loading: true };

    currentProfitPaketLayanan.value = {
        id: item.id,
        paket_layanan_id: item.paket_layanan_id,
        user_roles_id: item.user_roles_id,
        type: item.type,
        value: item.value,
    };

    showViewModal.value = true;

    try {
        const response = await axios.get(
            route("profit-paket-layanans.show", item.id)
        );
        selectedProfitPaketLayanan.value = response.data.profitPaketLayanan;

        // Load services for this package to preview pricing
        if (selectedProfitPaketLayanan.value.paket_layanan_id) {
            loadServicesForPackage(
                selectedProfitPaketLayanan.value.paket_layanan_id
            );
        }
    } catch (error) {
        console.error("Error fetching profit package details:", error);
        proxy.$showSwalConfirm({
            title: "Error",
            text: "Failed to load profit setting details",
            icon: "error",
        });
    } finally {
        isLoading.value = false;
    }
};

const handleEdit = (item) => {
    openEditForm(item);
};

const handleDelete = (item) => {
    proxy.$showSwalConfirm({
        onConfirm: () => {
            router.delete(route("profit-paket-layanans.destroy", item.id), {
                preserveScroll: true,
            });
        },
    });
};

// Form modal states
const showForm = ref(false);
const formMode = ref("add"); // 'add' or 'edit'
const currentProfitPaketLayanan = ref(null);

const openAddForm = () => {
    formMode.value = "add";
    currentProfitPaketLayanan.value = {
        paket_layanan_id: "",
        user_roles_id: "",
        type: "percent",
        value: 1.0,
    };
    showForm.value = true;
};

const openEditForm = (profitPaketLayanan) => {
    if (showViewModal.value) {
        showViewModal.value = false;
    }
    formMode.value = "edit";
    currentProfitPaketLayanan.value = {
        id: profitPaketLayanan.id,
        paket_layanan_id: profitPaketLayanan.paket_layanan_id,
        user_roles_id: profitPaketLayanan.user_roles_id,
        type: profitPaketLayanan.type,
        value: profitPaketLayanan.value,
    };
    loadServicesForPackage(profitPaketLayanan.paket_layanan_id);
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
};

const closeViewModal = () => {
    showViewModal.value = false;
    selectedProfitPaketLayanan.value = null;
    services.value = [];
};

const saveProfitPaketLayanan = () => {
    if (formMode.value === "add") {
        router.post(
            route("profit-paket-layanans.store"),
            currentProfitPaketLayanan.value,
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeForm();
                },
            }
        );
    } else {
        router.put(
            route(
                "profit-paket-layanans.update",
                currentProfitPaketLayanan.value.id
            ),
            currentProfitPaketLayanan.value,
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeForm();
                },
            }
        );
    }
};

// Load services for a package to preview pricing
const loadServicesForPackage = async (packageId) => {
    if (!packageId) {
        services.value = [];
        return;
    }

    try {
        const response = await axios.post(
            route("profit-paket-layanans.preview"),
            {
                paket_layanan_id: packageId,
            },
            {
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            }
        );

        if (response.data.success) {
            services.value = response.data;
        } else {
            console.error("Error loading price preview:", response.data);
        }
    } catch (error) {
        console.error("Error loading services:", error);
        services.value = [];
    }
};

// Calculate price based on profit setting
const calculatePrice = (basePrice) => {
    if (!currentProfitPaketLayanan.value) return basePrice;

    const value = parseFloat(currentProfitPaketLayanan.value.value);

    if (currentProfitPaketLayanan.value.type === "percent") {
        return basePrice + (basePrice * value) / 100;
    } else {
        // multiplier
        return basePrice * value;
    }
};

// Bulk assign functions
const openBulkModal = () => {
    showBulkModal.value = true;
};

const closeBulkModal = () => {
    showBulkModal.value = false;
};

// Watch for package changes to load services
watch(
    () => currentProfitPaketLayanan.value?.paket_layanan_id,
    (newValue) => {
        if (newValue) {
            loadServicesForPackage(newValue);
        }
    }
);
</script>

<template>
    <Head title="Profit Package Settings" />

    <AdminLayout title="Profit Package Settings">
        <div
            v-if="Object.keys(errors || {}).length > 0"
            class="px-4 py-3 mb-4 text-sm text-white rounded-lg bg-red-500/80"
        >
            <ul v-for="error in errors">
                <li>{{ error }}</li>
            </ul>
        </div>

        <div
            class="w-full border rounded-lg shadow-lg border-primary/40 bg-dark-card"
        >
            <DataTable
                :data="profitPaketLayanans"
                :columns="columns"
                :filters="filters"
                route="profit-paket-layanans.index"
                class="max-w-full whitespace-nowrap"
            >
                <template #title> Profit Package Settings </template>

                <template #addButton>
                    <div class="flex space-x-2">
                        <button
                            @click="openBulkModal"
                            class="flex items-center px-4 py-2 space-x-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-secondary hover:bg-secondary-hover hover:shadow-glow-secondary"
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
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                            <span>Bulk Assign</span>
                        </button>
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
                            <span>Add Profit Setting</span>
                        </button>
                    </div>
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
                                class="py-1 border rounded-lg shadow-lg border-primary/60 bg-dark-card"
                            >
                                <button
                                    @click="handleView(item)"
                                    class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-secondary"
                                >
                                    View
                                </button>
                                <button
                                    @click="handleEdit(item)"
                                    class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-primary"
                                >
                                    Edit
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

            <!-- Pagination component -->
            <Pagination
                :links="props.profitPaketLayanans.links"
                :currentPage="props.profitPaketLayanans.current_page"
                :perPage="props.profitPaketLayanans.per_page"
                :totalEntries="props.profitPaketLayanans.total"
            />
        </div>

        <!-- Bulk Assign Modal -->
        <BulkAssignModal
            :show="showBulkModal"
            :packages="packages"
            :roles="roles"
            @close="closeBulkModal"
        />

        <!-- Add/Edit Form Modal -->
        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
            @click.self="closeForm"
        >
            <div
                class="relative w-full max-w-md mx-4 p-3 border border-gray-700 rounded-lg shadow-lg sm:p-4 md:p-6 md:max-w-xl lg:max-w-2xl bg-dark-card max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">
                        {{
                            formMode === "add"
                                ? "Add New Profit Setting"
                                : "Edit Profit Setting"
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

                <form
                    @submit.prevent="saveProfitPaketLayanan"
                    class="overflow-visible"
                >
                    <div class="space-y-3 sm:space-y-4">
                        <!-- Package Selection -->
                        <div>
                            <label
                                for="paket_layanan_id"
                                class="block mb-1 text-sm font-medium text-gray-300"
                            >
                                Service Package
                            </label>
                            <select
                                id="paket_layanan_id"
                                v-model="
                                    currentProfitPaketLayanan.paket_layanan_id
                                "
                                class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                required
                            >
                                <option value="">Select Package</option>
                                <option
                                    v-for="pkg in packages"
                                    :key="pkg.id"
                                    :value="pkg.id"
                                >
                                    {{ pkg.judul_paket }}
                                    <span v-if="pkg.produk"
                                        >({{ pkg.produk.nama }})</span
                                    >
                                </option>
                            </select>
                        </div>

                        <!-- Role Selection -->
                        <div>
                            <label
                                for="user_roles_id"
                                class="block mb-1 text-sm font-medium text-gray-300"
                            >
                                User Role
                            </label>
                            <select
                                id="user_roles_id"
                                v-model="
                                    currentProfitPaketLayanan.user_roles_id
                                "
                                class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                required
                            >
                                <option value="">Select Role</option>
                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.id"
                                >
                                    {{ role.display_name }}
                                </option>
                            </select>
                        </div>

                        <div
                            class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4"
                        >
                            <!-- Type Selection -->
                            <div>
                                <label
                                    for="type"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                >
                                    Type
                                </label>
                                <select
                                    id="type"
                                    v-model="currentProfitPaketLayanan.type"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required
                                >
                                    <option value="percent">Percentage</option>
                                    <option value="multiplier">
                                        Multiplier
                                    </option>
                                </select>
                            </div>

                            <!-- Value Input -->
                            <div>
                                <label
                                    for="value"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                >
                                    Value
                                    <span
                                        class="text-xs text-gray-400"
                                        v-if="
                                            currentProfitPaketLayanan.type ===
                                            'percent'
                                        "
                                        >(%)</span
                                    >
                                    <span class="text-xs text-gray-400" v-else
                                        >(x)</span
                                    >
                                </label>
                                <input
                                    id="value"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    v-model="currentProfitPaketLayanan.value"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Price Preview for current package and role -->
                        <div
                            v-if="
                                services &&
                                services.pricing &&
                                services.pricing.length > 0
                            "
                            class="mt-4"
                        >
                            <h4 class="mb-2 text-sm font-medium text-gray-300">
                                Price Preview (Current Settings)
                            </h4>
                            <div
                                class="p-3 border border-gray-700 rounded-lg bg-dark-sidebar"
                            >
                                <div
                                    v-for="service in services.pricing.slice(
                                        0,
                                        3
                                    )"
                                    :key="service.id"
                                    class="flex items-center justify-between py-1 text-sm"
                                >
                                    <span class="text-gray-300">{{
                                        service.name
                                    }}</span>
                                    <div class="flex space-x-2">
                                        <span class="text-gray-400"
                                            >Base: Rp
                                            {{
                                                parseInt(
                                                    service.base_price
                                                ).toLocaleString()
                                            }}</span
                                        >
                                        <span class="font-medium text-primary"
                                            >→ Rp
                                            {{
                                                parseInt(
                                                    calculatePrice(
                                                        service.base_price
                                                    )
                                                ).toLocaleString()
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div
                                    v-if="services.pricing.length > 3"
                                    class="mt-1 text-xs text-gray-400"
                                >
                                    ... and
                                    {{ services.pricing.length - 3 }} more
                                    services
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 space-x-2">
                        <button
                            type="button"
                            @click="closeForm"
                            class="px-4 py-2 text-gray-400 border border-gray-600 rounded-lg hover:bg-gray-700 hover:text-white"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-white rounded-lg bg-primary hover:bg-primary-hover"
                        >
                            {{
                                formMode === "add"
                                    ? "Add Profit Setting"
                                    : "Update Profit Setting"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Modal -->
        <div
            v-if="showViewModal"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
            @click.self="closeViewModal"
        >
            <div
                class="relative w-full max-w-4xl mx-4 p-6 border border-gray-700 rounded-lg shadow-lg bg-dark-card max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-white">
                        Profit Setting Details
                    </h3>
                    <button
                        @click="closeViewModal"
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

                <div v-if="isLoading" class="py-8 text-center">
                    <div
                        class="inline-block w-8 h-8 border-b-2 rounded-full animate-spin border-primary"
                    ></div>
                    <p class="mt-2 text-gray-400">Loading details...</p>
                </div>

                <div v-else-if="selectedProfitPaketLayanan" class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div>
                                <h4
                                    class="mb-1 text-sm font-medium text-gray-300"
                                >
                                    Service Package
                                </h4>
                                <p class="text-white">
                                    {{
                                        selectedProfitPaketLayanan.paket_layanan
                                            ?.judul_paket
                                    }}
                                </p>
                                <p class="text-sm text-gray-400">
                                    {{
                                        selectedProfitPaketLayanan.paket_layanan
                                            ?.produk?.nama
                                    }}
                                </p>
                            </div>
                            <div>
                                <h4
                                    class="mb-1 text-sm font-medium text-gray-300"
                                >
                                    User Role
                                </h4>
                                <p class="text-white">
                                    {{
                                        selectedProfitPaketLayanan.user_role
                                            ?.display_name
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <h4
                                    class="mb-1 text-sm font-medium text-gray-300"
                                >
                                    Profit Type
                                </h4>
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm font-medium',
                                        selectedProfitPaketLayanan.type ===
                                        'percent'
                                            ? 'bg-blue-500/20 text-blue-400'
                                            : 'bg-purple-500/20 text-purple-400',
                                    ]"
                                >
                                    {{
                                        selectedProfitPaketLayanan.type ===
                                        "percent"
                                            ? "Percentage"
                                            : "Multiplier"
                                    }}
                                </span>
                            </div>
                            <div>
                                <h4
                                    class="mb-1 text-sm font-medium text-gray-300"
                                >
                                    Value
                                </h4>
                                <p class="text-lg font-medium text-white">
                                    {{ selectedProfitPaketLayanan.value
                                    }}{{
                                        selectedProfitPaketLayanan.type ===
                                        "percent"
                                            ? "%"
                                            : "x"
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Service Price Preview -->
                    <div
                        v-if="
                            services &&
                            services.pricing &&
                            services.pricing.length > 0
                        "
                    >
                        <h4 class="mb-4 text-lg font-medium text-white">
                            Service Price Preview
                        </h4>
                        <div
                            class="overflow-hidden border border-gray-700 rounded-lg"
                        >
                            <div
                                class="px-4 py-2 border-b border-gray-700 bg-dark-lighter"
                            >
                                <div
                                    class="grid grid-cols-4 gap-4 text-sm font-medium text-gray-300"
                                >
                                    <span>Service</span>
                                    <span>Base Price</span>
                                    <span>Final Price</span>
                                    <span>Profit</span>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-700">
                                <div
                                    v-for="service in services.pricing"
                                    :key="service.id"
                                    class="px-4 py-3"
                                >
                                    <div class="grid grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="font-medium text-white">
                                                {{ service.name }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ service.code }}
                                            </p>
                                        </div>
                                        <div class="text-gray-300">
                                            Rp
                                            {{
                                                parseInt(
                                                    service.base_price
                                                ).toLocaleString()
                                            }}
                                        </div>
                                        <div class="font-medium text-primary">
                                            Rp
                                            {{
                                                parseInt(
                                                    calculatePrice(
                                                        service.base_price
                                                    )
                                                ).toLocaleString()
                                            }}
                                        </div>
                                        <div class="text-secondary">
                                            +{{
                                                parseInt(
                                                    calculatePrice(
                                                        service.base_price
                                                    ) - service.base_price
                                                ).toLocaleString()
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

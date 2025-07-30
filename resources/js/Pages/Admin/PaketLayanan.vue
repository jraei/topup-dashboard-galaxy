<script setup>
import { ref, computed, watch, getCurrentInstance } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import Modal from "@/Components/Modal.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Pagination from "@/Components/Pagination.vue";
import axios from "axios";

const props = defineProps({
    paketLayanans: Object,
    products: Array,
    services: Array,
    errors: Object,
    filters: Object,
});

const { proxy } = getCurrentInstance();

// Data
const paketLayanans = computed(() => props.paketLayanans.data || []);
const availableServices = ref([]);
const packageServices = ref([]);

// Fusion Service states
const showFusionForm = ref(false);
const fusionFormMode = ref("add"); // 'add' or 'edit'
const currentFusion = ref(null);
const fusionServices = ref([]);
const selectedFusionServices = ref([]);

// Column definitions for the table
const columns = [
    { key: "id", label: "ID" },
    {
        key: "judul_paket",
        label: "Package Title",
        format: (value) => {
            return `<span class="font-medium text-white">${value}</span>`;
        },
    },
    {
        key: "layanans_count",
        label: "Services Count",
        format: (value, item) => {
            const fusionCount = item.fusion_services_count || 0;
            return `
                <div class="space-y-1">
                    <span class="px-2 py-1 text-xs rounded-xl bg-primary/20 text-primary">${value} services</span>
                    ${fusionCount > 0 ? `<span class="px-2 py-1 text-xs rounded-xl bg-secondary/20 text-secondary">${fusionCount} fusions</span>` : ''}
                </div>
            `;
        },
    },
    {
        key: "display_order",
        label: "Order",
        format: (value) => {
            return `<span class="px-2 py-1 text-xs rounded bg-dark-lighter text-white">#${value}</span>`;
        },
    },
    {
        key: "created_at",
        label: "Created",
        format: (value) => {
            return new Date(value).toLocaleDateString();
        },
    },
];

// View modal states
const showViewModal = ref(false);
const selectedPaketLayanan = ref(null);
const isLoading = ref(false);

// Form modal states
const showForm = ref(false);
const formMode = ref("add"); // 'add' or 'edit'
const currentPaketLayanan = ref(null);

// Service selection states
const selectedServices = ref([]);
const serviceSearchQuery = ref("");
const selectAll = ref(false);

// Computed for filtered services
const filteredServices = computed(() => {
    if (!serviceSearchQuery.value) return availableServices.value;

    return availableServices.value.filter(
        (service) =>
            service.nama_layanan
                .toLowerCase()
                .includes(serviceSearchQuery.value.toLowerCase()) ||
            service.kode_layanan
                .toLowerCase()
                .includes(serviceSearchQuery.value.toLowerCase()) ||
            (service.produk &&
                service.produk.nama
                    .toLowerCase()
                    .includes(serviceSearchQuery.value.toLowerCase()))
    );
});

// Handle view action
const handleView = async (item) => {
    isLoading.value = true;
    selectedPaketLayanan.value = { ...item, loading: true };
    showViewModal.value = true;

    try {
        const response = await axios.get(route("paket-layanans.show", item.id));
        selectedPaketLayanan.value = response.data.paketLayanan;

        // Load services for this package
        loadPackageServices(item.id);
    } catch (error) {
        console.error("Error fetching package details:", error);
        proxy.$showSwalConfirm({
            title: "Error",
            text: "Failed to load package details",
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
            router.delete(route("paket-layanans.destroy", item.id), {
                preserveScroll: true,
            });
        },
    });
};

// Form functions
const openAddForm = () => {
    formMode.value = "add";
    currentPaketLayanan.value = {
        judul_paket: "",
        informasi: "",
        display_order: 0,
    };
    selectedServices.value = [];
    loadAvailableServices();
    showForm.value = true;
};

const openEditForm = async (paketLayanan) => {
    if (showViewModal.value) {
        showViewModal.value = false;
    }
    formMode.value = "edit";
    currentPaketLayanan.value = {
        id: paketLayanan.id,
        judul_paket: paketLayanan.judul_paket,
        informasi: paketLayanan.informasi,
        display_order: paketLayanan.display_order || 0,
    };

    // Load available services for editing (including currently assigned ones)
    await loadAvailableServices(paketLayanan.id);

    // Load currently assigned services
    const currentServices = await loadPackageServices(paketLayanan.id);
    selectedServices.value = currentServices.map((service) => service.id);

    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    selectedServices.value = [];
    serviceSearchQuery.value = "";
    selectAll.value = false;
};

const closeViewModal = () => {
    showViewModal.value = false;
    selectedPaketLayanan.value = null;
    packageServices.value = [];
};

const savePaketLayanan = () => {
    if (selectedServices.value.length === 0) {
        proxy.$showSwalConfirm({
            title: "Error",
            text: "Please select at least one service",
            icon: "error",
        });
        return;
    }

    const data = {
        ...currentPaketLayanan.value,
        service_ids: selectedServices.value,
    };

    if (formMode.value === "add") {
        router.post(route("paket-layanans.store"), data, {
            preserveScroll: true,
            onSuccess: () => {
                closeForm();
            },
        });
    } else {
        router.put(
            route("paket-layanans.update", currentPaketLayanan.value.id),
            data,
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeForm();
                },
            }
        );
    }
};

// Service management functions
const loadAvailableServices = async (excludePackageId = null) => {
    try {
        const response = await axios.get(
            route("paket-layanans.available-services"),
            {
                params: {
                    exclude_package_id: excludePackageId,
                },
            }
        );
        availableServices.value = response.data.services;
    } catch (error) {
        console.error("Error loading available services:", error);
        availableServices.value = [];
    }
};

const loadPackageServices = async (packageId) => {
    try {
        const response = await axios.get(
            route("paket-layanans.package-services", packageId)
        );
        packageServices.value = response.data.services;
        // Also load fusion services
        await loadFusionServices(packageId);
        return response.data.services;
    } catch (error) {
        console.error("Error loading package services:", error);
        packageServices.value = [];
        return [];
    }
};

const toggleService = (serviceId) => {
    const index = selectedServices.value.indexOf(serviceId);
    if (index > -1) {
        selectedServices.value.splice(index, 1);
    } else {
        selectedServices.value.push(serviceId);
    }
    updateSelectAllState();
};

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedServices.value = filteredServices.value.map(
            (service) => service.id
        );
    } else {
        selectedServices.value = [];
    }
};

const updateSelectAllState = () => {
    selectAll.value =
        filteredServices.value.length > 0 &&
        filteredServices.value.every((service) =>
            selectedServices.value.includes(service.id)
        );
};

// Fusion Service functions
const loadFusionServices = async (packageId) => {
    try {
        const response = await axios.get(
            route("paket-layanans.fusion-services", packageId)
        );
        fusionServices.value = response.data.fusionServices;
        return response.data.fusionServices;
    } catch (error) {
        console.error("Error loading fusion services:", error);
        fusionServices.value = [];
        return [];
    }
};

const openAddFusionForm = (packageId) => {
    fusionFormMode.value = "add";
    currentFusion.value = {
        paket_layanan_id: packageId,
        nama_fusion: "",
        keterangan: "",
        custom_price: null,
    };
    selectedFusionServices.value = [];
    loadAvailableServices();
    showFusionForm.value = true;
};

const openEditFusionForm = async (fusion) => {
    fusionFormMode.value = "edit";
    currentFusion.value = { ...fusion };
    selectedFusionServices.value = fusion.layanan_ids || [];
    await loadAvailableServices();
    showFusionForm.value = true;
};

const closeFusionForm = () => {
    showFusionForm.value = false;
    selectedFusionServices.value = [];
    currentFusion.value = null;
};

const saveFusionService = () => {
    if (selectedFusionServices.value.length < 2) {
        proxy.$showSwalConfirm({
            title: "Error",
            text: "Please select at least 2 services to create a fusion",
            icon: "error",
        });
        return;
    }

    const data = {
        ...currentFusion.value,
        layanan_ids: selectedFusionServices.value,
    };

    if (fusionFormMode.value === "add") {
        router.post(route("fusion-services.store"), data, {
            preserveScroll: true,
            onSuccess: () => {
                closeFusionForm();
                loadFusionServices(currentFusion.value.paket_layanan_id);
            },
        });
    } else {
        router.put(route("fusion-services.update", currentFusion.value.id), data, {
            preserveScroll: true,
            onSuccess: () => {
                closeFusionForm();
                loadFusionServices(currentFusion.value.paket_layanan_id);
            },
        });
    }
};

const deleteFusionService = (fusion) => {
    proxy.$showSwalConfirm({
        onConfirm: () => {
            router.delete(route("fusion-services.destroy", fusion.id), {
                preserveScroll: true,
                onSuccess: () => {
                    loadFusionServices(fusion.paket_layanan_id);
                },
            });
        },
    });
};

const toggleFusionService = (serviceId) => {
    const index = selectedFusionServices.value.indexOf(serviceId);
    if (index > -1) {
        selectedFusionServices.value.splice(index, 1);
    } else {
        selectedFusionServices.value.push(serviceId);
    }
};


// Watch filtered services to update select all state
watch(filteredServices, () => {
    updateSelectAllState();
});

const formatCurrency = (value) => {
    // format and round the value
    value = Math.round(value);

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Service Packages" />

    <AdminLayout title="Service Package Management">
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
                :data="paketLayanans"
                :columns="columns"
                :filters="filters"
                route="paket-layanans.index"
                class="max-w-full whitespace-nowrap"
            >
                <template #title> Service Package Management </template>

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
                        <span>Add Service Package</span>
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
                :links="props.paketLayanans.links"
                :currentPage="props.paketLayanans.current_page"
                :perPage="props.paketLayanans.per_page"
                :totalEntries="props.paketLayanans.total"
            />
        </div>

        <!-- Package Services Section -->
        <div
            v-if="packageServices.length > 0 || fusionServices.length > 0"
            class="mt-8 border rounded-lg shadow-lg border-primary/40 bg-dark-card"
        >
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">
                        Services in "{{ selectedPaketLayanan?.judul_paket }}"
                    </h3>
                    <button
                        @click="openAddFusionForm(selectedPaketLayanan?.id)"
                        class="flex items-center px-3 py-2 space-x-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-secondary hover:bg-secondary-hover"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Add Fusion Service</span>
                    </button>
                </div>

                <!-- Regular Services -->
                <div v-if="packageServices.length > 0" class="mb-6">
                    <h4 class="mb-3 text-lg font-medium text-white">Regular Services</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="service in packageServices"
                            :key="service.id"
                            class="p-4 border border-gray-700 rounded-lg bg-dark-sidebar"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-white">
                                    {{ service.nama_layanan }}
                                </h4>
                                <span
                                    class="px-2 py-1 text-xs rounded bg-primary/20 text-primary"
                                >
                                    {{ service.kode_layanan }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-300">
                                Product: {{ service.produk?.nama || "N/A" }}
                            </p>
                            <p class="text-sm text-secondary">
                                Price: 
                                {{
                                    formatCurrency(service.harga_jual) ||
                                    formatCurrency(service.harga_beli) ||
                                    "N/A"
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fusion Services -->
                <div v-if="fusionServices.length > 0">
                    <h4 class="mb-3 text-lg font-medium text-white">Fusion Services</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="fusion in fusionServices"
                            :key="fusion.id"
                            class="p-4 border border-gray-700 rounded-lg bg-dark-sidebar"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-white">
                                    {{ fusion.nama_fusion }}
                                </h4>
                                <span
                                    class="px-2 py-1 text-xs rounded bg-secondary/20 text-secondary"
                                >
                                    FUSION
                                </span>
                            </div>
                            <p class="text-sm text-gray-300 mb-2">
                                {{ fusion.keterangan }}
                            </p>
                            <p class="text-sm text-secondary mb-3">
                                Services: {{ fusion.layanan_ids?.length || 0 }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-white">
                                    {{ formatCurrency(fusion.custom_price || 0) }}
                                </span>
                                <div class="flex space-x-2">
                                    <button
                                        @click="openEditFusionForm(fusion)"
                                        class="p-1 text-gray-400 transition-colors hover:text-primary"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="deleteFusionService(fusion)"
                                        class="p-1 text-gray-400 transition-colors hover:text-red-400"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Form Modal -->
        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
            @click.self="closeForm"
        >
            <div
                class="relative w-full max-w-4xl mx-4 p-3 border border-gray-700 rounded-lg shadow-lg sm:p-4 md:p-6 bg-dark-card max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">
                        {{
                            formMode === "add"
                                ? "Add New Service Package"
                                : "Edit Service Package"
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
                    @submit.prevent="savePaketLayanan"
                    class="overflow-visible"
                >
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Left Column - Package Info -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-white">
                                Package Information
                            </h4>

                            <!-- Package Title -->
                            <div>
                                <label
                                    for="judul_paket"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                >
                                    Package Title
                                </label>
                                <input
                                    id="judul_paket"
                                    v-model="currentPaketLayanan.judul_paket"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required
                                    placeholder="Enter package title"
                                />
                            </div>

                            <!-- Display Order -->
                            <div>
                                <label
                                    for="display_order"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                >
                                    Display Order
                                </label>
                                <input
                                    id="display_order"
                                    v-model.number="currentPaketLayanan.display_order"
                                    type="number"
                                    min="0"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="0"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Lower numbers appear first. Default is 0.
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label
                                    for="informasi"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                >
                                    Description
                                </label>
                                <textarea
                                    id="informasi"
                                    v-model="currentPaketLayanan.informasi"
                                    rows="4"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Enter package description"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Right Column - Service Selection -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-lg font-medium text-white">
                                    Select Services
                                </h4>
                                <span class="text-sm text-gray-400">
                                    {{ selectedServices.length }} selected
                                </span>
                            </div>

                            <!-- Search -->
                            <div>
                                <input
                                    v-model="serviceSearchQuery"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Search services..."
                                />
                            </div>

                            <!-- Select All -->
                            <div class="flex items-center space-x-2">
                                <input
                                    id="selectAll"
                                    v-model="selectAll"
                                    type="checkbox"
                                    @change="toggleSelectAll"
                                    class="w-4 h-4 border-gray-700 rounded text-primary bg-dark-sidebar focus:ring-primary focus:ring-2"
                                />
                                <label
                                    for="selectAll"
                                    class="text-sm text-gray-300"
                                >
                                    Select All ({{ filteredServices.length }}
                                    services)
                                </label>
                            </div>

                            <!-- Services List -->
                            <div class="space-y-2 overflow-y-auto max-h-96">
                                <div
                                    v-for="service in filteredServices"
                                    :key="service.id"
                                    class="flex items-center p-3 border border-gray-700 rounded-lg bg-dark-sidebar hover:bg-dark-lighter"
                                >
                                    <input
                                        :id="`service-${service.id}`"
                                        :value="service.id"
                                        :checked="
                                            selectedServices.includes(
                                                service.id
                                            )
                                        "
                                        type="checkbox"
                                        @change="toggleService(service.id)"
                                        class="w-4 h-4 mr-3 border-gray-700 rounded text-primary bg-dark-card focus:ring-primary focus:ring-2"
                                    />
                                    <label
                                        :for="`service-${service.id}`"
                                        class="flex-1 cursor-pointer"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div>
                                                <p
                                                    class="font-medium text-white"
                                                >
                                                    {{ service.nama_layanan }}
                                                </p>
                                                <p
                                                    class="text-sm text-gray-400"
                                                >
                                                    {{ service.kode_layanan }} -
                                                    {{
                                                        service.produk?.nama ||
                                                        "No Product"
                                                    }}
                                                </p>
                                            </div>
                                            <span
                                                class="text-sm text-secondary"
                                            >
                                                {{
                                                    formatCurrency(
                                                        service.harga_jual
                                                    ) ||
                                                    formatCurrency(
                                                        service.harga_beli
                                                    ) ||
                                                    "N/A"
                                                }}
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div
                                v-if="filteredServices.length === 0"
                                class="p-4 text-center text-gray-400"
                            >
                                No services available
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end mt-6 space-x-3">
                        <button
                            type="button"
                            @click="closeForm"
                            class="px-4 py-2 text-gray-300 transition-colors border border-gray-700 rounded-lg hover:bg-dark-lighter"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                        >
                            {{
                                formMode === "add"
                                    ? "Create Package"
                                    : "Update Package"
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
                class="relative w-full max-w-2xl mx-4 p-6 border border-gray-700 rounded-lg shadow-lg bg-dark-card max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">
                        Package Details
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

                <div
                    v-if="selectedPaketLayanan && !selectedPaketLayanan.loading"
                    class="space-y-4"
                >
                    <div>
                        <h4 class="text-lg font-semibold text-white">
                            {{ selectedPaketLayanan.judul_paket }}
                        </h4>
                        <p class="text-gray-400">
                            Product:
                            {{
                                selectedPaketLayanan.produk?.nama ||
                                "Not assigned"
                            }}
                        </p>
                    </div>

                    <div v-if="selectedPaketLayanan.informasi">
                        <h5 class="font-medium text-white">Description:</h5>
                        <p class="text-gray-300">
                            {{ selectedPaketLayanan.informasi }}
                        </p>
                    </div>

                    <div v-if="packageServices.length > 0">
                        <h5 class="font-medium text-white">
                            Services ({{ packageServices.length }}):
                        </h5>
                        <div class="mt-2 space-y-2">
                            <div
                                v-for="service in packageServices"
                                :key="service.id"
                                class="p-3 border border-gray-700 rounded-lg bg-dark-sidebar"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-white">
                                            {{ service.nama_layanan }}
                                        </p>
                                        <p class="text-sm text-gray-400">
                                            {{ service.kode_layanan }}
                                        </p>
                                    </div>
                                    <span class="text-sm text-secondary">
                                        Rp
                                        {{
                                            service.harga_jual?.toLocaleString() ||
                                            service.harga_beli?.toLocaleString() ||
                                            "N/A"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="flex items-center justify-center py-8">
                    <div
                        class="w-8 h-8 border-4 rounded-full animate-spin border-primary border-t-transparent"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Fusion Service Form Modal -->
        <div
            v-if="showFusionForm"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
            @click.self="closeFusionForm"
        >
            <div
                class="relative w-full max-w-4xl mx-4 p-6 border border-gray-700 rounded-lg shadow-lg bg-dark-card max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">
                        {{ fusionFormMode === "add" ? "Add New Fusion Service" : "Edit Fusion Service" }}
                    </h3>
                    <button
                        @click="closeFusionForm"
                        class="text-gray-400 hover:text-white"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveFusionService" class="overflow-visible">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Left Column - Fusion Info -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-medium text-white">Fusion Information</h4>

                            <!-- Fusion Name -->
                            <div>
                                <label for="nama_fusion" class="block mb-1 text-sm font-medium text-gray-300">
                                    Fusion Name
                                </label>
                                <input
                                    id="nama_fusion"
                                    v-model="currentFusion.nama_fusion"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-secondary focus:border-transparent"
                                    required
                                    placeholder="Enter fusion name"
                                />
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="keterangan" class="block mb-1 text-sm font-medium text-gray-300">
                                    Description
                                </label>
                                <textarea
                                    id="keterangan"
                                    v-model="currentFusion.keterangan"
                                    rows="3"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-secondary focus:border-transparent"
                                    placeholder="Enter fusion description"
                                ></textarea>
                            </div>

                            <!-- Custom Price -->
                            <div>
                                <label for="custom_price" class="block mb-1 text-sm font-medium text-gray-300">
                                    Custom Price (Optional)
                                </label>
                                <input
                                    id="custom_price"
                                    v-model.number="currentFusion.custom_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-secondary focus:border-transparent"
                                    placeholder="Leave empty to use sum of service prices"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    If not set, price will be calculated from selected services
                                </p>
                            </div>
                        </div>

                        <!-- Right Column - Service Selection -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-lg font-medium text-white">
                                    Select Services for Fusion
                                </h4>
                                <span class="text-sm text-gray-400">
                                    {{ selectedFusionServices.length }} selected
                                </span>
                            </div>

                            <!-- Search -->
                            <div>
                                <input
                                    v-model="serviceSearchQuery"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-secondary focus:border-transparent"
                                    placeholder="Search services..."
                                />
                            </div>

                            <!-- Services List -->
                            <div class="space-y-2 overflow-y-auto max-h-96">
                                <div
                                    v-for="service in filteredServices"
                                    :key="service.id"
                                    class="flex items-center p-3 border border-gray-700 rounded-lg bg-dark-sidebar hover:bg-dark-lighter"
                                >
                                    <input
                                        :id="`fusion-service-${service.id}`"
                                        :value="service.id"
                                        :checked="selectedFusionServices.includes(service.id)"
                                        type="checkbox"
                                        @change="toggleFusionService(service.id)"
                                        class="w-4 h-4 mr-3 border-gray-700 rounded text-secondary bg-dark-card focus:ring-secondary focus:ring-2"
                                    />
                                    <label
                                        :for="`fusion-service-${service.id}`"
                                        class="flex-1 cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-white">
                                                    {{ service.nama_layanan }}
                                                </p>
                                                <p class="text-sm text-gray-400">
                                                    {{ service.kode_layanan }} -
                                                    {{ service.produk?.nama || "No Product" }}
                                                </p>
                                            </div>
                                            <span class="text-sm text-secondary">
                                                {{
                                                    formatCurrency(service.harga_jual) ||
                                                    formatCurrency(service.harga_beli) ||
                                                    "N/A"
                                                }}
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div
                                v-if="filteredServices.length === 0"
                                class="p-4 text-center text-gray-400"
                            >
                                No services available
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end mt-6 space-x-3">
                        <button
                            type="button"
                            @click="closeFusionForm"
                            class="px-4 py-2 text-gray-300 transition-colors border border-gray-700 rounded-lg hover:bg-dark-lighter"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-secondary hover:bg-secondary-hover"
                        >
                            {{ fusionFormMode === "add" ? "Create Fusion" : "Update Fusion" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

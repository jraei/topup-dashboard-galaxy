<script setup>
import { ref, computed, watch, getCurrentInstance } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import Modal from "@/Components/Modal.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Pagination from "@/Components/Pagination.vue";
import { Package, Layers, Zap, AlertTriangle, CheckCircle, XCircle } from "lucide-vue-next";
import axios from "axios";

const props = defineProps({
    fusionServices: Object,
    packages: Array,
    errors: Object,
    filters: Object,
});

const { proxy } = getCurrentInstance();

// Data
const fusionServices = computed(() => props.fusionServices.data || []);

// Column definitions for the table
const columns = [
    { key: "id", label: "ID" },
    {
        key: "nama_fusion",
        label: "Fusion Name",
        format: (value, item) => {
            const statusIcon = item.status === 'active' 
                ? '<span class="w-2 h-2 mr-2 bg-green-500 rounded-full inline-block"></span>'
                : '<span class="w-2 h-2 mr-2 bg-red-500 rounded-full inline-block"></span>';
            
            return `
                <div class="flex items-center">
                    ${statusIcon}
                    <div class="flex flex-col">
                        <span class="font-medium text-white">${value}</span>
                        <span class="text-xs text-gray-400">${item.service_count} services combined</span>
                    </div>
                </div>
            `;
        },
    },
    {
        key: "package_name",
        label: "Package",
        format: (_, item) => {
            const packageName = item.paket_layanan?.judul_paket || "Unknown Package";
            return `<span class="text-secondary">${packageName}</span>`;
        },
    },
    {
        key: "calculated_price",
        label: "Price",
        format: (value, item) => {
            const customPrice = item.custom_price;
            const finalPrice = customPrice || value;
            
            return `
                <div class="flex flex-col">
                    <span class="font-medium text-primary">Rp ${finalPrice?.toLocaleString()}</span>
                    ${customPrice ? '<span class="text-xs text-yellow-400">Custom Price</span>' : '<span class="text-xs text-gray-400">Calculated</span>'}
                </div>
            `;
        },
    },
    {
        key: "status",
        label: "Status",
        format: (value) => {
            return `<span class="px-2 py-1 rounded-xl text-xs 
                    ${value === "active" 
                        ? "bg-green-500/20 text-green-400" 
                        : "bg-red-500/20 text-red-400"
                    }">
                    ${value.charAt(0).toUpperCase() + value.slice(1)}
                </span>`;
        },
    },
];

// Modal states
const showForm = ref(false);
const showViewModal = ref(false);
const formMode = ref("add");
const currentFusionService = ref(null);
const selectedFusionService = ref(null);
const isLoading = ref(false);

// Form data
const availableServices = ref([]);
const compatibility = ref(null);
const pricingPreview = ref(null);
const selectedServices = ref([]);

// Form functions
const openAddForm = () => {
    formMode.value = "add";
    currentFusionService.value = {
        nama_fusion: "",
        deskripsi: "",
        paket_layanan_id: "",
        layanan_ids: [],
        custom_price: null,
        status: "active",
        display_order: 0,
    };
    availableServices.value = [];
    compatibility.value = null;
    pricingPreview.value = null;
    selectedServices.value = [];
    showForm.value = true;
};

const openEditForm = (fusionService) => {
    if (showViewModal.value) {
        showViewModal.value = false;
    }
    formMode.value = "edit";
    currentFusionService.value = {
        id: fusionService.id,
        nama_fusion: fusionService.nama_fusion,
        deskripsi: fusionService.deskripsi || "",
        paket_layanan_id: fusionService.paket_layanan_id,
        layanan_ids: fusionService.layanan_ids || [],
        custom_price: fusionService.custom_price,
        status: fusionService.status,
        display_order: fusionService.display_order || 0,
    };
    
    // Load services for the package
    if (fusionService.paket_layanan_id) {
        loadServicesForPackage(fusionService.paket_layanan_id);
    }
    
    selectedServices.value = fusionService.layanan_ids || [];
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    currentFusionService.value = null;
    availableServices.value = [];
    compatibility.value = null;
    pricingPreview.value = null;
    selectedServices.value = [];
};

// Load services for selected package
const loadServicesForPackage = async (packageId) => {
    if (!packageId) {
        availableServices.value = [];
        return;
    }

    try {
        const response = await axios.post(route("fusion-services.services"), {
            package_id: packageId,
        });

        if (response.data.success) {
            availableServices.value = response.data.services;
        }
    } catch (error) {
        console.error("Error loading services:", error);
        availableServices.value = [];
    }
};

// Validate fusion compatibility
const validateCompatibility = async () => {
    if (selectedServices.value.length < 2) {
        compatibility.value = null;
        return;
    }

    try {
        const response = await axios.post(route("fusion-services.validate"), {
            layanan_ids: selectedServices.value,
        });

        if (response.data.success) {
            compatibility.value = response.data.compatibility;
        }
    } catch (error) {
        console.error("Error validating compatibility:", error);
        compatibility.value = null;
    }
};

// Calculate pricing preview
const calculatePricing = async () => {
    if (selectedServices.value.length < 2) {
        pricingPreview.value = null;
        return;
    }

    try {
        const response = await axios.post(route("fusion-services.pricing"), {
            layanan_ids: selectedServices.value,
            custom_price: currentFusionService.value.custom_price,
        });

        if (response.data.success) {
            pricingPreview.value = response.data.pricing;
        }
    } catch (error) {
        console.error("Error calculating pricing:", error);
        pricingPreview.value = null;
    }
};

// Save fusion service
const saveFusionService = () => {
    currentFusionService.value.layanan_ids = selectedServices.value;
    
    if (formMode.value === "add") {
        router.post(route("fusion-services.store"), currentFusionService.value, {
            preserveScroll: true,
            onSuccess: () => {
                closeForm();
            },
        });
    } else {
        router.put(
            route("fusion-services.update", currentFusionService.value.id),
            currentFusionService.value,
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeForm();
                },
            }
        );
    }
};

// Handle view action
const handleView = async (item) => {
    isLoading.value = true;
    selectedFusionService.value = { ...item, loading: true };
    showViewModal.value = true;

    try {
        const response = await axios.get(route("fusion-services.show", item.id));
        selectedFusionService.value = response.data;
    } catch (error) {
        console.error("Error fetching fusion service details:", error);
        proxy.$showSwalConfirm({
            title: "Error",
            text: "Failed to load fusion service details",
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
            router.delete(route("fusion-services.destroy", item.id), {
                preserveScroll: true,
            });
        },
    });
};

const closeViewModal = () => {
    showViewModal.value = false;
    selectedFusionService.value = null;
};

// Toggle service selection
const toggleServiceSelection = (serviceId) => {
    const index = selectedServices.value.indexOf(serviceId);
    if (index > -1) {
        selectedServices.value.splice(index, 1);
    } else {
        if (selectedServices.value.length < 5) {
            selectedServices.value.push(serviceId);
        }
    }
};

// Watch for changes
watch(
    () => currentFusionService.value?.paket_layanan_id,
    (newValue) => {
        if (newValue) {
            loadServicesForPackage(newValue);
            selectedServices.value = [];
        }
    }
);

watch(selectedServices, () => {
    validateCompatibility();
    calculatePricing();
}, { deep: true });

watch(
    () => currentFusionService.value?.custom_price,
    () => {
        if (selectedServices.value.length >= 2) {
            calculatePricing();
        }
    }
);
</script>

<template>
    <Head title="Fusion Services Management" />

    <AdminLayout title="Fusion Services Management">
        <div
            v-if="Object.keys(errors || {}).length > 0"
            class="px-4 py-3 mb-4 text-sm text-white rounded-lg bg-red-500/80"
        >
            <ul v-for="error in errors">
                <li>{{ error }}</li>
            </ul>
        </div>

        <div class="w-full border rounded-lg shadow-lg border-primary/40 bg-dark-card">
            <DataTable
                :data="fusionServices"
                :columns="columns"
                :filters="filters"
                route="fusion-services.index"
                class="max-w-full whitespace-nowrap"
            >
                <template #title>
                    <div class="flex items-center space-x-2">
                        <Layers class="w-6 h-6 text-primary" />
                        <span>Fusion Services Management</span>
                    </div>
                </template>

                <template #addButton>
                    <button
                        @click="openAddForm"
                        class="flex items-center px-4 py-2 space-x-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                    >
                        <Zap class="w-5 h-5" />
                        <span>Create Fusion Service</span>
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
                            <div class="py-1 border rounded-lg shadow-lg border-primary/60 bg-dark-card">
                                <button
                                    @click="handleView(item)"
                                    class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-secondary"
                                >
                                    View Details
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
                :links="props.fusionServices.links"
                :currentPage="props.fusionServices.current_page"
                :perPage="props.fusionServices.per_page"
                :totalEntries="props.fusionServices.total"
            />
        </div>

        <!-- Add/Edit Form Modal -->
        <Modal :show="showForm" @close="closeForm" max-width="4xl">
            <div class="p-6 border shadow-xl rounded-2xl bg-content_background/30 border-secondary/20 backdrop-blur">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">
                        {{ formMode === "add" ? "Create New Fusion Service" : "Edit Fusion Service" }}
                    </h2>
                    <button @click="closeForm" class="text-gray-400 hover:text-white">
                        &times;
                    </button>
                </div>

                <form @submit.prevent="saveFusionService" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Basic Info -->
                        <div class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">
                                    Fusion Name *
                                </label>
                                <input
                                    v-model="currentFusionService.nama_fusion"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required
                                    placeholder="e.g., ML + FF Diamond Combo"
                                />
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">
                                    Description
                                </label>
                                <textarea
                                    v-model="currentFusionService.deskripsi"
                                    rows="3"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Describe the fusion service benefits..."
                                ></textarea>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">
                                    Service Package *
                                </label>
                                <select
                                    v-model="currentFusionService.paket_layanan_id"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    required
                                >
                                    <option value="">Select Package</option>
                                    <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                                        {{ pkg.judul_paket }}
                                    </option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">
                                        Custom Price (Optional)
                                    </label>
                                    <input
                                        v-model="currentFusionService.custom_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Leave empty for auto-calc"
                                    />
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-300">
                                        Display Order
                                    </label>
                                    <input
                                        v-model="currentFusionService.display_order"
                                        type="number"
                                        min="0"
                                        class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">
                                    Status
                                </label>
                                <select
                                    v-model="currentFusionService.status"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">
                                    Select Services to Combine (2-5 services) *
                                </label>
                                <div v-if="availableServices.length === 0" class="p-4 text-center text-gray-400 border border-gray-700 rounded-lg bg-dark-sidebar">
                                    Select a package first to see available services
                                </div>
                                <div v-else class="space-y-2 max-h-60 overflow-y-auto">
                                    <div
                                        v-for="service in availableServices"
                                        :key="service.id"
                                        @click="toggleServiceSelection(service.id)"
                                        class="flex items-center justify-between p-3 border border-gray-700 rounded-lg cursor-pointer hover:border-primary transition-colors"
                                        :class="{
                                            'border-primary bg-primary/10': selectedServices.includes(service.id),
                                            'border-gray-700 bg-dark-sidebar': !selectedServices.includes(service.id)
                                        }"
                                    >
                                        <div class="flex items-center space-x-3">
                                            <input
                                                type="checkbox"
                                                :checked="selectedServices.includes(service.id)"
                                                class="w-4 h-4 text-primary bg-transparent border-gray-600 rounded focus:ring-primary focus:ring-2"
                                                @click.stop
                                            />
                                            <div>
                                                <div class="text-white font-medium">{{ service.nama_layanan }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ service.produk?.provider?.provider_name }} | 
                                                    Rp {{ service.harga_beli?.toLocaleString() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Compatibility Status -->
                            <div v-if="compatibility" class="p-4 border rounded-lg" :class="{
                                'border-green-500 bg-green-500/10': compatibility.compatible,
                                'border-red-500 bg-red-500/10': !compatibility.compatible
                            }">
                                <div class="flex items-center space-x-2 mb-2">
                                    <CheckCircle v-if="compatibility.compatible" class="w-5 h-5 text-green-400" />
                                    <XCircle v-else class="w-5 h-5 text-red-400" />
                                    <span class="font-medium text-white">
                                        {{ compatibility.compatible ? 'Compatible Combination' : 'Compatibility Issues' }}
                                    </span>
                                </div>
                                
                                <div v-if="!compatibility.compatible" class="space-y-1">
                                    <div v-for="issue in compatibility.issues" :key="issue" class="text-sm text-red-300">
                                        • {{ issue }}
                                    </div>
                                </div>

                                <div v-if="compatibility.providers.length > 0" class="mt-3">
                                    <div class="text-sm text-gray-300 mb-1">Providers involved:</div>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="provider in compatibility.providers" :key="provider.name" 
                                              class="px-2 py-1 text-xs rounded bg-primary/20 text-primary">
                                            {{ provider.name }} ({{ provider.service_count }})
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Preview -->
                            <div v-if="pricingPreview" class="p-4 border border-gray-700 rounded-lg bg-dark-sidebar">
                                <h4 class="font-medium text-white mb-3">Pricing Preview</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">Base Total:</span>
                                        <span class="text-white">Rp {{ pricingPreview.base_total.toLocaleString() }}</span>
                                    </div>
                                    <div v-if="currentFusionService.custom_price" class="flex justify-between">
                                        <span class="text-yellow-300">Custom Price:</span>
                                        <span class="text-yellow-300 font-medium">Rp {{ currentFusionService.custom_price.toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                        <button
                            type="button"
                            @click="closeForm"
                            class="px-4 py-2 text-gray-300 border border-gray-600 rounded-lg hover:bg-gray-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="selectedServices.length < 2 || (compatibility && !compatibility.compatible)"
                            class="px-4 py-2 text-white bg-primary rounded-lg hover:bg-primary-hover disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ formMode === "add" ? "Create Fusion Service" : "Update Fusion Service" }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- View Details Modal -->
        <Modal :show="showViewModal" @close="closeViewModal" max-width="3xl">
            <div class="p-6 border shadow-xl rounded-2xl bg-content_background/30 border-secondary/20 backdrop-blur">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">Fusion Service Details</h2>
                    <button @click="closeViewModal" class="text-gray-400 hover:text-white">
                        &times;
                    </button>
                </div>

                <div v-if="isLoading" class="text-center text-gray-400">
                    Loading details...
                </div>

                <div v-else-if="selectedFusionService" class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-white mb-3">Basic Information</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Name:</span>
                                    <span class="text-white">{{ selectedFusionService.fusionService?.nama_fusion }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Package:</span>
                                    <span class="text-white">{{ selectedFusionService.fusionService?.paket_layanan?.judul_paket }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Status:</span>
                                    <span :class="selectedFusionService.fusionService?.status === 'active' ? 'text-green-400' : 'text-red-400'">
                                        {{ selectedFusionService.fusionService?.status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-medium text-white mb-3">Service Details</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Service Count:</span>
                                    <span class="text-white">{{ selectedFusionService.services?.length || 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Compatible Inputs:</span>
                                    <span :class="selectedFusionService.hasCompatibleInputs ? 'text-green-400' : 'text-red-400'">
                                        {{ selectedFusionService.hasCompatibleInputs ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Display Order:</span>
                                    <span class="text-white">{{ selectedFusionService.fusionService?.display_order || 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="selectedFusionService.fusionService?.deskripsi">
                        <h3 class="font-medium text-white mb-2">Description</h3>
                        <p class="text-gray-300 text-sm">{{ selectedFusionService.fusionService.deskripsi }}</p>
                    </div>

                    <!-- Services List -->
                    <div v-if="selectedFusionService.services">
                        <h3 class="font-medium text-white mb-3">Combined Services</h3>
                        <div class="space-y-2">
                            <div v-for="service in selectedFusionService.services" :key="service.id"
                                 class="flex items-center justify-between p-3 border border-gray-700 rounded-lg bg-dark-sidebar">
                                <div>
                                    <div class="text-white font-medium">{{ service.nama_layanan }}</div>
                                    <div class="text-xs text-gray-400">{{ service.kode_layanan }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-primary font-medium">Rp {{ service.harga_beli?.toLocaleString() }}</div>
                                    <div class="text-xs text-gray-400">{{ service.produk?.provider?.provider_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validation Errors -->
                    <div v-if="selectedFusionService.validationErrors?.length > 0" class="p-4 border border-red-500 rounded-lg bg-red-500/10">
                        <h3 class="font-medium text-red-400 mb-2 flex items-center">
                            <AlertTriangle class="w-4 h-4 mr-2" />
                            Validation Issues
                        </h3>
                        <ul class="space-y-1">
                            <li v-for="error in selectedFusionService.validationErrors" :key="error" 
                                class="text-sm text-red-300">• {{ error }}</li>
                        </ul>
                    </div>

                    <!-- Price Breakdown -->
                    <div v-if="selectedFusionService.priceBreakdown">
                        <h3 class="font-medium text-white mb-3">Price Breakdown</h3>
                        <div class="space-y-2">
                            <div v-for="breakdown in selectedFusionService.priceBreakdown" :key="breakdown.service_id"
                                 class="flex items-center justify-between p-2 border border-gray-700 rounded bg-dark-sidebar/50">
                                <div>
                                    <span class="text-white text-sm">{{ breakdown.service_name }}</span>
                                    <span class="text-xs text-gray-400 ml-2">({{ breakdown.product_name }})</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-primary text-sm">Rp {{ breakdown.final_price?.toLocaleString() }}</div>
                                    <div v-if="breakdown.provider" class="text-xs text-gray-400">{{ breakdown.provider }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, watch, getCurrentInstance } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    show: Boolean,
    products: Array,
    roles: Array,
});

const emit = defineEmits(["close"]);

const { proxy } = getCurrentInstance();

// Form data
const formData = ref({
    user_roles_id: "",
    type: "percent",
    value: 1.0,
    product_ids: [],
});

// UI state
const currentStep = ref(1);
const isLoading = ref(false);
const searchTerm = ref("");

// Computed
const filteredProducts = computed(() => {
    if (!searchTerm.value) return props.products;
    return props.products.filter(product =>
        product.nama.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

const selectedProducts = computed(() => {
    return props.products.filter(product =>
        formData.value.product_ids.includes(product.id)
    );
});

const canProceedToStep2 = computed(() => {
    return formData.value.user_roles_id !== "";
});

const canProceedToStep3 = computed(() => {
    return formData.value.product_ids.length > 0;
});

const canSubmit = computed(() => {
    return formData.value.value > 0;
});

// Methods
const toggleProduct = (productId) => {
    const index = formData.value.product_ids.indexOf(productId);
    if (index > -1) {
        formData.value.product_ids.splice(index, 1);
    } else {
        formData.value.product_ids.push(productId);
    }
};

const selectAllProducts = () => {
    if (formData.value.product_ids.length === filteredProducts.value.length) {
        formData.value.product_ids = [];
    } else {
        formData.value.product_ids = filteredProducts.value.map(p => p.id);
    }
};

const nextStep = () => {
    if (currentStep.value < 3) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submitBulkAssign = async () => {
    isLoading.value = true;
    
    try {
        const response = await axios.post(route("profit-produks.bulk-store"), formData.value);
        
        if (response.data.success) {
            proxy.$showSwalConfirm({
                title: "Success",
                text: response.data.message,
                icon: "success",
                showCancelButton: false,
            });
            resetForm();
            emit("close");
            // Refresh the page data
            router.reload({ only: ['profitProduks'] });
        }
    } catch (error) {
        const message = error.response?.data?.message || "Failed to create bulk profit settings";
        proxy.$showSwalConfirm({
            title: "Error",
            text: message,
            icon: "error",
            showCancelButton: false,
        });
    } finally {
        isLoading.value = false;
    }
};

const resetForm = () => {
    formData.value = {
        user_roles_id: "",
        type: "percent",
        value: 1.0,
        product_ids: [],
    };
    currentStep.value = 1;
    searchTerm.value = "";
};

const closeModal = () => {
    resetForm();
    emit("close");
};

// Watch for modal show/hide
watch(() => props.show, (newValue) => {
    if (!newValue) {
        resetForm();
    }
});
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black/50"
        @click.self="closeModal"
    >
        <div
            class="relative w-full max-w-2xl mx-4 p-4 border border-gray-700 rounded-lg shadow-lg bg-dark-card max-h-[90vh] overflow-y-auto"
            @click.stop
        >
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">
                    Bulk Assign Profit Settings
                </h3>
                <button
                    @click="closeModal"
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

            <!-- Progress Steps -->
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div
                            :class="currentStep >= 1 ? 'bg-primary text-white' : 'bg-gray-600 text-gray-400'"
                            class="flex items-center justify-center w-8 h-8 rounded-full"
                        >
                            1
                        </div>
                        <span class="ml-2 text-sm text-gray-300">Role</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-600"></div>
                    <div class="flex items-center">
                        <div
                            :class="currentStep >= 2 ? 'bg-primary text-white' : 'bg-gray-600 text-gray-400'"
                            class="flex items-center justify-center w-8 h-8 rounded-full"
                        >
                            2
                        </div>
                        <span class="ml-2 text-sm text-gray-300">Products</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-600"></div>
                    <div class="flex items-center">
                        <div
                            :class="currentStep >= 3 ? 'bg-primary text-white' : 'bg-gray-600 text-gray-400'"
                            class="flex items-center justify-center w-8 h-8 rounded-full"
                        >
                            3
                        </div>
                        <span class="ml-2 text-sm text-gray-300">Settings</span>
                    </div>
                </div>
            </div>

            <!-- Step 1: Role Selection -->
            <div v-if="currentStep === 1" class="space-y-4">
                <h4 class="text-lg font-medium text-white">Select User Role</h4>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-300">
                        User Role
                    </label>
                    <select
                        v-model="formData.user_roles_id"
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
            </div>

            <!-- Step 2: Product Selection -->
            <div v-if="currentStep === 2" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-medium text-white">Select Products</h4>
                    <span class="text-sm text-gray-400">
                        {{ formData.product_ids.length }} selected
                    </span>
                </div>

                <!-- Search -->
                <div>
                    <input
                        v-model="searchTerm"
                        type="text"
                        placeholder="Search products..."
                        class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                </div>

                <!-- Select All -->
                <div class="flex items-center space-x-2">
                    <button
                        @click="selectAllProducts"
                        class="px-3 py-1 text-sm text-primary border border-primary rounded hover:bg-primary hover:text-white transition-colors"
                    >
                        {{ formData.product_ids.length === filteredProducts.length ? 'Deselect All' : 'Select All' }}
                    </button>
                </div>

                <!-- Product List -->
                <div class="max-h-64 overflow-y-auto space-y-2">
                    <div
                        v-for="product in filteredProducts"
                        :key="product.id"
                        @click="toggleProduct(product.id)"
                        class="flex items-center space-x-3 p-3 border border-gray-700 rounded-lg cursor-pointer hover:bg-dark-lighter transition-colors"
                        :class="formData.product_ids.includes(product.id) ? 'bg-primary/10 border-primary/40' : ''"
                    >
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                :checked="formData.product_ids.includes(product.id)"
                                class="w-4 h-4 text-primary border-gray-600 rounded bg-dark-sidebar focus:ring-primary focus:ring-2"
                                @click.stop
                            />
                        </div>
                        <img
                            :src="'/storage/' + product.thumbnail"
                            :alt="product.nama"
                            class="object-cover w-10 h-10 rounded"
                        />
                        <div class="flex-1">
                            <p class="text-white font-medium">{{ product.nama }}</p>
                            <p class="text-sm text-gray-400">{{ product.brand }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Profit Settings -->
            <div v-if="currentStep === 3" class="space-y-4">
                <h4 class="text-lg font-medium text-white">Configure Profit Settings</h4>
                
                <!-- Selected Products Summary -->
                <div class="p-3 bg-dark-lighter rounded-lg">
                    <p class="text-sm text-gray-300 mb-2">
                        Selected Products: {{ selectedProducts.length }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="product in selectedProducts.slice(0, 5)"
                            :key="product.id"
                            class="px-2 py-1 text-xs bg-primary/20 text-primary rounded"
                        >
                            {{ product.nama }}
                        </span>
                        <span
                            v-if="selectedProducts.length > 5"
                            class="px-2 py-1 text-xs bg-gray-600 text-gray-300 rounded"
                        >
                            +{{ selectedProducts.length - 5 }} more
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Profit Type -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-300">
                            Profit Type
                        </label>
                        <select
                            v-model="formData.type"
                            class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                            <option value="percent">Percentage (%)</option>
                            <option value="multiplier">Multiplier (x)</option>
                        </select>
                    </div>

                    <!-- Value Input -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-300">
                            Value
                        </label>
                        <div class="relative">
                            <input
                                v-model="formData.value"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                required
                            />
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 pointer-events-none">
                                {{ formData.type === "percent" ? "%" : "x" }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-between pt-6 mt-6 border-t border-gray-700">
                <div>
                    <button
                        v-if="currentStep > 1"
                        @click="prevStep"
                        class="px-4 py-2 text-gray-300 rounded-lg bg-dark-lighter hover:text-white transition-colors"
                    >
                        Previous
                    </button>
                </div>

                <div class="flex space-x-3">
                    <button
                        @click="closeModal"
                        class="px-4 py-2 text-gray-300 rounded-lg bg-dark-lighter hover:text-white transition-colors"
                    >
                        Cancel
                    </button>
                    
                    <button
                        v-if="currentStep < 3"
                        @click="nextStep"
                        :disabled="(currentStep === 1 && !canProceedToStep2) || (currentStep === 2 && !canProceedToStep3)"
                        class="px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Next
                    </button>
                    
                    <button
                        v-if="currentStep === 3"
                        @click="submitBulkAssign"
                        :disabled="!canSubmit || isLoading"
                        class="px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="isLoading">Creating...</span>
                        <span v-else>Create Profit Settings</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

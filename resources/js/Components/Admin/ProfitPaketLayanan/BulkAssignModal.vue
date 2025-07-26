<script setup>
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    packages: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close"]);

// Form data
const formData = ref({
    package_ids: [],
    user_roles_id: "",
    type: "percent",
    value: 1.0,
});

const isLoading = ref(false);
const selectAll = ref(false);

// Reset form when modal is opened/closed
const resetForm = () => {
    formData.value = {
        package_ids: [],
        user_roles_id: "",
        type: "percent",
        value: 1.0,
    };
    selectAll.value = false;
};

// Handle select all checkbox
const handleSelectAll = () => {
    if (selectAll.value) {
        formData.value.package_ids = props.packages.map((p) => p.id);
    } else {
        formData.value.package_ids = [];
    }
};

// Handle individual package selection
const handlePackageSelection = () => {
    selectAll.value =
        formData.value.package_ids.length === props.packages.length;
};

// Close modal
const closeModal = () => {
    resetForm();
    emit("close");
};

// Submit bulk assignment
const submitBulkAssign = async () => {
    if (formData.value.package_ids.length === 0) {
        alert("Please select at least one package");
        return;
    }

    if (!formData.value.user_roles_id) {
        alert("Please select a user role");
        return;
    }

    isLoading.value = true;

    try {
        const response = await axios.post(
            route("profit-paket-layanans.bulk-store"),
            formData.value,
            {
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            }
        );

        if (response.data.success) {
            // Show success message
            alert(response.data.message);
            closeModal();
            // Reload the page to show updated data
            window.location.reload();
        } else {
            alert("Error: " + response.data.message);
        }
    } catch (error) {
        console.error("Error in bulk assignment:", error);
        let errorMessage = "Failed to create bulk profit settings";

        if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }

        alert(errorMessage);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
        @click.self="closeModal"
    >
        <div
            class="relative w-full max-w-2xl mx-4 p-6 border border-gray-700 rounded-lg shadow-lg bg-dark-card max-h-[90vh] overflow-y-auto"
            @click.stop
        >
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

            <form @submit.prevent="submitBulkAssign" class="space-y-6">
                <!-- Package Selection -->
                <div>
                    <label class="block mb-3 text-sm font-medium text-gray-300">
                        Select Service Packages
                    </label>

                    <!-- Select All Checkbox -->
                    <div class="mb-3">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                v-model="selectAll"
                                @change="handleSelectAll"
                                class="border-gray-300 rounded shadow-sm text-primary focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-300"
                                >Select All Packages</span
                            >
                        </label>
                    </div>

                    <!-- Package List -->
                    <div
                        class="p-3 overflow-y-auto border border-gray-700 rounded-lg max-h-48 bg-dark-sidebar"
                    >
                        <div
                            v-for="pkg in packages"
                            :key="pkg.id"
                            class="flex items-start py-2 space-x-3"
                        >
                            <input
                                type="checkbox"
                                :id="`package-${pkg.id}`"
                                :value="pkg.id"
                                v-model="formData.package_ids"
                                @change="handlePackageSelection"
                                class="mt-1 border-gray-300 rounded shadow-sm text-primary focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                            />
                            <label
                                :for="`package-${pkg.id}`"
                                class="flex-1 cursor-pointer"
                            >
                                <div class="flex items-center space-x-2">
                                    <img
                                        v-if="pkg.produk?.thumbnail"
                                        :src="`/storage/${pkg.produk.thumbnail}`"
                                        :alt="pkg.judul_paket"
                                        class="object-cover w-8 h-8 rounded-sm"
                                    />
                                    <div>
                                        <p
                                            class="text-sm font-medium text-white"
                                        >
                                            {{ pkg.judul_paket }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{
                                                pkg.produk?.nama || "No Product"
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <p class="mt-2 text-xs text-gray-400">
                        {{ formData.package_ids.length }} of
                        {{ packages.length }} packages selected
                    </p>
                </div>

                <!-- Role Selection -->
                <div>
                    <label
                        for="user_roles_id"
                        class="block mb-2 text-sm font-medium text-gray-300"
                    >
                        User Role
                    </label>
                    <select
                        id="user_roles_id"
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

                <!-- Profit Settings -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Type Selection -->
                    <div>
                        <label
                            for="type"
                            class="block mb-2 text-sm font-medium text-gray-300"
                        >
                            Profit Type
                        </label>
                        <select
                            id="type"
                            v-model="formData.type"
                            class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                            required
                        >
                            <option value="percent">Percentage</option>
                            <option value="multiplier">Multiplier</option>
                        </select>
                    </div>

                    <!-- Value Input -->
                    <div>
                        <label
                            for="value"
                            class="block mb-2 text-sm font-medium text-gray-300"
                        >
                            Value
                            <span
                                class="text-xs text-gray-400"
                                v-if="formData.type === 'percent'"
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
                            v-model="formData.value"
                            class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                            required
                        />
                    </div>
                </div>

                <!-- Warning Note -->
                <div
                    class="p-4 border rounded-lg border-amber-500/30 bg-amber-500/10"
                >
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="w-5 h-5 text-amber-400"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-400">
                                Note
                            </h3>
                            <p class="mt-1 text-sm text-amber-200">
                                This will create profit settings for all
                                selected packages. Existing settings for the
                                same package+role combination will be skipped.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2 text-gray-400 border border-gray-600 rounded-lg hover:bg-gray-700 hover:text-white"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="
                            isLoading || formData.package_ids.length === 0
                        "
                        class="px-4 py-2 text-white rounded-lg bg-primary hover:bg-primary-hover disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="isLoading">Creating...</span>
                        <span v-else>Create Profit Settings</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

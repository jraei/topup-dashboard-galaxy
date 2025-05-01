
<script setup>
import { ref, computed, onMounted, watch } from "vue";
import axios from 'axios';
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";
import CosmicCard from "@/Components/Order/CosmicCard.vue";
import CosmicParticles from "@/Components/User/Flashsale/CosmicParticles.vue";
import { usePersistedAccount } from "@/Composables/usePersistedAccount";

const props = defineProps({
    inputFields: Array,
    produk: Object,
});

const emit = defineEmits(['account-data-change', 'username-validated']);

const showModal = ref(false);
const savedIdForFuture = ref(false);
const accountUsername = ref(null);
const isValidatingAccount = ref(false);
const validationError = ref(null);

// Create refs for form inputs
const formData = ref({});
const highlightSavedData = ref(false);

const { saveAccount, getAccountForProduct, hasSavedAccount } = usePersistedAccount();

// Check if there's saved data for this product
const hasSavedData = computed(() => {
    return hasSavedAccount(props.produk.slug);
});

// Initialize form data with empty values
onMounted(() => {
    // Initialize form data structure
    props.inputFields.forEach(field => {
        formData.value[field.name] = '';
    });
    
    // If there's saved account data, populate the form
    const savedData = getAccountForProduct(props.produk.slug);
    if (savedData) {
        // Fill in the form with saved data
        Object.keys(savedData).forEach(key => {
            if (formData.value[key] !== undefined) {
                formData.value[key] = savedData[key];
            }
        });
        
        // Highlight the form to show saved data was applied
        highlightSavedData.value = true;
        setTimeout(() => {
            highlightSavedData.value = false;
        }, 1500);
    }
});

// Watch for changes to form data and emit them to parent
watch(formData, (newData) => {
    emit('account-data-change', newData);
}, { deep: true });

// Save account data when checkbox is checked
watch(savedIdForFuture, (newVal) => {
    if (newVal && props.produk.slug) {
        // Collect relevant account data
        const accountData = {};
        
        // Map input fields to account data
        props.inputFields.forEach(field => {
            if (['user_id', 'server_id', 'server', 'zone_id'].includes(field.name)) {
                accountData[field.name] = formData.value[field.name];
            }
        });
        
        if (Object.keys(accountData).length > 0) {
            saveAccount(props.produk.slug, accountData);
        }
    }
});

// Validate account username if applicable
const validateUsername = async () => {
    // Make sure we have the required fields
    const requiredFields = props.inputFields.filter(f => f.required);
    const allRequiredFilled = requiredFields.every(field => 
        formData.value[field.name] && formData.value[field.name].toString().trim() !== ''
    );
    
    if (!allRequiredFilled || !props.produk.validasi_id || props.produk.validasi_id === 'tidak') {
        return;
    }
    
    // Find user ID and zone/server ID fields
    const userId = formData.value['user_id'];
    const zoneId = formData.value['zone_id'] || formData.value['server_id'] || formData.value['server'];
    
    if (!userId) return;
    
    try {
        isValidatingAccount.value = true;
        validationError.value = null;
        accountUsername.value = null;
        
        const response = await axios.post('/api/check-username', {
            game: props.produk.validasi_id,
            user_id: userId,
            zone_id: zoneId
        });
        
        if (response.data.status === 'success' && response.data.username) {
            accountUsername.value = response.data.username;
            emit('username-validated', {
                username: response.data.username,
                userId,
                zoneId
            });
        }
    } catch (error) {
        validationError.value = error.response?.data?.message || 'Failed to validate account';
        console.error('Account validation error:', error);
    } finally {
        isValidatingAccount.value = false;
    }
};

// Debounce username validation
let validateTimer = null;
const debouncedValidateUsername = () => {
    clearTimeout(validateTimer);
    validateTimer = setTimeout(() => {
        validateUsername();
    }, 800);
};

// Watch for changes to relevant form fields to trigger validation
watch(() => [
    formData.value?.user_id,
    formData.value?.zone_id,
    formData.value?.server_id,
    formData.value?.server
], () => {
    debouncedValidateUsername();
}, { deep: true });
</script>

<template>
    <div class="absolute inset-0 z-0">
        <CosmicParticles />
    </div>
    <CosmicCard :title="'Masukkan Data Akun'" :step-number="1">
        <form class="relative z-10" @submit.prevent>
            <div 
                class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4"
                :class="{ 'cosmic-highlight-pulse': highlightSavedData }"
            >
                <!-- Dynamic input fields based on produk_input_fields -->
                <template v-for="field in inputFields" :key="field.id">
                    <div class="space-y-2">
                        <label
                            :for="`field_${field.name}`"
                            class="block text-sm font-semibold text-primary-text"
                        >
                            {{ field.label }}
                            <span
                                v-if="field.required"
                                class="ml-1 text-secondary"
                                >*</span
                            >
                        </label>

                        <!-- Text/Number Input -->
                        <input
                            v-if="field.type === 'text' || field.type === 'number'"
                            :type="field.type"
                            :id="`field_${field.name}`"
                            :name="field.name"
                            v-model="formData[field.name]"
                            :required="field.required"
                            class="w-full rounded-lg outline-none bg-secondary/20 border-secondary placeholder-secondary focus:ring-secondary focus:border-primary/70 focus:bg-secondary/20/90 text-primary-text cosmic-input-effect"
                        />

                        <!-- Select Input -->
                        <select
                            v-else-if="field.type === 'select'"
                            :id="`field_${field.name}`"
                            :name="field.name"
                            v-model="formData[field.name]"
                            :required="field.required"
                            class="w-full rounded-lg bg-secondary/20 border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/50 text-primary-text cosmic-input-effect"
                        >
                            <option value="" disabled selected>
                                Select an option
                            </option>
                            <option
                                v-for="option in field.options"
                                :key="option.id"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </template>
            </div>

            <!-- Username validation result -->
            <div v-if="accountUsername || validationError" class="mt-4">
                <div v-if="accountUsername" class="p-3 rounded-lg bg-green-500/20 border border-green-500/30">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-green-300">Account verified: {{ accountUsername }}</span>
                    </div>
                </div>
                <div v-if="validationError" class="p-3 rounded-lg bg-red-500/20 border border-red-500/30">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-red-300">{{ validationError }}</span>
                    </div>
                </div>
            </div>
            <div v-else-if="isValidatingAccount" class="mt-4 p-3 rounded-lg bg-blue-500/20 border border-blue-500/30">
                <div class="flex items-center space-x-2">
                    <svg class="animate-spin h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-300">Validating account...</span>
                </div>
            </div>

            <!-- Footer Elements -->
            <div
                class="flex flex-col gap-4 mt-8 md:flex-row md:items-center md:justify-between"
            >
                <!-- Save ID Checkbox -->
                <div class="flex items-center">
                    <Checkbox v-model:checked="savedIdForFuture" class="" />
                    <label for="saveId" class="ml-2 text-sm text-primary-text">
                        Simpan ID untuk pembelian berikutnya
                    </label>
                </div>

                <!-- Purchase Guide Button -->
                <button
                    type="button"
                    @click="showModal = true"
                    class="flex items-center justify-center gap-2 px-4 py-2 text-white transition-colors rounded-md bg-primary/80 hover:bg-primary"
                >
                    <span>Panduan Pembelian</span>
                </button>
            </div>
        </form>
    </CosmicCard>

    <!-- Cosmic Modal -->
    <Modal :show="showModal" @close="showModal = false" max-width="xl">
        <div
            class="p-4 border rounded-lg md:p-6 bg-gradient-to-br from-primary/90 to-secondary/50 border-secondary/50 text-primary-text"
        >
            <h3 class="mb-4 text-xl font-bold text-center">
                Panduan Pembelian
            </h3>

            <div class="space-y-6">
                <!-- Purchase Guide Image -->
                <div
                    v-if="produk.petunjuk_field"
                    class="max-w-full mx-auto overflow-hidden rounded-lg"
                >
                    <img
                        :src="`/storage/${produk.petunjuk_field}`"
                        alt="Purchase Guide"
                        class="w-full object-contain max-h-[60vh]"
                    />
                </div>

                <!-- Purchase Guide Text -->
                <div
                    v-if="produk.deskripsi_game"
                    class="prose-sm prose md:prose-base max-w-none text-primary-text"
                >
                    <p>{{ produk.deskripsi_game }}</p>
                </div>
            </div>

            <!-- Close Button -->
            <div class="mt-6 text-center">
                <button
                    type="button"
                    @click="showModal = false"
                    class="px-6 py-2 text-white transition-colors rounded-md bg-primary hover:bg-primary-hover"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.cosmic-input-effect {
    transition: all 0.3s ease;
    transform: translateZ(0);
    will-change: transform, box-shadow;
}

.cosmic-input-effect:focus {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(155, 135, 245, 0.15);
}

.cosmic-highlight-pulse {
    position: relative;
}

.cosmic-highlight-pulse::before {
    content: "";
    position: absolute;
    inset: -2px;
    border-radius: 8px;
    background: linear-gradient(
        120deg,
        rgba(155, 135, 245, 0.2),
        rgba(51, 195, 240, 0.3)
    );
    z-index: -1;
    animation: account-highlight-pulse 2s ease-in-out;
}

@keyframes account-highlight-pulse {
    0% {
        opacity: 0;
        transform: scale(0.98);
    }
    25%, 75% {
        opacity: 0.8;
        transform: scale(1.01);
    }
    50% {
        opacity: 0.5;
        transform: scale(1);
    }
    100% {
        opacity: 0;
        transform: scale(0.98);
    }
}
</style>


<script setup>
import { ref, computed, onMounted } from "vue";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";
import CosmicCard from "@/Components/Order/CosmicCard.vue";
import CosmicParticles from "@/Components/User/Flashsale/CosmicParticles.vue";
import { usePersistedAccount } from "@/Composables/usePersistedAccount";

const props = defineProps({
    inputFields: Array,
    produk: Object,
});

const emit = defineEmits(['update:formData', 'saved-data-loaded']);

const showModal = ref(false);
const savedIdForFuture = ref(false);
const formData = ref({});
const { saveAccountData, getAccountData, hasStoredAccount, clearAccountData } = usePersistedAccount();
const hasSavedData = ref(false);

onMounted(() => {
    // Try to load saved account data on component mount
    loadSavedData();
});

const slugify = (text) => {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .trim();
};

const loadSavedData = () => {
    if (props.produk) {
        const slug = slugify(props.produk.nama_produk);
        const savedData = getAccountData(slug);
        
        if (savedData) {
            formData.value = { ...savedData };
            hasSavedData.value = true;
            savedIdForFuture.value = true;
            emit('saved-data-loaded', true);
            
            // Update field values with saved data
            setTimeout(() => {
                props.inputFields.forEach(field => {
                    const fieldElement = document.getElementById(`field_${field.name}`);
                    if (fieldElement && formData.value[field.name]) {
                        fieldElement.value = formData.value[field.name];
                    }
                });
            }, 0);
        }
    }
};

const handleInputChange = (field, event) => {
    formData.value[field.name] = event.target.value;
    emit('update:formData', formData.value);
};

const handleSaveToggle = () => {
    if (props.produk) {
        const slug = slugify(props.produk.nama_produk);
        
        if (savedIdForFuture.value) {
            // Save current form data
            const dataToSave = {};
            props.inputFields.forEach(field => {
                const fieldElement = document.getElementById(`field_${field.name}`);
                if (fieldElement) {
                    dataToSave[field.name] = fieldElement.value;
                }
            });
            
            saveAccountData(slug, dataToSave);
            formData.value = dataToSave;
            hasSavedData.value = true;
        } else {
            // Clear saved data when unchecked
            clearAccountData(slug);
            hasSavedData.value = false;
        }
    }
};

const hasDataSavedIndicator = computed(() => {
    return hasSavedData.value && props.produk;
});
</script>

<template>
    <div class="absolute inset-0 z-0">
        <CosmicParticles />
    </div>
    <CosmicCard :title="'Masukkan Data Akun'" :step-number="1">
        <form class="relative z-10" @submit.prevent>
            <!-- Data Saved Indicator -->
            <div 
                v-if="hasDataSavedIndicator" 
                class="mb-4 p-3 bg-secondary/20 rounded-lg border border-secondary/30 flex items-center space-x-2"
            >
                <div class="flex-shrink-0 w-5 h-5 text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="text-sm text-secondary">
                    Data akun tersimpan dan telah digunakan
                </div>
            </div>

            <!-- Dynamic Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
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
                            :required="field.required"
                            :value="formData[field.name] || ''"
                            @input="handleInputChange(field, $event)"
                            class="w-full rounded-lg outline-none bg-secondary/20 border-secondary placeholder-secondary focus:ring-secondary focus:border-primary/70 focus:bg-secondary/20/90 text-primary-text cosmic-input-effect"
                        />

                        <!-- Select Input -->
                        <select
                            v-else-if="field.type === 'select'"
                            :id="`field_${field.name}`"
                            :name="field.name"
                            :required="field.required"
                            :value="formData[field.name] || ''"
                            @change="handleInputChange(field, $event)"
                            class="w-full rounded-lg bg-secondary/20 border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/50 text-primary-text cosmic-input-effect"
                        >
                            <option value="" disabled selected>
                                Select an option
                            </option>
                            <option
                                v-for="option in field.options"
                                :key="option.id"
                                :value="option.value"
                                :selected="formData[field.name] === option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </template>
            </div>

            <!-- Footer Elements -->
            <div
                class="flex flex-col gap-4 mt-8 md:flex-row md:items-center md:justify-between"
            >
                <!-- Save ID Checkbox -->
                <div class="flex items-center">
                    <Checkbox 
                        v-model:checked="savedIdForFuture" 
                        class="" 
                        @update:checked="handleSaveToggle"
                    />
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
</style>

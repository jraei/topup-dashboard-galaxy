
<template>
    <Modal :show="show" @close="onCancel" max-width="md">
        <div class="p-6 space-y-6 bg-dark-lighter text-white">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-medium text-primary">Order Confirmation</h3>
                <button @click="onCancel" class="text-white hover:text-gray-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Loading state -->
            <div v-if="isLoading" class="flex flex-col items-center justify-center py-8">
                <div class="cosmic-loader mb-4"></div>
                <p>Verifying account details...</p>
            </div>
            
            <!-- Error state -->
            <div v-else-if="error" class="bg-red-900/30 border border-red-800 rounded-lg p-4">
                <p class="text-red-300">{{ error }}</p>
                <button 
                    @click="onCancel" 
                    class="mt-4 w-full bg-red-700 hover:bg-red-800 text-white font-medium py-2 px-4 rounded-lg transition"
                >
                    Go Back
                </button>
            </div>
            
            <!-- Content -->
            <div v-else class="space-y-6">
                <!-- Game details -->
                <div>
                    <h4 class="text-sm text-gray-400 mb-1">Game</h4>
                    <p class="text-lg font-medium">{{ orderData?.game_name }}</p>
                </div>
                
                <!-- Service details -->
                <div>
                    <h4 class="text-sm text-gray-400 mb-1">Service</h4>
                    <p class="text-lg font-medium">{{ orderData?.service_name }}</p>
                </div>
                
                <!-- Account details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm text-gray-400 mb-1">Game ID</h4>
                        <p class="text-lg font-medium">{{ orderData?.input_id }}</p>
                    </div>
                    <div v-if="orderData?.input_zone">
                        <h4 class="text-sm text-gray-400 mb-1">Server/Zone</h4>
                        <p class="text-lg font-medium">{{ orderData?.input_zone }}</p>
                    </div>
                </div>
                
                <!-- Username verification -->
                <div v-if="orderData?.username" class="bg-green-900/30 border border-green-800 rounded-lg p-4">
                    <h4 class="text-sm text-green-400 mb-1">Verified Account</h4>
                    <p class="text-lg font-medium">{{ orderData?.username }}</p>
                </div>
                
                <!-- Payment details -->
                <div class="border-t border-gray-700 pt-4 mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-400">Price:</span>
                        <span v-if="orderData?.discount > 0" class="line-through text-gray-400">
                            Rp {{ formatNumber(orderData?.original_price) }}
                        </span>
                        <span v-else>
                            Rp {{ formatNumber(orderData?.price) }}
                        </span>
                    </div>
                    
                    <div v-if="orderData?.discount > 0" class="flex items-center justify-between mb-2">
                        <span class="text-green-400">Discount:</span>
                        <span class="text-green-400">
                            -Rp {{ formatNumber(orderData?.discount) }}
                        </span>
                    </div>
                    
                    <div v-if="orderData?.payment_method?.fee > 0" class="flex items-center justify-between mb-2">
                        <span class="text-gray-400">{{ orderData?.payment_method?.name }} Fee:</span>
                        <span>
                            +Rp {{ formatNumber(orderData?.payment_method?.fee) }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4">
                        <span class="font-medium text-lg">Total:</span>
                        <span class="font-medium text-lg text-primary">
                            Rp {{ formatNumber(orderData?.price + (orderData?.payment_method?.fee || 0)) }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-gray-400">Payment Method:</span>
                        <span class="font-medium">
                            {{ orderData?.payment_method?.name }}
                        </span>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex space-x-4 pt-4">
                    <button 
                        @click="onCancel" 
                        class="w-1/2 bg-gray-700 hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="onConfirm" 
                        class="w-1/2 bg-primary hover:bg-primary-hover text-white font-medium py-3 px-4 rounded-lg transition"
                        :disabled="isProcessing"
                    >
                        <span v-if="isProcessing">
                            <span class="inline-block cosmic-spinner mr-2"></span>
                            Processing...
                        </span>
                        <span v-else>Confirm Order</span>
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import Modal from '../Modal.vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
    show: Boolean,
    orderData: Object,
    isLoading: Boolean,
    error: String
});

const emit = defineEmits(['cancel', 'confirm']);

const isProcessing = ref(false);

const toast = useToast();

const onCancel = () => {
    if (isProcessing.value) return;
    emit('cancel');
};

const onConfirm = async () => {
    isProcessing.value = true;
    emit('confirm');
};

// Reset processing state when modal is closed
watch(() => props.show, (newVal) => {
    if (!newVal) {
        isProcessing.value = false;
    }
});

// Helper function to format numbers
const formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number);
};
</script>

<style scoped>
.cosmic-loader {
    width: 48px;
    height: 48px;
    border: 5px solid #9b87f5;
    border-bottom-color: transparent;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
}

.cosmic-spinner {
    width: 16px;
    height: 16px;
    border: 3px solid #ffffff;
    border-bottom-color: transparent;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
}

@keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>


<template>
    <Modal
        :show="showModal"
        @close="closeModal"
        max-width="6xl"
        class="w-[90vw] max-w-6xl"
    >
        <div class="p-6 bg-gradient-to-br from-primary/10 to-secondary/5 border border-secondary/20 rounded-lg">
            <!-- Header -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-white">Order Confirmation</h2>
                <p class="text-gray-300">Please review your order details before proceeding</p>
            </div>

            <div v-if="orderData">
                <!-- Account Verification Status -->
                <div class="mb-6">
                    <div v-if="orderData.nickname" class="p-4 mb-4 bg-green-500/20 border border-green-400/40 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <div class="font-semibold text-green-300">Account Verified</div>
                                <div class="text-sm text-green-200">{{ orderData.nickname }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="orderData.validation_error" class="p-4 mb-4 bg-red-500/20 border border-red-400/40 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <div class="font-semibold text-red-300">Verification Error</div>
                                <div class="text-sm text-red-200">{{ orderData.validation_error }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Left Column: Account Info -->
                    <div class="bg-secondary/10 p-4 rounded-lg border border-secondary/20">
                        <h3 class="text-lg font-semibold text-white mb-4">Account Information</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Account ID:</span>
                                <span class="font-medium text-white">{{ orderData.account_id }}</span>
                            </div>
                            
                            <div v-if="orderData.server_id" class="flex justify-between">
                                <span class="text-gray-400">Server/Zone ID:</span>
                                <span class="font-medium text-white">{{ orderData.server_id }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-400">Contact:</span>
                                <span class="font-medium text-white">{{ orderData.contact.phone }}</span>
                            </div>
                            
                            <div v-if="orderData.contact.email" class="flex justify-between">
                                <span class="text-gray-400">Email:</span>
                                <span class="font-medium text-white">{{ orderData.contact.email }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Order Info -->
                    <div class="bg-secondary/10 p-4 rounded-lg border border-secondary/20">
                        <h3 class="text-lg font-semibold text-white mb-4">Order Information</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Service:</span>
                                <span class="font-medium text-white">{{ orderData.layanan }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-400">Quantity:</span>
                                <span class="font-medium text-white">{{ orderData.quantity }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-400">Base Price:</span>
                                <span class="font-medium text-white">Rp {{ formatPrice(orderData.basePrice) }}</span>
                            </div>
                            
                            <div v-if="orderData.discount > 0" class="flex justify-between">
                                <span class="text-gray-400">Discount:</span>
                                <span class="font-medium text-green-400">-Rp {{ formatPrice(orderData.discount) }}</span>
                            </div>
                            
                            <div v-if="orderData.payment_fee > 0" class="flex justify-between">
                                <span class="text-gray-400">Payment Fee:</span>
                                <span class="font-medium text-orange-400">+Rp {{ formatPrice(orderData.payment_fee) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="bg-primary/10 p-4 rounded-lg border border-primary/20 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Payment Method</h3>
                            <p class="text-gray-400">{{ orderData.payment_method }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-400">Total Payment</p>
                            <p class="text-xl font-bold text-primary">Rp {{ formatPrice(orderData.final_price) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Warning Message -->
                <div class="p-4 mb-6 bg-yellow-500/20 border border-yellow-400/40 rounded-lg">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-yellow-300">By proceeding, you agree to our Terms of Service and Privacy Policy.</span>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button 
                        @click="closeModal" 
                        class="px-6 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="confirmOrder"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors focus:ring-2 focus:ring-primary/50 relative overflow-hidden"
                    >
                        <span class="relative z-10">Confirm Order</span>
                        <div class="absolute inset-0 overflow-hidden">
                            <div class="cosmic-btn-particles"></div>
                        </div>
                    </button>
                </div>
            </div>

            <div v-else class="p-8 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary mb-4"></div>
                <p class="text-white">Loading order details...</p>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    showModal: Boolean,
    orderData: Object
});

const emit = defineEmits(['close', 'confirmed']);

const closeModal = () => {
    emit('close');
};

const confirmOrder = () => {
    emit('confirmed', props.orderData);
};

const formatPrice = (price) => {
    if (!price) return '0';
    const roundedPrice = Math.ceil(price);
    return roundedPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};
</script>

<style scoped>
.cosmic-btn-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle, rgba(51, 195, 240, 0.2) 1px, transparent 1px),
        radial-gradient(circle, rgba(155, 135, 245, 0.2) 1px, transparent 1px);
    background-size: 12px 12px, 16px 16px;
    background-position: 0 0, 6px 6px;
    opacity: 0.3;
    animation: particle-drift 8s infinite linear;
}

@keyframes particle-drift {
    0% {
        background-position: 0 0, 6px 6px;
    }
    100% {
        background-position: 100px 0, 106px 6px;
    }
}
</style>


<template>
  <Modal :show="showModal" @close="closeModal" max-width="6xl" class="w-[90vw] md:w-auto">
    <div class="relative p-5 border rounded-lg md:p-6 bg-gradient-to-br from-primary/90 to-secondary/50 border-secondary/50 text-primary-text">
      <!-- Cosmic particles background -->
      <div class="absolute inset-0 z-0 overflow-hidden rounded-lg opacity-20">
        <div class="cosmic-stars"></div>
        <div class="cosmic-nebula"></div>
      </div>

      <!-- Title -->
      <div class="relative z-10">
        <h3 class="mb-6 text-xl font-bold text-center text-white">Konfirmasi Pesanan</h3>

        <div v-if="isLoading" class="flex flex-col items-center justify-center py-10">
          <div class="w-16 h-16 mb-4 border-4 rounded-full border-secondary/30 border-t-secondary animate-spin"></div>
          <p class="text-center text-white">{{ loadingMessage || 'Memvalidasi akun game...' }}</p>
        </div>

        <div v-else-if="validationError" class="p-4 mb-6 border rounded-lg bg-red-500/20 border-red-500/50">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 pt-0.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <p class="font-medium text-white">Validation Error</p>
              <p class="text-sm text-gray-300">{{ validationError }}</p>
            </div>
          </div>
        </div>

        <div v-else class="space-y-6">
          <!-- Account Information -->
          <div class="p-4 border rounded-lg bg-primary/10 border-secondary/20">
            <h4 class="mb-3 font-medium text-secondary">Game Account Details</h4>
            
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div v-for="(value, key) in accountData" :key="key" class="flex flex-col">
                <span class="text-xs text-gray-400">{{ formatLabel(key) }}:</span>
                <span class="text-sm font-medium text-white">{{ value }}</span>
              </div>
              
              <div v-if="accountUsername" class="flex flex-col">
                <span class="text-xs text-gray-400">Username:</span>
                <span class="text-sm font-medium text-white">{{ accountUsername }}</span>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="p-4 border rounded-lg bg-primary/10 border-secondary/20">
            <h4 class="mb-3 font-medium text-secondary">Order Details</h4>

            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400">Service:</span>
                <span class="text-sm font-semibold text-white">{{ orderData.serviceName }}</span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400">Quantity:</span>
                <span class="text-sm font-semibold text-white">{{ orderData.quantity }}x</span>
              </div>

              <div v-if="orderData.voucherDiscount" class="flex items-center justify-between">
                <span class="text-xs text-gray-400">Discount:</span>
                <span class="text-sm font-semibold text-green-400">-Rp {{ formatPrice(orderData.voucherDiscount) }}</span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400">Payment Method:</span>
                <span class="text-sm font-semibold text-white">{{ orderData.paymentMethodName }}</span>
              </div>

              <div v-if="orderData.paymentFee > 0" class="flex items-center justify-between">
                <span class="text-xs text-gray-400">Payment Fee:</span>
                <span class="text-sm font-semibold text-orange-400">+Rp {{ formatPrice(orderData.paymentFee) }}</span>
              </div>

              <div class="flex items-center justify-between pt-2 mt-2 border-t border-gray-700">
                <span class="text-sm font-medium text-gray-300">Total:</span>
                <span class="text-lg font-bold text-primary">Rp {{ formatPrice(orderData.finalPrice) }}</span>
              </div>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="p-4 border rounded-lg bg-primary/10 border-secondary/20">
            <h4 class="mb-3 font-medium text-secondary">Contact Information</h4>

            <div class="space-y-2">
              <div v-if="orderData.contactEmail" class="flex items-center justify-between">
                <span class="text-xs text-gray-400">Email:</span>
                <span class="text-sm font-semibold text-white">{{ orderData.contactEmail }}</span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400">WhatsApp:</span>
                <span class="text-sm font-semibold text-white">
                  +{{ orderData.countryCode || '62' }} {{ orderData.contactPhone }}
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col gap-3 mt-6 md:flex-row md:justify-end">
            <button 
              type="button" 
              @click="closeModal"
              class="px-6 py-3 text-white transition-colors rounded-lg bg-gray-700 hover:bg-gray-600"
            >
              Cancel
            </button>
            <button 
              type="button" 
              @click="handleConfirm"
              class="px-6 py-3 text-white transition-colors rounded-lg bg-primary hover:bg-primary-hover"
            >
              Confirm Order
            </button>
          </div>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import Modal from "@/Components/Modal.vue";
import axios from 'axios';

const props = defineProps({
  showModal: Boolean,
  orderData: Object
});

const emit = defineEmits(['close', 'confirmed']);

const isLoading = ref(true);
const loadingMessage = ref('Validating account...');
const validationError = ref(null);
const accountUsername = ref(null);
const accountData = ref({});

watch(() => props.showModal, (newVal) => {
  if (newVal && props.orderData) {
    resetModal();
    validateAccount();
  }
});

watch(() => props.orderData, (newVal) => {
  if (newVal && props.showModal) {
    resetModal();
    validateAccount();
  }
});

const resetModal = () => {
  isLoading.value = true;
  loadingMessage.value = 'Validating account...';
  validationError.value = null;
  accountUsername.value = null;
  accountData.value = {};
};

const validateAccount = async () => {
  if (!props.orderData) return;
  
  // Extract account data
  Object.keys(props.orderData.accountData || {}).forEach(key => {
    accountData.value[key] = props.orderData.accountData[key];
  });
  
  try {
    isLoading.value = true;
    loadingMessage.value = 'Validating game account...';

    // Call API endpoint to validate account username
    if (props.orderData.checkUsername) {
      const response = await axios.post('/api/order/check-username', {
        game: props.orderData.validationId,
        user_id: props.orderData.accountData?.user_id,
        zone_id: props.orderData.accountData?.zone_id || props.orderData.accountData?.server_id
      });

      if (response.data && response.data.status === 'success') {
        accountUsername.value = response.data.username;
      } else {
        validationError.value = response.data?.message || 'Failed to validate username';
      }
    }

    await nextTick();
    isLoading.value = false;
  } catch (error) {
    console.error('Account validation error:', error);
    validationError.value = error.response?.data?.message || 'An error occurred during validation';
    isLoading.value = false;
  }
};

const closeModal = () => {
  emit('close');
};

const handleConfirm = () => {
  // If there's a validation error but we're allowing to proceed anyway
  if (validationError.value && !confirm('There was a validation error with your account. Are you sure you want to proceed?')) {
    return;
  }
  
  emit('confirmed', {
    ...props.orderData,
    username: accountUsername.value
  });
};

const formatLabel = (key) => {
  return key
    .replace(/_/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase());
};

const formatPrice = (price) => {
  let priceRounded = Math.ceil(price);
  return priceRounded.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};
</script>

<style scoped>
.cosmic-stars {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(white 1px, transparent 1px);
  background-size: 50px 50px;
  opacity: 0.3;
}

.cosmic-nebula {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 30% 50%, rgba(155, 135, 245, 0.3) 0%, transparent 70%),
             radial-gradient(circle at 70% 50%, rgba(51, 195, 240, 0.3) 0%, transparent 70%);
  opacity: 0.6;
  filter: blur(30px);
}
</style>

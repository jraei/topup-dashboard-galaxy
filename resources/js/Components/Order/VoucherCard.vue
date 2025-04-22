
<template>
  <div class="cosmic-card bg-content-background border border-primary/30 rounded-lg overflow-hidden shadow-lg">
    <!-- Card Header -->
    <div class="px-4 py-3 bg-header-background border-b border-primary/30 flex items-center">
      <div class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="text-primary h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 12.5L9 10.5" />
          <path d="M15 12.5L15 10.5" />
          <path d="M10.5 15.5C11.3284 15.5 12 14.8284 12 14C12 13.1716 11.3284 12.5 10.5 12.5C9.67157 12.5 9 13.1716 9 14C9 14.8284 9.67157 15.5 10.5 15.5Z" />
          <path d="M13.5 9.5C14.3284 9.5 15 8.82843 15 8C15 7.17157 14.3284 6.5 13.5 6.5C12.6716 6.5 12 7.17157 12 8C12 8.82843 12.6716 9.5 13.5 9.5Z" />
          <path d="M12 2L2 7L12 12L22 7L12 2Z" />
          <path d="M2 17L12 22L22 17" />
          <path d="M2 12L12 17L22 12" />
        </svg>
        <h3 class="text-lg font-medium text-secondary">Promo Code</h3>
      </div>
    </div>

    <!-- Card Body -->
    <div class="p-4 space-y-3">
      <!-- Voucher Input Group -->
      <div class="flex items-center space-x-2">
        <div class="relative flex-grow">
          <input
            type="text"
            v-model="voucherCode"
            placeholder="Enter voucher code"
            class="w-full px-4 py-2 bg-header-background text-secondary border border-primary/30 rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all duration-200"
            :class="{ 'border-red-500': validationError }"
          />
          <!-- Animated particles for valid voucher -->
          <div v-if="validVoucher" class="absolute inset-0 pointer-events-none overflow-hidden">
            <div v-for="i in 5" :key="i" class="absolute w-1 h-1 rounded-full bg-primary animate-particle" :style="particleStyle(i)"></div>
          </div>
          <!-- Meteor effect for invalid voucher -->
          <div v-if="invalidVoucher" class="absolute top-1/2 right-0 w-12 h-px bg-gradient-to-l from-red-500 to-transparent meteor-burn transform -translate-y-1/2"></div>
        </div>
        <button 
          @click="applyVoucher" 
          class="px-4 py-2 bg-primary text-primary-text rounded-md hover:bg-primary-hover transition duration-200 shadow-cosmic"
          :disabled="isApplyingVoucher"
        >
          <span v-if="isApplyingVoucher">...</span>
          <span v-else>Apply</span>
        </button>
      </div>

      <!-- Validation Error Message -->
      <div v-if="validationError" class="text-sm text-red-500 animate-fade-in">
        {{ validationError }}
      </div>

      <!-- Available Promos Button -->
      <button 
        @click="showAvailableVouchers" 
        class="w-full px-4 py-2 bg-header-background border border-primary/30 rounded-md hover:bg-content-background/80 hover:border-primary/50 transition duration-200 text-secondary"
      >
        Available Promos
      </button>
    </div>
  </div>

  <!-- Voucher Modal -->
  <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm transition-opacity" @click="closeModal">
    <div class="cosmic-modal relative w-full max-w-3xl max-h-[80vh] bg-content-background border border-primary/30 rounded-lg shadow-cosmic overflow-hidden transition transform" @click.stop>
      
      <!-- Modal Header -->
      <div class="px-4 py-3 bg-header-background border-b border-primary/30 flex items-center justify-between">
        <h3 class="text-lg font-medium text-secondary">Available Promo Codes</h3>
        <button @click="closeModal" class="text-secondary hover:text-primary transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6L6 18" />
            <path d="M6 6L18 18" />
          </svg>
        </button>
      </div>
      
      <!-- Modal Body -->
      <div class="p-4 overflow-y-auto max-h-[calc(80vh-4rem)]">
        <!-- No vouchers message -->
        <p v-if="!vouchers.length" class="text-center py-6 text-secondary">No promo codes available at the moment.</p>
        
        <!-- Vouchers Grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="voucher in vouchers"
            :key="voucher.code"
            class="voucher-card relative overflow-hidden rounded-lg border transition-all duration-300"
            :class="isVoucherActive(voucher) ? 'border-primary/50 shadow-cosmic-sm' : 'border-gray-700 opacity-75'"
          >
            <!-- Card Content -->
            <div class="p-4 space-y-2 bg-header-background">
              <!-- Discount Value (Largest Text) -->
              <div class="text-xl font-bold text-secondary">
                <span v-if="voucher.discount_type === 'percentage'">{{ voucher.discount_value }}% Off</span>
                <span v-else>Rp {{ formatNumber(voucher.discount_value) }} Off</span>
              </div>
              
              <!-- Code -->
              <div class="bg-content-background/50 px-2 py-1 rounded inline-block text-sm font-mono text-primary">
                {{ voucher.code }}
              </div>
              
              <!-- Conditions -->
              <div class="space-y-1 text-sm">
                <div v-if="voucher.end_date" class="flex items-center space-x-2 text-secondary">
                  <span class="w-4 inline-block">⌚</span>
                  <span>Valid until {{ voucher.end_date }}</span>
                </div>
                <div v-if="voucher.max_discount" class="flex items-center space-x-2 text-secondary">
                  <span class="w-4 inline-block">⌚</span>
                  <span>Max discount: Rp {{ formatNumber(voucher.max_discount) }}</span>
                </div>
                <div v-if="voucher.min_purchase" class="flex items-center space-x-2 text-secondary">
                  <span class="w-4 inline-block">⌚</span>
                  <span>Min purchase: Rp {{ formatNumber(voucher.min_purchase) }}</span>
                </div>
                <div v-if="voucher.usage_limit" class="flex items-center space-x-2 text-secondary">
                  <span class="w-4 inline-block">⌚</span>
                  <span>Usage: {{ voucher.usage_count || 0 }}/{{ voucher.usage_limit }}</span>
                </div>
              </div>
              
              <!-- Status Badge -->
              <div v-if="!isVoucherActive(voucher)" class="absolute top-2 right-2 bg-red-500/80 text-white text-xs px-2 py-1 rounded-full">
                {{ getVoucherStatus(voucher) }}
              </div>
            </div>
            
            <!-- Card Footer -->
            <div class="p-3 bg-header-background/70 border-t border-primary/20 flex justify-end">
              <button
                @click="selectVoucher(voucher)"
                class="px-3 py-1 text-sm rounded-md transition-all duration-200"
                :class="isVoucherActive(voucher) ? 'bg-primary hover:bg-primary-hover text-primary-text' : 'bg-gray-700 text-gray-400 cursor-not-allowed'"
                :disabled="!isVoucherActive(voucher)"
              >
                Use This
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  currentTotal: {
    type: Number,
    required: true
  },
  vouchers: {
    type: Array,
    default: () => []
  }
});

// Voucher state
const voucherCode = ref('');
const validationError = ref('');
const isApplyingVoucher = ref(false);
const validVoucher = ref(false);
const invalidVoucher = ref(false);
const modalOpen = ref(false);

// Format number with thousand separator
const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number);
};

// Check if voucher is active (usable)
const isVoucherActive = (voucher) => {
  // Usage limit check
  if (voucher.usage_limit && voucher.usage_count >= voucher.usage_limit) {
    return false;
  }
  
  // Min purchase check
  if (voucher.min_purchase && props.currentTotal < voucher.min_purchase) {
    return false;
  }
  
  return true;
};

// Get voucher status message
const getVoucherStatus = (voucher) => {
  if (voucher.usage_limit && voucher.usage_count >= voucher.usage_limit) {
    return 'LIMIT REACHED';
  }
  
  if (voucher.min_purchase && props.currentTotal < voucher.min_purchase) {
    return 'MIN PURCHASE';
  }
  
  return 'UNAVAILABLE';
};

// Apply voucher
const applyVoucher = async () => {
  if (!voucherCode.value) {
    validationError.value = 'Please enter a voucher code';
    showInvalidAnimation();
    return;
  }
  
  isApplyingVoucher.value = true;
  validationError.value = '';
  
  try {
    // Check if voucher exists in available vouchers
    const selectedVoucher = props.vouchers.find(v => v.code === voucherCode.value);
    
    if (!selectedVoucher) {
      validationError.value = 'Invalid voucher code';
      showInvalidAnimation();
      return;
    }
    
    if (!isVoucherActive(selectedVoucher)) {
      validationError.value = getVoucherStatus(selectedVoucher);
      showInvalidAnimation();
      return;
    }
    
    // Apply voucher via Inertia
    router.post(route('voucher.apply'), {
      code: voucherCode.value
    }, {
      preserveScroll: true,
      onSuccess: () => {
        showValidAnimation();
      },
      onError: (errors) => {
        validationError.value = errors.code || 'Failed to apply voucher';
        showInvalidAnimation();
      }
    });
    
  } catch (error) {
    validationError.value = 'An error occurred';
    showInvalidAnimation();
  } finally {
    isApplyingVoucher.value = false;
  }
};

// Show voucher modal
const showAvailableVouchers = () => {
  modalOpen.value = true;
  document.body.classList.add('overflow-hidden');
};

// Close voucher modal
const closeModal = () => {
  modalOpen.value = false;
  document.body.classList.remove('overflow-hidden');
};

// Select voucher from modal
const selectVoucher = (voucher) => {
  voucherCode.value = voucher.code;
  closeModal();
  applyVoucher();
};

// Animation for valid voucher
const showValidAnimation = () => {
  validVoucher.value = true;
  setTimeout(() => {
    validVoucher.value = false;
  }, 1500);
};

// Animation for invalid voucher
const showInvalidAnimation = () => {
  invalidVoucher.value = true;
  setTimeout(() => {
    invalidVoucher.value = false;
  }, 1500);
};

// Generate random particle style
const particleStyle = (index) => {
  const randomX = Math.floor(Math.random() * 100);
  const randomY = Math.floor(Math.random() * 100);
  const randomDelay = Math.random() * 0.5;
  
  return {
    left: `${randomX}%`,
    top: `${randomY}%`,
    animationDelay: `${randomDelay}s`,
  };
};
</script>

<style scoped>
.shadow-cosmic {
  box-shadow: 0 0 15px rgba(155, 135, 245, 0.15);
}

.shadow-cosmic-sm {
  box-shadow: 0 0 10px rgba(155, 135, 245, 0.1);
}

.voucher-card.border-primary\/50 {
  background: linear-gradient(to bottom right, rgba(155, 135, 245, 0.05), rgba(155, 135, 245, 0.01));
}

@keyframes particle {
  0% {
    transform: scale(0);
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
  100% {
    transform: scale(8);
    opacity: 0;
  }
}

.animate-particle {
  animation: particle 1s ease-out forwards;
}

@keyframes meteor {
  0% {
    width: 0;
    opacity: 1;
  }
  100% {
    width: 100%;
    opacity: 0;
  }
}

.meteor-burn {
  animation: meteor 0.6s ease-out forwards;
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-out forwards;
}
</style>


<template>
    <div class="min-h-screen bg-dark-default">
        <!-- Navigation -->
        <CosmicNavbar :user="user" />

        <!-- Product Banner -->
        <ProductBanner :produk="produk" />

        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left column - Service selection -->
                <div class="lg:col-span-2">
                    <!-- Product Info Panel -->
                    <ProductInfoPanel :produk="produk" />

                    <!-- Service Selection -->
                    <ServiceList
                        :services="layanans"
                        :flashsaleItems="flashsaleItems"
                        :selected-service="selectedService"
                        @select="handleServiceSelection"
                    />

                    <!-- Product Description -->
                    <ProductDescription :product="produk" />

                    <!-- FAQs Section -->
                    <FaqSection :faqs="faqs" />
                </div>

                <!-- Right Column - User inputs and checkout -->
                <div>
                    <!-- User Data Input Form -->
                    <UserDataCard
                        :input-fields="inputFields"
                        :selected-service="selectedService"
                        v-model:user-inputs="userInputs"
                        @validate-input="validateUserInput"
                    />

                    <!-- Voucher Section -->
                    <VoucherSection
                        :vouchers="activeVouchers"
                        :selected-service="selectedService"
                        @apply-voucher="applyVoucher"
                        @remove-voucher="removeVoucher"
                        v-model:voucher="voucher"
                    />

                    <!-- Payment Selection -->
                    <PaymentSelector
                        :static-methods="staticMethods"
                        :dynamic-methods="dynamicMethods"
                        :selected-service="selectedService"
                        :voucher-discount="voucherDiscount"
                        v-model:selected-payment="selectedPayment"
                    />

                    <!-- Order Summary & Button -->
                    <CheckoutSummary
                        :selected-service="selectedService"
                        :selected-payment="selectedPayment"
                        :voucher-discount="voucherDiscount"
                        :input-valid="isInputValid"
                        @process-order="processOrder"
                    />

                    <!-- Help Contact -->
                    <HelpContact :wa-number="waNumber" />
                </div>
            </div>
        </div>

        <!-- Order Confirmation Modal -->
        <OrderConfirmationModal
            :show="showConfirmationModal"
            :order-data="confirmationData"
            :is-loading="isConfirmationLoading"
            :error="confirmationError"
            @cancel="cancelOrder"
            @confirm="confirmOrder"
        />

        <!-- Footer -->
        <CosmicFooter />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useToast } from '@/Composables/useToast';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// Components
import CosmicNavbar from "@/Components/User/Navigation/CosmicNavbar.vue";
import ProductBanner from "@/Components/Order/ProductBanner.vue";
import ProductInfoPanel from "@/Components/Order/ProductInfoPanel.vue";
import ServiceList from "@/Components/Order/ServiceList.vue";
import UserDataCard from "@/Components/Order/UserDataCard.vue";
import VoucherSection from "@/Components/Order/VoucherSection.vue";
import PaymentSelector from "@/Components/Order/PaymentSelector.vue";
import CheckoutSummary from "@/Components/Order/CheckoutSummary.vue";
import HelpContact from "@/Components/Order/HelpContact.vue";
import ProductDescription from "@/Components/Order/ProductDescription.vue";
import FaqSection from "@/Components/Order/FaqSection.vue";
import CosmicFooter from "@/Components/User/Footer/CosmicFooter.vue";
import OrderConfirmationModal from "@/Components/Order/OrderConfirmationModal.vue";

// Props
const props = defineProps({
    user: Object,
    produk: Object,
    layanans: Array,
    flashsaleItems: Array,
    inputFields: Array,
    waNumber: String,
    staticMethods: Object,
    dynamicMethods: Object,
    activeVouchers: Array,
    faqs: Array,
});

// Toast notifications
const toast = useToast();

// Reactive state
const selectedService = ref(null);
const userInputs = ref({});
const selectedPayment = ref(null);
const voucher = ref(null);
const voucherDiscount = ref(0);
const isInputValid = ref(false);

// Order confirmation modal
const showConfirmationModal = ref(false);
const confirmationData = ref(null);
const isConfirmationLoading = ref(false);
const confirmationError = ref(null);

// Service selection handler
const handleServiceSelection = (service) => {
    selectedService.value = service;
    
    // Reset voucher when changing service
    if (voucher.value) {
        voucher.value = null;
        voucherDiscount.value = 0;
    }
};

// Apply voucher
const applyVoucher = async (code) => {
    if (!selectedService.value) {
        toast.error('Please select a service first');
        return;
    }

    try {
        const response = await axios.post(route('vouchers.validate'), {
            code,
            layanan_id: selectedService.value.id
        });

        if (response.data.status === 'success') {
            voucher.value = response.data.voucher;
            voucherDiscount.value = response.data.voucher.discount_amount;
            toast.success('Voucher applied successfully');
        }
    } catch (error) {
        console.error('Voucher validation failed:', error);
        const message = error.response?.data?.message || 'Failed to validate voucher';
        toast.error(message);
        voucher.value = null;
        voucherDiscount.value = 0;
    }
};

// Remove voucher
const removeVoucher = () => {
    voucher.value = null;
    voucherDiscount.value = 0;
};

// Input validation
const validateUserInput = (isValid) => {
    isInputValid.value = isValid;
};

// Process order - Phase 1: Validate and show confirmation
const processOrder = async () => {
    if (!selectedService.value) {
        toast.error('Please select a service');
        return;
    }

    if (!isInputValid.value) {
        toast.error('Please fill in all required fields correctly');
        return;
    }

    if (!selectedPayment.value) {
        toast.error('Please select a payment method');
        return;
    }

    // Prepare form data
    const formData = {
        layanan_id: selectedService.value.id,
        input_id: userInputs.value.user_id || userInputs.value.account_id || '',
        input_zone: userInputs.value.zone_id || userInputs.value.server_id || '',
        payment_method: selectedPayment.value.id || selectedPayment.value.type,
        voucher_code: voucher.value?.code || null,
    };

    // Reset confirmation data
    confirmationError.value = null;
    confirmationData.value = null;
    isConfirmationLoading.value = true;
    showConfirmationModal.value = true;

    try {
        // Call validate order endpoint
        const response = await axios.post(route('order.validate'), formData);
        
        if (response.data.status === 'success') {
            confirmationData.value = response.data.order_data;
        } else {
            confirmationError.value = response.data.message || 'Failed to validate order';
        }
    } catch (error) {
        console.error('Order validation failed:', error);
        confirmationError.value = error.response?.data?.message || 'An error occurred while validating your order';
    } finally {
        isConfirmationLoading.value = false;
    }
};

// Cancel order confirmation
const cancelOrder = () => {
    showConfirmationModal.value = false;
};

// Confirm order - Phase 2: Process payment
const confirmOrder = async () => {
    try {
        // Call process order endpoint
        const response = await axios.post(route('order.process'));
        
        if (response.data.status === 'success') {
            toast.success(response.data.message || 'Order processed successfully');
            
            // Close modal
            showConfirmationModal.value = false;
            
            // Redirect if needed
            if (response.data.redirect) {
                router.visit(response.data.redirect);
            }
        } else {
            toast.error(response.data.message || 'Failed to process order');
            showConfirmationModal.value = false;
        }
    } catch (error) {
        console.error('Order processing failed:', error);
        toast.error(error.response?.data?.message || 'An error occurred while processing your order');
        showConfirmationModal.value = false;
    }
};

// Set default payment method on mount
onMounted(() => {
    if (props.staticMethods?.saldo && props.user) {
        selectedPayment.value = {
            type: 'saldo',
            name: props.staticMethods.saldo.nama || 'Account Balance',
            id: 'saldo'
        };
    }

    // Select first service if available
    if (props.layanans && props.layanans.length > 0) {
        selectedService.value = props.layanans[0];
    } else if (props.flashsaleItems && props.flashsaleItems.length > 0) {
        selectedService.value = props.flashsaleItems[0];
    }

    // Initialize user inputs based on input fields
    if (props.inputFields) {
        props.inputFields.forEach(field => {
            userInputs.value[field.field_key] = '';
        });
    }
});

// Watch for login state changes
watch(() => props.user, (newUser) => {
    if (newUser && !selectedPayment.value && props.staticMethods?.saldo) {
        selectedPayment.value = {
            type: 'saldo',
            name: props.staticMethods.saldo.nama || 'Account Balance',
            id: 'saldo'
        };
    }
}, { immediate: true });
</script>

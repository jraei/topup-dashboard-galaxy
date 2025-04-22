
<template>
    <div
        class="p-4 rounded-2xl bg-dark-card/40 backdrop-blur-sm border-t border-secondary/20 shadow-lg"
    >
        <h3
            class="mb-3 text-xl font-semibold text-white"
        >
            Order Summary
        </h3>

        <div v-if="selectedService">
            <!-- Service selection -->
            <div
                class="flex items-center justify-between p-3 mb-3 bg-primary/10 rounded-lg border border-primary/20"
            >
                <div>
                    <h4 class="font-medium text-white">
                        {{ selectedService.nama_layanan }}
                    </h4>
                    <p class="text-sm text-gray-300">
                        {{ quantity }} x
                        {{ formattedPrice(selectedService.harga_jual) }}
                    </p>
                </div>
                <div class="text-lg font-bold text-white">
                    {{ formattedPrice(basePrice) }}
                </div>
            </div>

            <!-- Payment method fee -->
            <div
                v-if="paymentInfo"
                class="flex items-center justify-between p-3 mb-3 bg-primary/5 rounded-lg"
            >
                <div>
                    <h4 class="font-medium text-white">
                        {{ paymentInfo.methodLabel }} Fee
                    </h4>
                    <p
                        v-if="paymentInfo.feeType === 'percentage'"
                        class="text-sm text-gray-300"
                    >
                        {{ paymentInfo.fee }}% of order total
                    </p>
                </div>
                <div class="text-lg text-white">
                    {{ formattedPrice(paymentFee) }}
                </div>
            </div>
            
            <!-- Voucher discount -->
            <div
                v-if="voucher"
                class="flex items-center justify-between p-3 mb-3 bg-secondary/10 rounded-lg"
            >
                <div>
                    <h4 class="font-medium text-white">
                        Promo: {{ voucher.code }}
                    </h4>
                    <p class="text-sm text-secondary">
                        <span v-if="voucher.discount_type === 'fixed'">
                            {{ formattedPrice(voucher.discount_value) }} off
                        </span>
                        <span v-else>
                            {{ voucher.discount_value }}% off (Max: {{ formattedPrice(voucher.max_discount || basePrice) }})
                        </span>
                    </p>
                </div>
                <div class="text-lg text-secondary">
                    -{{ formattedPrice(voucherDiscount) }}
                </div>
            </div>

            <!-- Total -->
            <div
                class="flex items-center justify-between p-3 mb-5 bg-secondary/10 rounded-lg"
            >
                <h4 class="font-bold text-white">Total Payment</h4>
                <div class="text-xl font-bold text-white">
                    {{ formattedPrice(totalPrice) }}
                </div>
            </div>

            <!-- Order button -->
            <button
                class="w-full py-3 text-white bg-primary rounded-lg focus:outline-none hover:bg-primary/80 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :disabled="!canSubmit"
                @click="$emit('checkout')"
            >
                <span v-if="canSubmit">Process Order</span>
                <span v-else>
                    {{ submitMessage }}
                </span>
            </button>
        </div>
        <div v-else class="space-y-4 text-center py-4">
            <div
                class="animate-pulse p-6 rounded-lg bg-primary/5 space-y-4"
            >
                <div class="h-5 bg-primary/10 rounded w-3/4 mx-auto"></div>
                <div class="h-8 bg-primary/10 rounded w-1/2 mx-auto"></div>
            </div>
            <p class="text-gray-500">
                Please select a service to continue
            </p>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    produk: Object,
    minPrice: [Number, String],
    selectedService: Object,
    quantity: {
        type: Number,
        default: 1,
    },
    selectedPayment: [Object, null],
    paymentInfo: Object,
    contact: Object,
    voucher: Object,
});

defineEmits(["checkout"]);

const basePrice = computed(() => {
    if (!props.selectedService) return 0;
    return props.selectedService.harga_jual * props.quantity;
});

// Calculate voucher discount
const voucherDiscount = computed(() => {
    if (!props.voucher || !props.selectedService) return 0;
    
    let discount = 0;
    
    // Fixed amount discount
    if (props.voucher.discount_type === 'fixed') {
        discount = props.voucher.discount_value;
    }
    // Percentage discount
    else {
        discount = (basePrice.value * props.voucher.discount_value) / 100;
        
        // Apply max discount cap if exists
        if (props.voucher.max_discount && discount > props.voucher.max_discount) {
            discount = props.voucher.max_discount;
        }
    }
    
    // Don't allow discount to exceed the base price
    return Math.min(discount, basePrice.value);
});

const paymentFee = computed(() => {
    if (!props.paymentInfo || !props.selectedService) return 0;

    const price = basePrice.value - voucherDiscount.value;

    if (props.paymentInfo.feeType === "fixed") {
        return props.paymentInfo.fee;
    } else {
        return (price * props.paymentInfo.fee) / 100;
    }
});

const totalPrice = computed(() => {
    if (!props.selectedService) return 0;
    return basePrice.value + paymentFee.value - voucherDiscount.value;
});

const canSubmit = computed(() => {
    if (!props.selectedService) return false;
    if (!props.selectedPayment) return false;
    if (!props.contact?.phone) return false;
    return true;
});

const submitMessage = computed(() => {
    if (!props.selectedService) return "Select a service";
    if (!props.selectedPayment) return "Select payment method";
    if (!props.contact?.phone) return "Enter contact info";
    return "Process Order";
});

const formattedPrice = (price) => {
    if (price === 0 || price === undefined || price === null)
        return "Rp 0";
    return `Rp ${price.toLocaleString()}`;
};
</script>

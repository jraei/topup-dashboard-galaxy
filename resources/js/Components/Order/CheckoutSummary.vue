<template>
    <div
        class="p-4 border-t shadow-lg rounded-2xl bg-dark-card/40 backdrop-blur-sm border-secondary/20"
    >
        <h3 class="mb-3 text-xl font-semibold text-white">Order Summary</h3>

        <div v-if="selectedService">
            <!-- Service selection -->
            <div
                class="flex items-center justify-between p-3 mb-3 border rounded-lg bg-primary/10 border-primary/20"
            >
                <div>
                    <!-- product thumbnail flex-->
                    <div class="flex items-center">
                        <img
                            :src="'/storage/' + produk.thumbnail"
                            :alt="selectedService.nama_layanan"
                            class="w-12 h-12 mr-3 rounded-lg"
                        />
                        <div>
                            <h4 class="font-medium text-white">
                                {{ selectedService.nama_layanan }}
                            </h4>
                            <p class="text-sm text-gray-300">
                                {{ quantity }} x
                                {{ formattedPrice(selectedService.harga_jual) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-lg font-bold text-white">
                    {{ formattedPrice(basePrice) }}
                </div>
            </div>

            <!-- Payment method fee -->
            <div
                v-if="paymentInfo"
                class="flex items-center justify-between p-3 mb-3 rounded-lg bg-primary/5"
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
                class="flex items-center justify-between p-3 mb-3 rounded-lg bg-secondary/10"
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
                            {{ voucher.discount_value }}% off (Max:
                            {{
                                formattedPrice(
                                    voucher.max_discount || basePrice
                                )
                            }})
                        </span>
                    </p>
                </div>
                <div class="text-lg text-secondary">
                    -{{ formattedPrice(voucherDiscount) }}
                </div>
            </div>

            <!-- Total -->
            <div
                class="flex items-center justify-between p-3 mb-5 rounded-lg bg-secondary/10"
            >
                <h4 class="font-bold text-white">Total Payment</h4>
                <div class="text-xl font-bold text-white">
                    {{ formattedPrice(totalPrice) }}
                </div>
            </div>

            <!-- Order button -->
            <button
                class="w-full py-3 text-white transition-colors rounded-lg bg-primary focus:outline-none hover:bg-primary/80 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="!canSubmit"
                @click="$emit('checkout')"
            >
                <span v-if="canSubmit">Process Order</span>
                <span v-else>
                    {{ submitMessage }}
                </span>
            </button>
        </div>
        <div v-else class="py-4 space-y-4 text-center">
            <div class="p-6 space-y-4 rounded-lg animate-pulse bg-primary/5">
                <div class="w-3/4 h-5 mx-auto rounded bg-primary/10"></div>
                <div class="w-1/2 h-8 mx-auto rounded bg-primary/10"></div>
            </div>
            <p class="text-gray-500">Please select a service to continue</p>
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
    if (props.voucher.discount_type === "fixed") {
        discount = props.voucher.discount_value;
    }
    // Percentage discount
    else {
        discount = (basePrice.value * props.voucher.discount_value) / 100;

        // Apply max discount cap if exists
        if (
            props.voucher.max_discount &&
            discount > props.voucher.max_discount
        ) {
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
    if (price === 0 || price === undefined || price === null) return "Rp 0";
    return `Rp ${price.toLocaleString()}`;
};
</script>

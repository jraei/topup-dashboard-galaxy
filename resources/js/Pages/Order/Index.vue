
<script setup>
import { ref, computed, onMounted, watch } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import CosmicParticles from "@/Components/User/Flashsale/CosmicParticles.vue";
import ProductBanner from "@/Components/Order/ProductBanner.vue";
import ProductInfoPanel from "@/Components/Order/ProductInfoPanel.vue";
import UserDataCard from "@/Components/Order/UserDataCard.vue";
import ServiceList from "@/Components/Order/ServiceList.vue";
import QuantitySelector from "@/Components/Order/QuantitySelector.vue";
import CheckoutSummary from "@/Components/Order/CheckoutSummary.vue";
import HelpContact from "@/Components/Order/HelpContact.vue";
import PaymentSelector from "@/Components/Order/PaymentSelector.vue";
import ContactForm from "@/Components/Order/ContactForm.vue";
import VoucherSection from "@/Components/Order/VoucherSection.vue";
import ProductDescription from "@/Components/Order/ProductDescription.vue";
import FaqSection from "@/Components/Order/FaqSection.vue";
import OrderConfirmationModal from "@/Components/Order/OrderConfirmationModal.vue";
import { useToast } from "@/Composables/useToast";
import { usePersistedAccount } from "@/Composables/usePersistedAccount";
import axios from "axios";

const props = defineProps({
    produk: Object,
    layanans: Array,
    flashsaleItems: Array,
    user: Object,
    inputFields: Array,
    waNumber: String,
    flashsaleEvents: Array,
    staticMethods: Array,
    dynamicMethods: Array,
    activeVouchers: Array,
    faqs: Array,
});

// Centralized state management
const selectedService = ref(null);
const quantity = ref(1);
const paymentInfo = ref(null);
const selectedPayment = ref(null);
const contactData = ref({ email: "", phone: "", country: "ID", countryCode: "62" });
const selectedVoucher = ref(null);
const formData = ref({});
const { toast } = useToast();

// For OrderConfirmationModal
const showConfirmationModal = ref(false);
const confirmationData = ref(null);
const isProcessingOrder = ref(false);

// UI related refs
const sidebarRef = ref(null);
const orderQuantityRef = ref(null);
const contactSectionRef = ref(null);
const paymentSectionHighlight = ref(false);
const hasSavedData = ref(false);

const { saveAccountData, getAccountData } = usePersistedAccount();

// When form data is updated from UserDataCard
const handleFormDataUpdate = (data) => {
    formData.value = { ...data };
};

// When saved data is loaded in UserDataCard
const handleSavedDataLoaded = (loaded) => {
    hasSavedData.value = loaded;
};

// Total amount calculation for voucher validation
const totalAmount = computed(() => {
    if (!selectedService.value) return 0;

    const baseAmount = selectedService.value.harga_jual * quantity.value;
    return baseAmount;
});

const handleServiceSelection = (service) => {
    selectedService.value = service;

    // Auto-scroll to payment section with delay to ensure rendering
    nextTick(() => {
        setTimeout(() => {
            scrollToPaymentSection();
        }, 100);
    });
};

// Scroll to payment section function
const scrollToPaymentSection = () => {
    if (!orderQuantityRef.value) return;

    const isInView = isElementInViewport(orderQuantityRef.value);
    if (isInView) return; // Don't scroll if already in view

    // Smooth scroll to payment section with offset
    const offset = 150; // 32px above the component
    const top =
        orderQuantityRef.value.getBoundingClientRect().top +
        window.pageYOffset -
        offset;

    window.scrollTo({
        top,
        behavior: "smooth",
    });

    // Highlight payment section with pulsing glow
    highlightPaymentSection();
};

// Check if element is in viewport
const isElementInViewport = (el) => {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <=
            (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <=
            (window.innerWidth || document.documentElement.clientWidth)
    );
};

// Highlight payment section with pulsing glow
const highlightPaymentSection = () => {
    if (!orderQuantityRef.value) return;

    paymentSectionHighlight.value = true;

    // Remove highlight after animation (3 pulses ≈ 1500ms)
    setTimeout(() => {
        paymentSectionHighlight.value = false;
    }, 1500);
};

const handleQuantityUpdate = (newQuantity) => {
    quantity.value = newQuantity;
};

const handlePaymentChange = (pay) => {
    selectedPayment.value = pay;
};

const handleFeeChange = (info) => {
    paymentInfo.value = info;
};

const handleContactUpdate = (contact) => {
    contactData.value = contact;
};

const handleVoucherUpdate = (voucher) => {
    selectedVoucher.value = voucher;
};

// Process order button from CheckoutSummary
const handleProcessOrder = () => {
    // Validation checks
    if (!selectedService.value) {
        toast.error("Please select a service first");
        return;
    }
    if (!selectedPayment.value) {
        toast.error("Please select a payment method");
        return;
    }
    if (!contactData.value.phone || contactData.value.phone.length < 7) {
        toast.error("Please enter a valid WhatsApp number");
        return;
    }
    if (!formData.value || Object.keys(formData.value).length === 0) {
        toast.error("Please enter account information");
        return;
    }

    // Prepare order data for confirmation
    const paymentMethodName = paymentInfo.value?.methodLabel || "Unknown payment method";
    const basePrice = selectedService.value.flashSaleItem?.is_active 
        ? selectedService.value.flashSaleItem.harga_flashsale 
        : selectedService.value.harga_jual;
    
    const voucherDiscount = calculateVoucherDiscount();
    const paymentFee = calculatePaymentFee(basePrice * quantity.value - voucherDiscount);
    const finalPrice = (basePrice * quantity.value) - voucherDiscount + paymentFee;

    // Save account data if we have form data
    if (props.produk && formData.value) {
        const slug = slugify(props.produk.nama_produk);
        saveAccountData(slug, formData.value);
    }

    confirmationData.value = {
        serviceName: selectedService.value.nama_layanan,
        serviceId: selectedService.value.id,
        quantity: quantity.value,
        basePrice: basePrice,
        voucherDiscount: voucherDiscount,
        paymentMethodName: paymentMethodName,
        paymentMethod: selectedPayment.value,
        paymentFee: paymentFee,
        finalPrice: finalPrice,
        contactEmail: contactData.value.email,
        contactPhone: contactData.value.phone,
        countryCode: contactData.value.countryCode,
        accountData: formData.value,
        voucher: selectedVoucher.value?.code,
        checkUsername: props.produk.validasi_id !== 'tidak',
        validationId: props.produk.validasi_id
    };

    showConfirmationModal.value = true;
};

// Utility function to slugify text
const slugify = (text) => {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .trim();
};

// Calculate voucher discount
const calculateVoucherDiscount = () => {
    if (!selectedVoucher.value || !selectedService.value) return 0;

    const basePrice = selectedService.value.flashSaleItem?.is_active 
        ? selectedService.value.flashSaleItem.harga_flashsale 
        : selectedService.value.harga_jual;
    
    const totalBase = basePrice * quantity.value;
    let discount = 0;

    if (selectedVoucher.value.discount_type === 'fixed') {
        discount = selectedVoucher.value.discount_value;
    } else {
        discount = (totalBase * selectedVoucher.value.discount_value) / 100;

        if (selectedVoucher.value.max_discount && discount > selectedVoucher.value.max_discount) {
            discount = selectedVoucher.value.max_discount;
        }
    }

    return Math.min(discount, totalBase);
};

// Calculate payment fee
const calculatePaymentFee = (amount) => {
    if (!paymentInfo.value) return 0;

    const {
        fee_fixed = 0,
        fee_percent = 0,
        feeType = "none",
    } = paymentInfo.value;

    let fee = 0;

    if (feeType === "fixed") {
        fee = fee_fixed;
    } else if (feeType === "percent") {
        fee = amount * (fee_percent / 100);
    } else if (feeType === "mixed") {
        fee = fee_fixed + amount * (fee_percent / 100);
    }

    return Math.ceil(fee);
};

// Handle confirmation from modal
const handleOrderConfirmed = async (confirmedData) => {
    if (isProcessingOrder.value) return;
    isProcessingOrder.value = true;

    try {
        // Phase 1: Order confirmation
        const confirmResponse = await axios.post('/api/order/confirm', {
            layanan_id: confirmedData.serviceId,
            input_id: confirmedData.accountData.user_id,
            input_zone: confirmedData.accountData.zone_id || confirmedData.accountData.server_id,
            quantity: confirmedData.quantity,
            payment_method: confirmedData.paymentMethod,
            email: confirmedData.contactEmail,
            phone: confirmedData.contactPhone,
            country_code: confirmedData.countryCode,
            voucher_code: confirmedData.voucher,
        });

        if (confirmResponse.data.status === 'success') {
            // Phase 2: Process order
            const processResponse = await axios.post('/api/order/process', {
                layanan_id: confirmedData.serviceId,
                input_id: confirmedData.accountData.user_id,
                input_zone: confirmedData.accountData.zone_id || confirmedData.accountData.server_id,
                nickname: confirmedData.username || '',
                quantity: confirmedData.quantity,
                payment_method: confirmedData.paymentMethod,
                email: confirmedData.contactEmail,
                phone: confirmedData.contactPhone,
                country_code: confirmedData.countryCode,
                voucher_code: confirmedData.voucher,
            });

            if (processResponse.data.status === 'success') {
                toast.success('Order placed successfully!');
                
                // Handle redirect if needed
                if (processResponse.data.redirect && processResponse.data.payment_url) {
                    window.location.href = processResponse.data.payment_url;
                    return;
                }
                
                // Reset form after successful submission
                resetForm();
            } else {
                toast.error(processResponse.data.message || 'Error processing your order');
            }
        } else {
            toast.error(confirmResponse.data.message || 'Error confirming your order');
        }
    } catch (error) {
        console.error('Order processing error:', error);
        toast.error(error.response?.data?.message || 'Error processing your order');
    } finally {
        isProcessingOrder.value = false;
        showConfirmationModal.value = false;
    }
};

// Reset form after successful order
const resetForm = () => {
    selectedService.value = null;
    quantity.value = 1;
    selectedPayment.value = null;
    paymentInfo.value = null;
    selectedVoucher.value = null;
    contactData.value = { email: "", phone: "", country: "ID", countryCode: "62" };
    formData.value = {};
    confirmationData.value = null;
};

onMounted(() => {
    initPriceAnimations();
});

const initPriceAnimations = () => {
    const animateDigits = (element, targetValue) => {
        if (!element || !targetValue) return;

        const targetString = targetValue.toString();
        const digitContainers = [];

        element.textContent = "";

        for (let i = 0; i < targetString.length; i++) {
            const digitContainer = document.createElement("span");
            digitContainer.className =
                "inline-block overflow-hidden relative w-[0.6em] h-[1em]";

            const digit = document.createElement("span");
            digit.className =
                "absolute transition-transform duration-800 ease-out-back";
            digit.textContent = targetString[i];

            digit.style.transform = "translateY(-1000%)";

            digitContainer.appendChild(digit);
            element.appendChild(digitContainer);

            digitContainers.push({
                container: digitContainer,
                digit: digit,
                targetValue: targetString[i],
            });
        }

        digitContainers.forEach((container, index) => {
            setTimeout(() => {
                container.digit.style.transform = "translateY(0)";
            }, index * 80);
        });
    };

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === "childList") {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        const priceElements =
                            node.querySelectorAll(".flashsale-price");
                        let animationCount = 0;
                        priceElements.forEach((el) => {
                            if (animationCount < 3) {
                                const value = el.dataset.value;
                                if (value) {
                                    animateDigits(el, value);
                                    animationCount++;
                                }
                            }
                        });
                    }
                });
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    let animationCount = 0;
    document.querySelectorAll(".flashsale-price").forEach((el) => {
        if (animationCount < 3) {
            const value = el.dataset.value;
            if (value) {
                animateDigits(el, value);
                animationCount++;
            }
        }
    });
};
</script>

<template>
    <GuestLayout>
        <section>
            <div class="relative">
                <ProductBanner :banner="produk.banner" />
            </div>

            <ProductInfoPanel :produk="produk" :min-price="0" />
        </section>

        <section class="relative px-4 py-8 md:pt-20 bg-content_background">
            <div class="absolute inset-0 z-0">
                <CosmicParticles />
            </div>
            <div
                class="relative z-10 grid grid-cols-1 mx-auto max-w-7xl lg:grid-cols-6 lg:gap-6"
            >
                <div class="lg:col-span-4 lg:pr-8">
                    <UserDataCard
                        :input-fields="inputFields"
                        :produk="produk"
                        @update:formData="handleFormDataUpdate"
                        @saved-data-loaded="handleSavedDataLoaded"
                    />
                    <ServiceList
                        :services="layanans"
                        :flashsale-items="flashsaleItems"
                        :flashsale-events="flashsaleEvents"
                        @select-service="handleServiceSelection"
                    />
                    <div
                        ref="orderQuantityRef"
                        :class="{
                            'cosmic-highlight-pulse': paymentSectionHighlight,
                        }"
                    >
                        <QuantitySelector
                            :disabled="!selectedService"
                            :max-quantity="1000"
                            :initial-quantity="quantity"
                            @update:quantity="handleQuantityUpdate"
                        />
                    </div>
                    <!-- Payment section with ref for scrolling and highlighting -->

                    <PaymentSelector
                        :static-methods="staticMethods"
                        :dynamic-methods="dynamicMethods"
                        :selected-service="selectedService"
                        :selected-payment="selectedPayment"
                        :base-price="
                            selectedService ? selectedService.harga_jual : 0
                        "
                        :quantity="quantity"
                        :voucher="selectedVoucher"
                        @update:selectedPayment="handlePaymentChange"
                        @update:fee="handleFeeChange"
                    />

                    <VoucherSection
                        :active-vouchers="activeVouchers"
                        :total-amount="totalAmount"
                        :selected-service="selectedService"
                        @update:voucher="handleVoucherUpdate"
                    />
                    <div ref="contactSectionRef">
                        <ContactForm
                            :initial-email="contactData.email"
                            :initial-phone="contactData.phone"
                            :initial-country="contactData.country"
                            @update:contact="handleContactUpdate"
                        />
                    </div>
                </div>

                <div class="space-y-4 lg:col-span-2">
                    <div
                        ref="sidebarRef"
                        class="sticky space-y-4 transition-all duration-300 top-36"
                    >
                        <HelpContact :wa-number="waNumber" />
                        <CheckoutSummary
                            :produk="produk"
                            min-price="0"
                            :selected-service="selectedService"
                            :quantity="quantity"
                            :selected-payment="selectedPayment"
                            :payment-info="paymentInfo"
                            :contact="contactData"
                            :voucher="selectedVoucher"
                            :input-form-data="formData"
                            @process-order="handleProcessOrder"
                        />
                    </div>
                </div>
            </div>

            <!-- Product Description Section - Full Width -->
            <div class="relative z-10 mx-auto mt-8 max-w-7xl">
                <ProductDescription :description="produk.deskripsi_game" />

                <FaqSection :faqs="faqs" />
            </div>
        </section>

        <!-- Order Confirmation Modal -->
        <OrderConfirmationModal
            :show-modal="showConfirmationModal"
            :order-data="confirmationData"
            @close="showConfirmationModal = false"
            @confirmed="handleOrderConfirmed"
        />
    </GuestLayout>
</template>

<style scoped>
.cosmic-sticky {
    transition: all 0.3s ease;
    will-change: transform;
    transform: translateZ(0);
}

.cosmic-sticky::before {
    content: "";
    position: absolute;
    inset: -5px;
    border-radius: 12px;
    background: linear-gradient(
        120deg,
        rgba(51, 195, 240, 0.1),
        rgba(155, 135, 245, 0.1)
    );
    opacity: 0;
    z-index: -1;
    transition: opacity 0.3s ease;
    animation: cosmic-warp 10s infinite alternate;
}

.cosmic-sticky:hover::before {
    opacity: 1;
}

.cosmic-sticky::after {
    content: "";
    position: absolute;
    right: -15px;
    top: 0;
    bottom: 0;
    width: 4px;
    background: radial-gradient(
        circle,
        rgba(51, 195, 240, 0.3) 0%,
        transparent 70%
    );
    opacity: 0.2;
    z-index: -1;
    animation: micro-planet-trail 5s infinite ease-in-out;
}

/* New highlight pulse animation for payment section */
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
    animation: payment-highlight-pulse 1.5s ease-in-out;
}

@keyframes payment-highlight-pulse {
    0%,
    100% {
        opacity: 0;
        transform: scale(0.98);
    }
    25%,
    75% {
        opacity: 0.8;
        transform: scale(1.01);
    }
    50% {
        opacity: 0.5;
        transform: scale(1);
    }
}

@keyframes digit-shuffle {
    0% {
        transform: translateY(-1000%);
    }
    70% {
        transform: translateY(10%);
    }
    85% {
        transform: translateY(-5%);
    }
    100% {
        transform: translateY(0);
    }
}

@keyframes cosmic-warp {
    0% {
        background-position: 0% 0%;
    }
    100% {
        background-position: 100% 100%;
    }
}

@keyframes micro-planet-trail {
    0%,
    100% {
        opacity: 0.2;
        height: 30%;
    }
    50% {
        opacity: 0.5;
        height: 80%;
    }
}

.ease-out-back {
    transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>

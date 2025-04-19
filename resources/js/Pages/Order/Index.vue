<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import CosmicParticles from "@/Components/User/Flashsale/CosmicParticles.vue";
import ProductBanner from "@/Components/Order/ProductBanner.vue";
import ProductInfoPanel from "@/Components/Order/ProductInfoPanel.vue";
import UserDataCard from "@/Components/Order/UserDataCard.vue";
import ServiceList from "@/Components/Order/ServiceList.vue";
import QuantitySelector from "@/Components/Order/QuantitySelector.vue";
import CheckoutSummary from "@/Components/Order/CheckoutSummary.vue";
import HelpContact from "@/Components/Order/HelpContact.vue";
import CosmicPaymentSelector from "@/Components/Order/CosmicPaymentSelector.vue";
import CosmicContactForm from "@/Components/Order/CosmicContactForm.vue";
import { useToast } from "@/Composables/useToast";

const props = defineProps({
    produk: Object,
    layanans: Array,
    flashsaleItems: Array,
    user: Object,
    inputFields: Array,
    waNumber: String,
    flashsaleEvents: Array,
    staticMethods: Object,
    dynamicMethods: Object,
});

// Initialize reactive state
const selectedService = ref(null);
const quantity = ref(1);
const { toast } = useToast();
const sidebarRef = ref(null);
const isSidebarSticky = ref(false);
const footerVisible = ref(false);
const paymentMethods = ref({
    static: {
        saldo: props.staticMethods?.saldo || {},
        qris: props.staticMethods?.qris || {},
    },
    dynamic: props.dynamicMethods || {},
});
const selectedPayment = ref({ type: "static", code: "saldo", channelId: null });
const paymentFee = ref({ fee: 0, feeType: "fixed", amount: props.produk?.harga_min || 0 });

// Contact section fields
const contactEmail = ref("");
const contactPhone = ref("");

// Handle service selection
const handleServiceSelection = (service) => {
    selectedService.value = service;
    // Quantity is preserved when switching services
};

// Handle quantity update
const handleQuantityUpdate = (newQuantity) => {
    quantity.value = newQuantity;
};

// Handle checkout
const handleCheckout = () => {
    if (!selectedService.value) {
        toast.error("Please select a service first");
        return;
    }

    // TODO: Implement checkout functionality
    toast.success(
        `Processing order for ${quantity.value} x ${selectedService.value.nama_layanan}`
    );
};

// Create an improved scroll handler with IntersectionObserver
const setupStickyObserver = () => {
    if (!sidebarRef.value) return;
    
    // Footer observer to detect when footer is visible
    const footerElement = document.querySelector('footer');
    if (footerElement) {
        const footerObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    footerVisible.value = entry.isIntersecting;
                    // Update sticky state when footer visibility changes
                    updateStickyState();
                });
            },
            { threshold: 0.1 }
        );
        footerObserver.observe(footerElement);
    }
    
    // Initial state check
    updateStickyState();
};

// Function to check if screen is large enough for sticky behavior
const isLargeScreen = () => {
    return window.innerWidth >= 1024; // LG breakpoint
};

// Update sticky state based on scroll position and viewport size
const updateStickyState = () => {
    if (!sidebarRef.value || !isLargeScreen()) {
        isSidebarSticky.value = false;
        return;
    }
    
    const navbarHeight = 64; // Approximate height of the navbar
    const sidebarRect = sidebarRef.value.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    
    // Only make sidebar sticky if:
    // 1. It's at or above the navbar position
    // 2. Footer is not visible
    // 3. Sidebar bottom is within viewport
    if (sidebarRect.top <= navbarHeight + 10 && 
        !footerVisible.value &&
        sidebarRect.bottom <= viewportHeight) {
        
        if (!isSidebarSticky.value) {
            isSidebarSticky.value = true;
        }
    } else {
        if (isSidebarSticky.value) {
            isSidebarSticky.value = false;
        }
    }
};

// Debounce function for improved performance
const debounce = (fn, delay) => {
    let timer = null;
    return function (...args) {
        if (timer) clearTimeout(timer);
        timer = setTimeout(() => {
            fn(...args);
        }, delay);
    };
};

// Debounced scroll handler
const debouncedScroll = debounce(updateStickyState, 100);

// Set up and clean up event listeners
onMounted(() => {
    // Set up scroll event listener
    window.addEventListener("scroll", debouncedScroll, { passive: true });
    
    // Set up resize listener to handle responsive changes
    window.addEventListener("resize", debouncedScroll, { passive: true });
    
    // Setup IntersectionObserver
    nextTick(() => {
        setupStickyObserver();
    });

    // Initialize casino-style price animations
    initPriceAnimations();
});

onUnmounted(() => {
    window.removeEventListener("scroll", debouncedScroll);
    window.removeEventListener("resize", debouncedScroll);
});

// Casino-style price animation
const initPriceAnimations = () => {
    const animateDigits = (element, targetValue) => {
        if (!element || !targetValue) return;
        
        // Convert targetValue to string
        const targetString = targetValue.toString();
        // Create temporary divs for each digit
        const digitContainers = [];
        
        // Clear the element
        element.textContent = "";
        
        // Create a container for each digit
        for (let i = 0; i < targetString.length; i++) {
            const digitContainer = document.createElement("span");
            digitContainer.className = "inline-block overflow-hidden relative w-[0.6em] h-[1em]";
            
            const digit = document.createElement("span");
            digit.className = "absolute transition-transform duration-800 ease-out-back";
            digit.textContent = targetString[i];
            
            // Start from a random position
            digit.style.transform = "translateY(-1000%)";
            
            // Append digit to container
            digitContainer.appendChild(digit);
            
            // Append container to element
            element.appendChild(digitContainer);
            
            // Store for animation
            digitContainers.push({
                container: digitContainer,
                digit: digit,
                targetValue: targetString[i],
            });
        }
        
        // Animate each digit with slight delay between them
        digitContainers.forEach((container, index) => {
            setTimeout(() => {
                container.digit.style.transform = "translateY(0)";
            }, index * 80); // Stagger the animations, reduced from 100 to 80ms for faster animation
        });
    };
    
    // Observer to watch for newly added price elements
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === "childList") {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) { // Element node
                        // Look for price elements
                        const priceElements = node.querySelectorAll(".flashsale-price");
                        let animationCount = 0;
                        priceElements.forEach((el) => {
                            // Limit concurrent animations to 3
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
    
    // Start observing
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Initial run for existing elements
    let animationCount = 0;
    document.querySelectorAll(".flashsale-price").forEach((el) => {
        // Limit concurrent animations to 3
        if (animationCount < 3) {
            const value = el.dataset.value;
            if (value) {
                animateDigits(el, value);
                animationCount++;
            }
        }
    });
};

// Payment selection triggers fee/summary update
function onPaymentSelected(value) {
    selectedPayment.value = value;
}
function onFeeUpdate(payload) {
    paymentFee.value = payload;
    // Update CheckoutSummary if necessary
}
</script>

<template>
    <GuestLayout>
        <!-- Product Information Section -->
        <section class="relative">
            <div class="relative w-full overflow-hidden">
                <!-- Banner -->
                <ProductBanner :banner="produk.banner" />
            </div>

            <!-- Cosmic Product Panel -->
            <ProductInfoPanel :produk="produk" :min-price="0" />
        </section>

        <!-- User Data Section -->
        <section
            class="relative px-4 py-8 overflow-hidden bg-content_background"
        >
            <div class="absolute inset-0 z-0">
                <CosmicParticles />
            </div>

            <!-- Two-column layout on MD+ screens -->
            <div
                class="relative z-10 grid grid-cols-1 mx-auto max-w-7xl lg:grid-cols-6 lg:gap-6"
            >
                <!-- Main column (80%) -->
                <div class="lg:col-span-4 lg:pr-8">
                    <!-- User Data Card -->
                    <UserDataCard
                        :input-fields="inputFields"
                        :produk="produk"
                    />

                    <!-- Service Selection -->
                    <ServiceList
                        :services="layanans"
                        :flashsale-items="flashsaleItems"
                        :flashsale-events="flashsaleEvents"
                        @select-service="handleServiceSelection"
                    />

                    <!-- Purchase Quantity -->
                    <QuantitySelector
                        :disabled="!selectedService"
                        :max-quantity="1000"
                        :initial-quantity="quantity"
                        @update:quantity="handleQuantityUpdate"
                    />

                    <!-- ADD Cosmic Payment Selector -->
                    <CosmicPaymentSelector
                        :static-methods="paymentMethods.static"
                        :dynamic-groups="paymentMethods.dynamic"
                        :base-price="produk?.harga_min || 0"
                        :model-value="selectedPayment"
                        @update:modelValue="onPaymentSelected"
                        @update:fee="onFeeUpdate"
                        class="mb-8"
                    />

                    <!-- ADD Cosmic Contact Form -->
                    <CosmicContactForm
                        v-model:email="contactEmail"
                        v-model:phone="contactPhone"
                    />
                </div>

                <!-- Sidebar column (20%) -->
                <div class="space-y-4 lg:col-span-2">
                    <div
                        ref="sidebarRef"
                        :class="[
                            'space-y-4 transition-all duration-300',
                            {
                                'lg:sticky lg:top-[74px] cosmic-sticky': isSidebarSticky,
                            },
                        ]"
                    >
                        <HelpContact :wa-number="waNumber" />
                        <CheckoutSummary
                            :produk="produk"
                            min-price="0"
                            :selected-service="selectedService"
                            :quantity="quantity"
                            :payment-fee="paymentFee"
                            :selected-payment="selectedPayment"
                            :contact-info="{ email: contactEmail, phone: contactPhone }"
                            @checkout="handleCheckout"
                        />
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
.cosmic-sticky {
    transition: all 0.3s ease;
    will-change: transform;
    transform: translateZ(0); /* Enable GPU acceleration */
}

/* Warp effect for sticky state change */
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

/* Micro-planet trail for sticky sidebar */
.cosmic-sticky::after {
    content: "";
    position: absolute;
    right: -15px;
    top: 0;
    bottom: 0;
    width: 4px;
    background: radial-gradient(circle, rgba(51, 195, 240, 0.3) 0%, transparent 70%);
    opacity: 0.2;
    z-index: -1;
    animation: micro-planet-trail 5s infinite ease-in-out;
}

/* CSS for casino-style price animations */
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
    0%, 100% {
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

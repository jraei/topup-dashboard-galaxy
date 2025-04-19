<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import CosmicParticles from "@/Components/User/Flashsale/CosmicParticles.vue";
import ProductBanner from "@/Components/Order/ProductBanner.vue";
import ProductInfoPanel from "@/Components/Order/ProductInfoPanel.vue";
import UserDataCard from "@/Components/Order/UserDataCard.vue";
import ServiceList from "@/Components/Order/ServiceList.vue";
import QuantitySelector from "@/Components/Order/QuantitySelector.vue";
import CheckoutSummary from "@/Components/Order/CheckoutSummary.vue";
import HelpContact from "@/Components/Order/HelpContact.vue";
import { useToast } from "@/Composables/useToast";

const props = defineProps({
    produk: Object,
    layanans: Array,
    flashsaleItems: Array,
    user: Object,
    inputFields: Array,
    waNumber: String,
    flashsaleEvents: Array,
});

// Initialize reactive state
const selectedService = ref(null);
const quantity = ref(1);
const { toast } = useToast();
const sidebarRef = ref(null);
const isSidebarSticky = ref(false);

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

// Debounce function for scroll events
const debounce = (fn, delay) => {
    let timer = null;
    return function (...args) {
        if (timer) clearTimeout(timer);
        timer = setTimeout(() => {
            fn(...args);
        }, delay);
    };
};

// Handle scroll for sticky sidebar with performance optimizations
const handleScroll = () => {
    if (!sidebarRef.value) return;

    const navbarHeight = 64; // Approximate height of the navbar
    const sidebarRect = sidebarRef.value.getBoundingClientRect();
    const viewportHeight = window.innerHeight;

    // Check if sidebar should be sticky
    if (sidebarRect.top <= navbarHeight + 10) {
        // Only update state if it's changed to avoid unnecessary renders
        if (!isSidebarSticky.value) {
            isSidebarSticky.value = true;
        }
    } else if (isSidebarSticky.value) {
        isSidebarSticky.value = false;
    }

    // Check if bottom of sidebar is visible
    if (sidebarRect.bottom >= viewportHeight && isSidebarSticky.value) {
        isSidebarSticky.value = false;
    }
};

const debouncedScroll = debounce(handleScroll, 16); // ~60fps

// Set up and clean up scroll event listener
onMounted(() => {
    window.addEventListener("scroll", debouncedScroll, { passive: true });
    // Initial check
    handleScroll();

    // Initialize casino-style price animations
    initPriceAnimations();
});

onUnmounted(() => {
    window.removeEventListener("scroll", debouncedScroll);
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
            digitContainer.className =
                "inline-block overflow-hidden relative w-[0.6em] h-[1em]";

            const digit = document.createElement("span");
            digit.className =
                "absolute transition-transform duration-800 ease-out-back";
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
            }, index * 100); // Stagger the animations
        });
    };

    // Observer to watch for newly added price elements
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === "childList") {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        // Element node
                        // Look for price elements
                        const priceElements =
                            node.querySelectorAll(".flashsale-price");
                        priceElements.forEach((el) => {
                            const value = el.dataset.value;
                            if (value) {
                                animateDigits(el, value);
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
    document.querySelectorAll(".flashsale-price").forEach((el) => {
        const value = el.dataset.value;
        if (value) {
            animateDigits(el, value);
        }
    });
};
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
                </div>

                <!-- Sidebar column (20%) -->
                <div class="space-y-4 lg:col-span-2">
                    <div
                        ref="sidebarRef"
                        :class="[
                            'space-y-4',
                            {
                                'md:sticky md:top-[74px] cosmic-sticky':
                                    isSidebarSticky,
                            },
                        ]"
                    >
                        <HelpContact :wa-number="waNumber" />
                        <CheckoutSummary
                            :produk="produk"
                            min-price="0"
                            :selected-service="selectedService"
                            :quantity="quantity"
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
}

.cosmic-sticky:hover::before {
    opacity: 1;
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

.ease-out-back {
    transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>

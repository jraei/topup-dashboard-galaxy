
<script setup>
import { computed, ref, onMounted } from "vue";

const props = defineProps({
    flashItem: {
        type: Object,
        required: true,
    },
});

const layanan = computed(() => props.flashItem.layanan);
const produk = computed(() => layanan.value.produk);
const cardRef = ref(null);

// Calculate discount percentage
const discountPercentage = computed(() => {
    const original = layanan.value.harga_jual;
    const sale = props.flashItem.harga_flashsale;

    if (!original || !sale || original <= 0) return 0;

    const percentage = Math.round(((original - sale) / original) * 100);
    return percentage;
});

// Format price with IDR
const formatPrice = (price) => {
    if (!price) return "Rp 0";

    // Bulatkan ke angka terdekat
    const roundedPrice = Math.round(price);

    // Format dengan titik sebagai pemisah ribuan
    const formatted = roundedPrice
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    return "Rp " + formatted;
};

// Calculate stock percentage
const stockPercentage = computed(() => {
    // If no stock tracking, return 100%
    if (props.flashItem.stok_tersedia === null) return 100;

    const total =
        props.flashItem.stok_tersedia + (props.flashItem.stok_terjual || 0);

    // Prevent division by zero
    if (total <= 0) return 0;

    const percentage = (props.flashItem.stok_tersedia / total) * 100;

    return Math.max(0, Math.min(100, percentage)); // Clamp between 0-100
});

// Get thumbnail image
const thumbnailImage = computed(() => {
    if (
        layanan.value &&
        layanan.value.gambar &&
        layanan.value.gambar.length > 0
    ) {
        return layanan.value.gambar;
    }
    return null;
});

// Check if stock is low (less than 20%)
const isStockLow = computed(() => {
    return stockPercentage.value < 20;
});

// Generate random parameters for cosmic elements
const cosmicElements = ref([]);
const generateCosmicElements = () => {
    const elements = [];
    // Generate 3-5 planets
    for (let i = 0; i < 3 + Math.floor(Math.random() * 3); i++) {
        elements.push({
            type: "planet",
            size: 8 + Math.floor(Math.random() * 12), // 8px to 20px
            top: Math.random() * 70 + 10,
            right: Math.random() * 60 + 5,
            rotation: Math.random() * 360,
            orbitSpeed: 5 + Math.random() * 15, // 5s to 20s
            delay: Math.random() * 3,
        });
    }

    // Generate 2-3 stars
    for (let i = 0; i < 2 + Math.floor(Math.random() * 2); i++) {
        elements.push({
            type: "pulsar",
            size: 3 + Math.floor(Math.random() * 5), // 3px to 8px
            top: Math.random() * 80 + 5,
            right: Math.random() * 80 + 10,
            pulseSpeed: 1.5 + Math.random() * 2, // 1.5s to 3.5s
            delay: Math.random() * 2,
        });
    }

    // Generate quantum waves
    elements.push({
        type: "quantum",
        height: 50 + Math.random() * 30,
        right: 5 + Math.random() * 15,
        top: 50 + Math.random() * 30,
        waveSpeed: 4 + Math.random() * 8,
    });

    return elements;
};

onMounted(() => {
    cosmicElements.value = generateCosmicElements();

    // Apply parallax effect on desktop
    if (window.innerWidth >= 768) {
        const handleMouseMove = (e) => {
            if (!cardRef.value) return;

            const rect = cardRef.value.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const moveX = (x - centerX) / 20;
            const moveY = (y - centerY) / 20;

            const cosmicLayer = cardRef.value.querySelector(".cosmic-layer");
            if (cosmicLayer) {
                cosmicLayer.style.transform = `translate(${moveX}px, ${moveY}px)`;
            }
        };

        cardRef.value.addEventListener("mousemove", handleMouseMove);
        cardRef.value.addEventListener("mouseleave", () => {
            const cosmicLayer = cardRef.value.querySelector(".cosmic-layer");
            if (cosmicLayer) {
                cosmicLayer.style.transform = "translate(0, 0)";
            }
        });
    }
});
</script>

<template>
    <div 
        ref="cardRef" 
        class="relative overflow-hidden transition-all duration-300 border rounded-lg border-opacity-10 border-primary bg-[rgba(33,92,187,0.6)] aspect-[5/3] h-60 max-w-full transform-gpu will-change-transform backface-hidden shadow-lg hover:shadow-glow-primary hover:translate-y-[-5px] hover:scale-[1.02]"
    >
        <!-- User Limit Badge -->
        <div v-if="flashItem.batas_user" class="absolute z-20 top-2 right-2">
            <div
                class="px-2 py-1 text-xs text-white border rounded-full bg-primary border-primary"
            >
                <span class="flex items-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-3 h-3 mr-1"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    Max. {{ flashItem.batas_user }}/user
                </span>
            </div>
        </div>

        <!-- Card Body - New Layout -->
        <div class="flex h-4/5 w-full relative overflow-hidden">
            <!-- Left Section -->
            <div class="w-[65%] p-3 flex flex-col z-10">
                <!-- Product Image -->
                <div class="relative w-20 h-20 mb-3 overflow-hidden bg-transparent">
                    <img
                        v-if="thumbnailImage"
                        :src="'/storage/' + thumbnailImage"
                        alt=""
                        loading="lazy"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 opacity-0 transition-opacity duration-300 shadow-[0_0_10px_rgba(155,135,245,0.7)] group-hover:opacity-100"></div>
                </div>

                <!-- Product Info -->
                <div>
                    <h3 class="text-base font-bold text-secondary transition-all duration-300 group-hover:text-shadow-neon group-hover:scale-[1.02] mb-1">
                        {{ layanan.nama_layanan }}
                    </h3>
                    <p class="text-xs text-[#cbd5e1] mb-2">{{ produk.nama }}</p>
                </div>

                <!-- Price Section -->
                <div class="mt-auto">
                    <div class="flex items-center text-lg font-bold text-secondary animate-price-pulse">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 mr-1 text-secondary"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                        </svg>
                        {{ formatPrice(flashItem.harga_flashsale) }}
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-[#94a3b8] line-through opacity-70">{{ formatPrice(layanan.harga_jual) }}</span>
                        <span
                            v-if="discountPercentage > 0"
                            class="bg-primary text-white text-[0.65rem] px-1.5 py-0.5 rounded"
                        >
                            -{{ discountPercentage }}%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Section - Cosmic Elements -->
            <div class="w-[35%] relative overflow-hidden">
                <div class="cosmic-layer absolute inset-0 transition-transform duration-300">
                    <!-- Dynamic cosmic elements -->
                    <template
                        v-for="(element, index) in cosmicElements"
                        :key="index"
                    >
                        <!-- Planet -->
                        <div
                            v-if="element.type === 'planet'"
                            class="absolute rounded-full bg-[radial-gradient(circle_at_30%_30%,rgba(255,255,255,0.4),rgba(51,195,240,0.2)_50%,transparent)] shadow-[inset_-2px_-2px_4px_rgba(0,0,0,0.5)]"
                            :style="{
                                width: `${element.size}px`,
                                height: `${element.size}px`,
                                top: `${element.top}%`,
                                right: `${element.right}%`,
                                animationDuration: `${element.orbitSpeed}s`,
                                animationDelay: `${element.delay}s`,
                                animation: `orbit-rotation ${element.orbitSpeed}s linear infinite`,
                                willChange: 'transform',
                                transform: 'translateZ(0)',
                            }"
                        >
                            <div
                                v-if="index % 3 === 0"
                                class="absolute w-[150%] h-[30%] top-[35%] left-[-25%] rounded-full border border-[rgba(155,135,245,0.3)] transform rotate-[-30deg] after:content-[''] after:absolute after:top-[-50%] after:left-[10%] after:w-[80%] after:h-[200%] after:bg-[linear-gradient(90deg,transparent,rgba(51,195,240,0.2),transparent)] after:transform after:rotate-[10deg]"
                            ></div>
                        </div>

                        <!-- Pulsar star -->
                        <div
                            v-if="element.type === 'pulsar'"
                            class="absolute rounded-full bg-secondary shadow-[0_0_10px_#33c3f0,0_0_20px_rgba(51,195,240,0.5)]"
                            :style="{
                                width: `${element.size}px`,
                                height: `${element.size}px`,
                                top: `${element.top}%`,
                                right: `${element.right}%`,
                                animationDuration: `${element.pulseSpeed}s`,
                                animationDelay: `${element.delay}s`,
                                animation: `pulsar-glow ${element.pulseSpeed}s ease-in-out infinite`,
                                willChange: 'transform, opacity',
                                transform: 'translateZ(0)',
                            }"
                        ></div>

                        <!-- Quantum wave -->
                        <div
                            v-if="element.type === 'quantum'"
                            class="absolute w-10 bg-[linear-gradient(90deg,transparent,rgba(155,135,245,0.1),rgba(155,135,245,0.3),rgba(155,135,245,0.1),transparent)] origin-center"
                            :style="{
                                height: `${element.height}px`,
                                right: `${element.right}%`,
                                top: `${element.top}%`,
                                animationDuration: `${element.waveSpeed}s`,
                                animation: `quantum-wave ${element.waveSpeed}s ease-in-out infinite`,
                                willChange: 'transform, opacity',
                                transform: 'translateZ(0)',
                            }"
                        ></div>
                    </template>
                </div>

                <!-- Thermal Edge Effect -->
                <div class="absolute bottom-0 right-0 w-[70%] h-[70%] bg-[radial-gradient(circle_at_bottom_right,rgba(255,100,50,0.15),transparent_70%)] [mask-image:linear-gradient(135deg,transparent,rgba(0,0,0,0.7))] z-1"></div>
            </div>
        </div>

        <!-- Enhanced Card Footer - Progress Bar -->
        <div class="h-1/5 px-3 pb-3 pt-1.5 border-t border-[rgba(155,135,245,0.1)] bg-[rgba(33,92,187,0.9)] flex flex-col justify-end">
            <!-- Progress Container with Integrated Text -->
            <div class="relative mt-[18px]">
                <!-- Stock text moved above progress bar -->
                <div
                    class="text-xs absolute inset-x-0 top-[-18px] text-white opacity-90"
                >
                    <span v-if="flashItem.stok_tersedia !== null">
                        Tersisa {{ flashItem.stok_tersedia }}
                    </span>
                    <span v-else>Stok tersedia</span>
                </div>

                <!-- Progress Bar with enhanced styling -->
                <div class="h-[calc(6px+0.5rem)] bg-[rgba(255,255,255,0.1)] overflow-hidden relative backdrop-blur-[4px] shadow-[inset_0_1px_3px_rgba(0,0,0,0.2)] px-1 py-0.5 rounded-full">
                    <div
                        class="h-full bg-primary transition-width duration-1000 ease-out relative shadow-[0_0_10px_rgba(155,135,245,0.5)] bg-[linear-gradient(90deg,rgba(155,135,245,0.8),rgba(155,135,245,1)_50%,rgba(155,135,245,0.8))]"
                        :class="{ 'bg-red-500 bg-[linear-gradient(90deg,rgba(239,68,68,0.8),rgba(239,68,68,1)_50%,rgba(239,68,68,0.8))] shadow-[0_0_10px_rgba(239,68,68,0.5)]': isStockLow }"
                        :style="{ width: `${stockPercentage}%` }"
                    >
                        <!-- Animated particle sparks for thermal effect -->
                        <div class="absolute right-0 top-0 h-full w-[25px] overflow-hidden">
                            <div 
                                v-for="i in 5" 
                                :key="i"
                                class="absolute w-0.5 h-0.5 bg-[rgba(255,200,50,0.8)] rounded-full shadow-[0_0_4px_rgba(255,200,50,0.6)]"
                                :style="{
                                    animation: `spark-float 2s infinite ease-out`,
                                    animationDelay: `${i * 0.4}s`,
                                    left: `${i * 5}px`,
                                    top: `${50 - i * 10}%`,
                                    transform: 'translateZ(0)'
                                }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hexagonal grid pattern overlay -->
        <div class="absolute inset-0 opacity-20 pointer-events-none z-0 bg-[radial-gradient(rgba(155,135,245,0.1)_2px,transparent_2px)] bg-[size:16px_16px] bg-[position:-8px_-8px]"></div>

        <!-- CRT scanline effect -->
        <div class="absolute inset-0 pointer-events-none z-5 opacity-5 bg-[linear-gradient(to_bottom,rgba(255,255,255,0)_50%,rgba(0,0,0,0.05)_50%)] bg-[size:100%_4px]"></div>
    </div>
</template>

<style scoped>
/* Complex animations that are hard to represent in Tailwind */
@keyframes orbit-rotation {
    0% {
        transform: rotate(0deg) translateX(5px) rotate(0deg);
    }
    100% {
        transform: rotate(360deg) translateX(5px) rotate(-360deg);
    }
}

@keyframes pulsar-glow {
    0%, 100% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.2);
        opacity: 1;
        box-shadow: 0 0 15px #33c3f0, 0 0 30px rgba(51, 195, 240, 0.5);
    }
}

@keyframes quantum-wave {
    0% {
        opacity: 0.3;
        transform: scaleY(0.7) rotate(10deg);
    }
    50% {
        opacity: 0.6;
        transform: scaleY(1) rotate(-5deg);
    }
    100% {
        opacity: 0.3;
        transform: scaleY(0.7) rotate(10deg);
    }
}

@keyframes spark-float {
    0% {
        transform: translateY(0) scale(0.8);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translateY(-10px) scale(1.2);
        opacity: 0;
    }
}

@keyframes price-pulse {
    0% {
        text-shadow: 0 0 5px rgba(155, 135, 245, 0.3);
    }
    100% {
        text-shadow: 0 0 12px rgba(155, 135, 245, 0.7);
    }
}

/* Media queries for responsive design */
@media (max-width: 768px) {
    .cosmic-planet:nth-child(3),
    .cosmic-planet:nth-child(4),
    .cosmic-pulsar:nth-child(3) {
        display: none;
    }
}

/* Add text shadow utility for neon effect */
.text-shadow-neon {
    text-shadow: 0 0 8px rgba(155, 135, 245, 0.7);
}

/* For reduced motion preference */
@media (prefers-reduced-motion: reduce) {
    .progress-bar {
        transition: width 2s linear;
    }

    .spark {
        animation: none;
    }
}
</style>

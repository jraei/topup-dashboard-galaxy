
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
    const original = layanan.value.harga_beli_idr;
    const sale = props.flashItem.harga_flashsale;

    if (!original || !sale || original <= 0) return 0;

    const percentage = Math.round(((original - sale) / original) * 100);
    return percentage;
});

// Format price with IDR
const formatPrice = (price) => {
    if (!price) return "0";
    const formatted = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
            type: 'planet',
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
            type: 'pulsar',
            size: 3 + Math.floor(Math.random() * 5), // 3px to 8px
            top: Math.random() * 80 + 5,
            right: Math.random() * 80 + 10,
            pulseSpeed: 1.5 + Math.random() * 2, // 1.5s to 3.5s
            delay: Math.random() * 2,
        });
    }
    
    // Generate quantum waves
    elements.push({
        type: 'quantum',
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
            
            const cosmicLayer = cardRef.value.querySelector('.cosmic-layer');
            if (cosmicLayer) {
                cosmicLayer.style.transform = `translate(${moveX}px, ${moveY}px)`;
            }
        };
        
        cardRef.value.addEventListener('mousemove', handleMouseMove);
        cardRef.value.addEventListener('mouseleave', () => {
            const cosmicLayer = cardRef.value.querySelector('.cosmic-layer');
            if (cosmicLayer) {
                cosmicLayer.style.transform = 'translate(0, 0)';
            }
        });
    }
});
</script>

<template>
    <div ref="cardRef" class="flashsale-card group">
        <!-- User Limit Badge -->
        <div v-if="flashItem.batas_user" class="absolute z-20 top-2 right-2">
            <div class="px-2 py-1 text-xs text-white border rounded-full bg-black/70 border-primary">
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
        <div class="card-content">
            <!-- Left Section -->
            <div class="left-section">
                <!-- Product Image -->
                <div class="product-image">
                    <img
                        v-if="thumbnailImage"
                        :src="'/storage/' + thumbnailImage"
                        alt=""
                        loading="lazy"
                    />
                    <div class="image-glow"></div>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <h3 class="product-name">
                        {{ layanan.nama_layanan }}
                    </h3>
                    <p class="product-category">{{ produk.nama }}</p>
                </div>

                <!-- Price Section -->
                <div class="price-section">
                    <div class="flash-price">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="flash-icon"
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
                    <div class="regular-price">
                        <span>{{ formatPrice(layanan.harga_beli_idr) }}</span>
                        <span v-if="discountPercentage > 0" class="discount-badge">
                            -{{ discountPercentage }}%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Section - Cosmic Elements -->
            <div class="right-section">
                <div class="cosmic-layer">
                    <!-- Dynamic cosmic elements -->
                    <template v-for="(element, index) in cosmicElements" :key="index">
                        <!-- Planet -->
                        <div 
                            v-if="element.type === 'planet'" 
                            class="cosmic-planet"
                            :style="{
                                width: `${element.size}px`, 
                                height: `${element.size}px`,
                                top: `${element.top}%`, 
                                right: `${element.right}%`,
                                animationDuration: `${element.orbitSpeed}s`,
                                animationDelay: `${element.delay}s`
                            }"
                        >
                            <div class="planet-ring" v-if="index % 3 === 0"></div>
                        </div>
                        
                        <!-- Pulsar star -->
                        <div 
                            v-if="element.type === 'pulsar'" 
                            class="cosmic-pulsar"
                            :style="{
                                width: `${element.size}px`, 
                                height: `${element.size}px`,
                                top: `${element.top}%`, 
                                right: `${element.right}%`,
                                animationDuration: `${element.pulseSpeed}s`,
                                animationDelay: `${element.delay}s`
                            }"
                        ></div>
                        
                        <!-- Quantum wave -->
                        <div 
                            v-if="element.type === 'quantum'" 
                            class="quantum-wave"
                            :style="{
                                height: `${element.height}px`,
                                right: `${element.right}%`,
                                top: `${element.top}%`,
                                animationDuration: `${element.waveSpeed}s`
                            }"
                        ></div>
                    </template>
                </div>

                <!-- Thermal Edge Effect -->
                <div class="thermal-edge"></div>
            </div>
        </div>

        <!-- Card Footer - Progress Bar -->
        <div class="card-footer">
            <!-- Progress Bar -->
            <div class="progress-container">
                <div
                    class="progress-bar"
                    :class="[isStockLow ? 'low-stock' : '']"
                    :style="{ width: `${stockPercentage}%` }"
                >
                    <!-- Animated particle sparks for thermal effect -->
                    <div class="particle-sparks">
                        <div class="spark" v-for="i in 5" :key="i"></div>
                    </div>
                </div>
            </div>

            <!-- Stock text -->
            <p class="stock-text">
                <span v-if="flashItem.stok_tersedia !== null">
                    Tersisa {{ flashItem.stok_tersedia }}
                </span>
                <span v-else>Stok tersedia</span>
            </p>
        </div>
    </div>
</template>

<style scoped>
.flashsale-card {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(155, 135, 245, 0.1);
    border-radius: 0.5rem;
    background: #1A2236; /* Deep space blue */
    aspect-ratio: 5/3;
    height: 240px;
    max-width: 100%;
    transform-origin: center bottom;
}

.flashsale-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 0 25px rgba(155, 135, 245, 0.3);
}

/* Card content layout */
.card-content {
    display: flex;
    height: 80%;
    width: 100%;
    position: relative;
    overflow: hidden;
}

/* Left section - Product info and pricing */
.left-section {
    width: 65%;
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    z-index: 10;
}

/* Product image styles */
.product-image {
    width: 80px;
    height: 80px;
    position: relative;
    margin-bottom: 0.75rem;
    border-radius: 50%;
    overflow: hidden;
    background: rgba(0, 0, 0, 0.2);
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.flashsale-card:hover .product-image img {
    transform: scale(1.1);
}

.image-glow {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(155, 135, 245, 0.7);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.flashsale-card:hover .image-glow {
    opacity: 1;
}

/* Product info */
.product-info {
    margin-bottom: 0.5rem;
}

.product-name {
    font-size: 1rem;
    font-weight: bold;
    color: #9b87f5; /* primary color */
    margin-bottom: 0.25rem;
    transition: all 0.3s ease;
}

.flashsale-card:hover .product-name {
    text-shadow: 0 0 8px rgba(155, 135, 245, 0.7);
    transform: scale(1.02);
}

.product-category {
    font-size: 0.75rem;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
}

/* Price section */
.price-section {
    margin-top: auto;
}

.flash-price {
    display: flex;
    align-items: center;
    font-size: 1.1rem;
    font-weight: bold;
    color: #9b87f5;
    animation: price-pulse 3s infinite alternate;
}

.flash-icon {
    width: 16px;
    height: 16px;
    margin-right: 0.25rem;
    color: #33c3f0; /* secondary color */
}

.regular-price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.regular-price span:first-child {
    text-decoration: line-through;
    font-size: 0.75rem;
    color: #94a3b8;
    opacity: 0.7;
}

.discount-badge {
    background-color: #9b87f5;
    color: white;
    font-size: 0.65rem;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
}

/* Right section - Cosmic elements */
.right-section {
    width: 35%;
    position: relative;
    overflow: hidden;
}

.cosmic-layer {
    position: absolute;
    inset: 0;
    transition: transform 0.3s ease;
}

/* Cosmic elements */
.cosmic-planet {
    position: absolute;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.4), rgba(51, 195, 240, 0.2) 50%, transparent);
    box-shadow: inset -2px -2px 4px rgba(0, 0, 0, 0.5);
    animation: orbit-rotation linear infinite;
}

.planet-ring {
    position: absolute;
    width: 150%;
    height: 30%;
    top: 35%;
    left: -25%;
    border-radius: 50%;
    border: 1px solid rgba(155, 135, 245, 0.3);
    transform: rotate(-30deg);
}

.planet-ring::after {
    content: '';
    position: absolute;
    top: -50%;
    left: 10%;
    width: 80%;
    height: 200%;
    background: linear-gradient(90deg, transparent, rgba(51, 195, 240, 0.2), transparent);
    transform: rotate(10deg);
}

.cosmic-pulsar {
    position: absolute;
    border-radius: 50%;
    background-color: #33c3f0;
    box-shadow: 0 0 10px #33c3f0, 0 0 20px rgba(51, 195, 240, 0.5);
    animation: pulsar-glow ease-in-out infinite;
}

.quantum-wave {
    position: absolute;
    width: 40px;
    background: linear-gradient(90deg, transparent, rgba(155, 135, 245, 0.1), rgba(155, 135, 245, 0.3), rgba(155, 135, 245, 0.1), transparent);
    animation: quantum-wave ease-in-out infinite;
    transform-origin: center center;
}

/* Thermal edge effect */
.thermal-edge {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 70%;
    height: 70%;
    background: radial-gradient(circle at bottom right, rgba(255, 100, 50, 0.15), transparent 70%);
    mask-image: linear-gradient(135deg, transparent, rgba(0, 0, 0, 0.7));
    z-index: 1;
}

/* Card footer */
.card-footer {
    height: 20%;
    padding: 0.75rem;
    border-top: 1px solid rgba(155, 135, 245, 0.1);
    background-color: rgba(26, 34, 54, 0.8); /* Darker version of card background */
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.progress-container {
    height: 6px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 9999px;
    overflow: hidden;
    margin-bottom: 0.25rem;
}

.progress-bar {
    height: 100%;
    background-color: #9b87f5; /* primary */
    border-radius: 9999px;
    transition: width 1s ease-out;
    position: relative;
}

.progress-bar.low-stock {
    background-color: #ef4444; /* Red for low stock */
}

.stock-text {
    font-size: 0.75rem;
    color: #cbd5e1;
}

/* Particle sparks for thermal effect */
.particle-sparks {
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    width: 20px;
    overflow: hidden;
}

.spark {
    position: absolute;
    width: 2px;
    height: 2px;
    background-color: rgba(255, 200, 50, 0.8);
    border-radius: 50%;
    animation: spark-float 2s infinite ease-out;
}

.spark:nth-child(1) { right: 5px; animation-delay: 0s; }
.spark:nth-child(2) { right: 8px; animation-delay: 0.4s; }
.spark:nth-child(3) { right: 3px; animation-delay: 0.8s; }
.spark:nth-child(4) { right: 10px; animation-delay: 1.2s; }
.spark:nth-child(5) { right: 7px; animation-delay: 1.6s; }

/* Hexagonal grid pattern overlay */
.flashsale-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(rgba(155, 135, 245, 0.1) 2px, transparent 2px);
    background-size: 16px 16px;
    background-position: -8px -8px;
    opacity: 0.2;
    z-index: 0;
    pointer-events: none;
}

/* CRT scanline effect */
.flashsale-card::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 50%, rgba(0, 0, 0, 0.05) 50%);
    background-size: 100% 4px;
    pointer-events: none;
    z-index: 5;
    opacity: 0.05;
}

/* Animations */
@keyframes price-pulse {
    0% {
        text-shadow: 0 0 5px rgba(155, 135, 245, 0.3);
    }
    100% {
        text-shadow: 0 0 12px rgba(155, 135, 245, 0.7);
    }
}

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

/* Media queries for responsive design */
@media (max-width: 768px) {
    .left-section {
        width: 70%;
    }
    
    .right-section {
        width: 30%;
    }
    
    .product-image {
        width: 60px;
        height: 60px;
    }
    
    /* Hide some cosmic elements on mobile */
    .cosmic-planet:nth-child(3),
    .cosmic-planet:nth-child(4),
    .cosmic-pulsar:nth-child(3) {
        display: none;
    }
    
    /* Increase scroll speed by 30% (handled in parent component) */
}
</style>

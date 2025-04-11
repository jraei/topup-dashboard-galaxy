
<script setup>
import { computed, ref, onMounted } from "vue";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        default: 0
    }
});

const productName = computed(() => props.product.nama || "Unnamed Product");
const developer = computed(() => props.product.developer || "Unknown Developer");
const thumbnail = computed(() => {
    if (props.product.thumbnail && props.product.thumbnail.startsWith("http")) {
        return props.product.thumbnail;
    } else if (props.product.thumbnail) {
        return `/storage/${props.product.thumbnail}`;
    }
    return "/img/default-product.png";
});

// Cosmic effects configuration
const nebulaType = ref(Math.floor(Math.random() * 4));
const starDensity = ref(Math.floor(Math.random() * 5) + 5);
const blackholeSize = ref(Math.floor(Math.random() * 20) + 15);
const pulsarAngle = ref(Math.floor(Math.random() * 360));

// Generate random stars
const stars = ref([]);
const generateStars = () => {
    stars.value = [];
    for (let i = 0; i < starDensity.value; i++) {
        stars.value.push({
            size: Math.random() * 2 + 1,
            left: `${Math.random() * 100}%`,
            top: `${Math.random() * 100}%`,
            delay: Math.random() * 5
        });
    }
};

// Check reduced motion preference
const prefersReducedMotion = ref(false);
const checkReducedMotion = () => {
    prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
};

onMounted(() => {
    generateStars();
    checkReducedMotion();
});

// Compute nebula gradients
const nebulaGradient = computed(() => {
    const gradients = [
        'radial-gradient(circle at 70% 70%, rgba(155, 135, 245, 0.15) 0%, transparent 60%)',
        'radial-gradient(circle at 30% 60%, rgba(51, 195, 240, 0.15) 0%, transparent 65%)',
        'radial-gradient(ellipse at 20% 20%, rgba(155, 135, 245, 0.2) 0%, transparent 70%)',
        'radial-gradient(circle at 80% 30%, rgba(51, 195, 240, 0.2) 0%, transparent 75%)'
    ];
    
    return gradients[nebulaType.value];
});
</script>

<template>
    <div
        class="relative overflow-hidden transition-all duration-300 ease-out border rounded-lg bg-gradient-to-br from-secondary/5 to-content_background border-primary/20 group hover:border-primary hover:scale-105 hover:shadow-glow-primary cosmic-card"
        :class="{ 'reduced-motion': prefersReducedMotion }"
    >
        <!-- Nebula Background -->
        <div class="absolute inset-0 nebula-background" :style="{ backgroundImage: nebulaGradient }"></div>
        
        <!-- Stars -->
        <div class="absolute inset-0 overflow-hidden stars-layer">
            <div 
                v-for="(star, i) in stars" 
                :key="i"
                class="absolute rounded-full bg-primary-text twinkle-star" 
                :style="{
                    width: `${star.size}px`,
                    height: `${star.size}px`,
                    left: star.left,
                    top: star.top,
                    animationDelay: `${star.delay}s`
                }"
            ></div>
        </div>
        
        <!-- Black Hole Effect (Bottom Right) -->
        <div 
            class="absolute bottom-0 right-0 black-hole" 
            :style="{
                width: `${blackholeSize.value}px`, 
                height: `${blackholeSize.value}px`
            }"
        >
            <div class="accretion-disk"></div>
        </div>
        
        <!-- Pulsar Beam -->
        <div class="pulsar-container" :style="{ transform: `rotate(${pulsarAngle}deg)` }">
            <div class="pulsar-beam"></div>
        </div>

        <!-- Card Body -->
        <div class="flex p-1 h-[80%] relative z-10">
            <!-- Product Thumbnail -->
            <div class="flex items-center justify-center w-2/5">
                <div
                    class="w-16 h-16 overflow-hidden transition-transform duration-500 ease-out border rounded-lg md:w-24 md:h-24 border-primary/30 hover:border-primary/60 product-image-container"
                >
                    <img
                        :src="thumbnail"
                        :alt="productName"
                        loading="lazy"
                        class="object-cover w-full h-full product-image"
                    />
                    <!-- Cosmic Ray Overlay -->
                    <div class="cosmic-rays"></div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="flex flex-col justify-center w-3/5">
                <div class="text-protection p-1 rounded-lg">
                    <h3
                        class="mb-1 text-xs font-bold md:text-lg text-primary-text line-clamp-2 product-title"
                    >
                        {{ productName }}
                    </h3>
                    <p class="text-xs md:text-sm text-primary-text/80 product-developer">
                        {{ developer }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Card Footer -->
        <div
            class="border-t border-primary/20 bg-primary/10 py-2 px-3 h-[20%] flex items-center justify-end relative z-10"
        >
            <!-- Quantum Fractal Border (visible on hover) -->
            <div class="quantum-fractal"></div>
        </div>
    </div>
</template>

<style scoped>
/* Base Card Styling */
.cosmic-card {
    position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    overflow: hidden;
}

.cosmic-card:hover {
    transform: translateY(-5px) scale(1.02);
}

/* Nebula Background */
.nebula-background {
    opacity: 0.7;
    transition: opacity 0.4s ease;
    z-index: 1;
}

.cosmic-card:hover .nebula-background {
    opacity: 1;
}

/* Stars Layer */
.twinkle-star {
    box-shadow: 0 0 4px #fff;
    animation: twinkle 4s infinite ease-in-out;
}

@keyframes twinkle {
    0%, 100% {
        opacity: 0.2;
        transform: scale(0.8);
    }
    50% {
        opacity: 1;
        transform: scale(1.2);
    }
}

/* Black Hole Effect */
.black-hole {
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 70%);
    filter: blur(2px);
    z-index: 2;
    transform: translate(30%, 30%);
}

.accretion-disk {
    position: absolute;
    inset: -6px;
    border-radius: 50%;
    border: 2px solid transparent;
    background-image: conic-gradient(
        from 0deg,
        rgba(51, 195, 240, 0.8),
        rgba(155, 135, 245, 0.6),
        rgba(51, 195, 240, 0.2),
        transparent
    );
    animation: rotate 20s linear infinite;
    opacity: 0.5;
    transform: rotateX(70deg);
}

.cosmic-card:hover .accretion-disk {
    opacity: 0.8;
}

/* Pulsar Beam */
.pulsar-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
    opacity: 0.3;
    transition: opacity 0.4s ease;
}

.pulsar-beam {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: conic-gradient(
        from 0deg,
        transparent 0deg,
        rgba(155, 135, 245, 0.5) 5deg,
        transparent 10deg,
        transparent 175deg,
        rgba(155, 135, 245, 0.5) 180deg,
        transparent 185deg,
        transparent 355deg
    );
    animation: rotate 8s linear infinite;
}

.cosmic-card:hover .pulsar-container {
    opacity: 0.5;
}

/* Text Protection */
.text-protection {
    background-color: rgba(7, 76, 172, 0.5); /* content_background with 50% opacity */
    backdrop-filter: blur(2px);
}

/* Product Image Container */
.product-image-container {
    position: relative;
    z-index: 15;
    box-shadow: 0 0 10px rgba(155, 135, 245, 0.3);
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
}

.cosmic-card:hover .product-image-container {
    transform: translateY(-3px) rotate(2deg);
}

/* Product Image */
.product-image {
    transition: transform 1s ease;
}

.cosmic-card:hover .product-image {
    transform: scale(1.05);
}

/* Cosmic Rays */
.cosmic-rays {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(45deg, transparent 95%, rgba(155, 135, 245, 0.3) 95%, transparent 96%),
        linear-gradient(135deg, transparent 97%, rgba(51, 195, 240, 0.3) 97%, transparent 98%);
    opacity: 0.3;
    mix-blend-mode: screen;
    pointer-events: none;
}

/* Quantum Fractal Border */
.quantum-fractal {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background-image: linear-gradient(90deg, 
        transparent, 
        rgba(155, 135, 245, 0.5),
        rgba(51, 195, 240, 0.8),
        rgba(155, 135, 245, 0.5),
        transparent
    );
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.5s ease;
}

.cosmic-card:hover .quantum-fractal {
    transform: scaleX(1);
}

/* Animation for rotation */
@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Text Styles */
.product-title {
    text-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
}

.product-developer {
    text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
}

/* Reduced Motion */
.reduced-motion .twinkle-star,
.reduced-motion .accretion-disk,
.reduced-motion .pulsar-beam,
.reduced-motion:hover .product-image-container,
.reduced-motion:hover .product-image {
    animation: none !important;
    transition: none !important;
    transform: none !important;
}

/* Media Queries */
@media (max-width: 768px) {
    .pulsar-beam {
        opacity: 0.3;
    }
    
    .black-hole {
        transform: translate(40%, 40%);
    }
    
    .stars-layer .twinkle-star:nth-child(odd) {
        display: none; /* Reduce stars on mobile */
    }
}
</style>

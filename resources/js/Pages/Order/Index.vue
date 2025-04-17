
<script setup>
import { ref } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Link } from "@inertiajs/vue3";
import { Rocket, MessageSquare, Shield } from "lucide-vue-next";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
    produk: Object,
    layanans: Object,
    user: Object,
    inputFields: Array,
});

const showModal = ref(false);
const savedIdForFuture = ref(false);

// Format currency to IDR
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Calculate minimum price from layanans
const getMinimumPrice = () => {
    if (!props.layanans || props.layanans.length === 0) return 0;
    return Math.min(...props.layanans.map(item => item.harga_jual));
};
</script>

<template>
    <GuestLayout>
        <!-- Product Information Section -->
        <section class="relative w-full overflow-hidden">
            <!-- Banner -->
            <div v-if="produk.banner" class="relative w-full min-h-56 overflow-hidden">
                <img :src="`/storage/${produk.banner}`" 
                     alt="Product Banner" 
                     class="w-full object-cover h-full md:object-center" 
                     width="1920" 
                     height="340">
            </div>
            
            <!-- Cosmic Product Panel -->
            <div class="relative bg-primary overflow-hidden py-8 px-4 md:px-8 lg:px-12">
                <!-- Saturn Decoration (visible on larger screens) -->
                <div class="absolute right-0 top-20 hidden md:block" aria-hidden="true">
                    <div class="w-40 h-40 opacity-20 bg-secondary rounded-full transform rotate-12 relative overflow-visible">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-secondary to-primary opacity-50"></div>
                        <div class="absolute inset-[-10px] border-2 border-secondary/30 rounded-full transform -rotate-12"></div>
                    </div>
                </div>
                
                <!-- Stars background (CSS-generated) -->
                <div class="absolute inset-0 overflow-hidden opacity-10">
                    <div class="stars-sm"></div>
                    <div class="stars-md"></div>
                    <div class="stars-lg"></div>
                </div>
                
                <!-- Product Info - Flex Container 1 -->
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6 relative z-10 mb-12">
                    <!-- Thumbnail with 3D Effect -->
                    <div class="relative mt-[-4rem] md:mt-[-6rem] transform perspective-cosmic">
                        <div class="w-32 md:w-60 h-32 md:h-60 overflow-hidden rounded-lg 
                                  shadow-glow-primary transform rotateY-cosmic">
                            <img v-if="produk.thumbnail" 
                                 :src="`/storage/${produk.thumbnail}`" 
                                 :alt="produk.nama" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Fallback if no thumbnail -->
                            <div v-else class="w-full h-full bg-secondary/30 flex items-center justify-center">
                                <span class="text-white text-xl">{{ produk.nama.charAt(0) }}</span>
                            </div>
                        </div>
                        
                        <!-- Orbiting Planets -->
                        <div class="absolute w-4 h-4 rounded-full bg-secondary/80 orbit-element-1"></div>
                        <div class="absolute w-2 h-2 rounded-full bg-primary-hover/80 orbit-element-2"></div>
                        <div class="absolute w-3 h-3 rounded-full bg-secondary-hover/80 orbit-element-3"></div>
                    </div>
                    
                    <!-- Title & Description -->
                    <div class="text-center md:text-left text-white">
                        <h1 class="text-2xl md:text-4xl font-bold cosmic-text-glow mb-2">{{ produk.nama }}</h1>
                        <h2 v-if="produk.developer" class="text-lg md:text-xl text-secondary cosmic-underline mb-4">
                            {{ produk.developer }}
                        </h2>
                        <div class="max-w-xl mt-4">
                            <div class="flex items-center mb-2">
                                <span class="text-primary-text text-lg font-semibold mr-2">Starting from:</span>
                                <span class="text-secondary text-xl font-bold">{{ formatCurrency(getMinimumPrice()) }}</span>
                            </div>
                            <p class="text-sm md:text-base text-primary-text/80">
                                {{ produk.deskripsi_game }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Product Info - Flex Container 2 (Feature Icons) -->
                <div class="grid grid-cols-3 gap-4 justify-evenly relative z-10">
                    <!-- Fast Process -->
                    <div class="flex flex-col items-center text-primary-text">
                        <div class="w-12 h-12 rounded-full bg-secondary/20 flex items-center justify-center mb-2 cosmic-pulse">
                            <Rocket class="w-6 h-6 text-primary-text" />
                        </div>
                        <span class="text-xs md:text-sm font-medium text-center">Proses Cepat</span>
                    </div>
                    
                    <!-- 24/7 Support -->
                    <div class="flex flex-col items-center text-primary-text">
                        <div class="w-12 h-12 rounded-full bg-secondary/20 flex items-center justify-center mb-2 cosmic-pulse">
                            <MessageSquare class="w-6 h-6 text-primary-text" />
                        </div>
                        <span class="text-xs md:text-sm font-medium text-center">Layanan Chat 24/7</span>
                    </div>
                    
                    <!-- Secure Payment -->
                    <div class="flex flex-col items-center text-primary-text">
                        <div class="w-12 h-12 rounded-full bg-secondary/20 flex items-center justify-center mb-2 cosmic-pulse">
                            <Shield class="w-6 h-6 text-primary-text" />
                        </div>
                        <span class="text-xs md:text-sm font-medium text-center">Pembayaran Aman</span>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- User Data Section -->
        <section class="py-8 px-4 bg-content_background">
            <div class="max-w-4xl mx-auto">
                <!-- Cosmic Card Component -->
                <div class="rounded-lg overflow-hidden cosmic-card mb-8">
                    <!-- Card Header -->
                    <div class="bg-primary p-4 text-white relative">
                        <!-- Constellation pattern -->
                        <div class="absolute inset-0 opacity-10 constellation-pattern"></div>
                        
                        <div class="relative z-10">
                            <h3 class="text-lg md:text-xl font-bold">Step 1 of 6: Masukkan Data Akun</h3>
                            <!-- Cosmic progress bar -->
                            <div class="w-full h-2 bg-secondary/30 rounded-full mt-2 overflow-hidden">
                                <div class="h-full w-1/6 bg-gradient-to-r from-primary-hover to-secondary rounded-full"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="bg-dark-card p-6 relative">
                        <!-- Cosmic noise texture background -->
                        <div class="absolute inset-0 bg-noise opacity-[0.02]"></div>
                        
                        <!-- Dynamic Input Fields -->
                        <form class="relative z-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                <template v-for="field in inputFields" :key="field.id">
                                    <div class="space-y-2">
                                        <label :for="`field_${field.id}`" class="block text-primary-text font-semibold text-sm">
                                            {{ field.label }}
                                            <span v-if="field.required" class="text-secondary ml-1">*</span>
                                        </label>
                                        
                                        <!-- Text/Number Input -->
                                        <input 
                                            v-if="field.type === 'text' || field.type === 'number'" 
                                            :type="field.type" 
                                            :id="`field_${field.id}`" 
                                            :name="field.name" 
                                            :required="field.required"
                                            class="w-full rounded-md bg-dark-lighter border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/30 text-primary-text cosmic-input-effect"
                                        />
                                        
                                        <!-- Select Input -->
                                        <select 
                                            v-else-if="field.type === 'select'" 
                                            :id="`field_${field.id}`" 
                                            :name="field.name" 
                                            :required="field.required"
                                            class="w-full rounded-md bg-dark-lighter border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/30 text-primary-text cosmic-input-effect"
                                        >
                                            <option value="" disabled selected>Select an option</option>
                                            <option 
                                                v-for="option in field.options" 
                                                :key="option.id" 
                                                :value="option.value"
                                            >
                                                {{ option.label }}
                                            </option>
                                        </select>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Footer Elements -->
                            <div class="mt-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Save ID Checkbox -->
                                <div class="flex items-center">
                                    <Checkbox v-model:checked="savedIdForFuture" class="cosmic-checkbox" />
                                    <label for="saveId" class="ml-2 text-sm text-primary-text">
                                        Simpan ID untuk pembelian berikutnya
                                    </label>
                                </div>
                                
                                <!-- Purchase Guide Button -->
                                <button 
                                    type="button" 
                                    @click="showModal = true"
                                    class="px-4 py-2 rounded-md bg-primary/80 hover:bg-primary text-white transition-colors flex items-center justify-center gap-2"
                                >
                                    <span>Panduan Pembelian</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Cosmic Modal -->
        <Modal :show="showModal" @close="showModal = false" max-width="xl">
            <div class="p-4 md:p-6 bg-dark-card text-primary-text">
                <h3 class="text-xl font-bold mb-4 text-center">Panduan Pembelian</h3>
                
                <div class="space-y-6">
                    <!-- Purchase Guide Image -->
                    <div v-if="produk.petunjuk_field" class="mx-auto max-w-full overflow-hidden rounded-lg">
                        <img 
                            :src="`/storage/${produk.petunjuk_field}`" 
                            alt="Purchase Guide" 
                            class="w-full object-contain max-h-[60vh]"
                        />
                    </div>
                    
                    <!-- Purchase Guide Text -->
                    <div v-if="produk.deskripsi_game" class="prose prose-sm md:prose-base max-w-none text-primary-text">
                        <p>{{ produk.deskripsi_game }}</p>
                    </div>
                </div>
                
                <!-- Close Button -->
                <div class="mt-6 text-center">
                    <button 
                        type="button" 
                        @click="showModal = false"
                        class="px-6 py-2 rounded-md bg-primary text-white hover:bg-primary-hover transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </Modal>
    </GuestLayout>
</template>

<style scoped>
/* Cosmic Animation & Effects */
.cosmic-text-glow {
    text-shadow: 0 0 8px rgba(155, 135, 245, 0.5);
}

.cosmic-underline {
    position: relative;
    display: inline-block;
}

.cosmic-underline::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -2px;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, rgba(51, 195, 240, 0.3), rgba(51, 195, 240, 0.8), rgba(51, 195, 240, 0.3));
    animation: pulse-animation 3s infinite;
}

.shadow-glow-primary {
    box-shadow: 0 0 15px rgba(155, 135, 245, 0.4);
}

.perspective-cosmic {
    perspective: 800px;
}

.rotateY-cosmic {
    transform: rotateY(10deg) rotateX(-5deg);
    transform-origin: left center;
    transition: transform 0.3s ease;
}

.rotateY-cosmic:hover {
    transform: rotateY(5deg) rotateX(-2deg) scale(1.02);
}

/* Orbiting elements */
.orbit-element-1 {
    top: 10%;
    right: -10px;
    animation: orbit 8s linear infinite;
}

.orbit-element-2 {
    bottom: 30%;
    left: -5px;
    animation: orbit 12s linear infinite reverse;
}

.orbit-element-3 {
    top: 40%;
    right: 10%;
    animation: orbit 15s linear infinite;
}

.cosmic-pulse {
    animation: pulse-subtle 4s ease-in-out infinite;
}

.cosmic-input-effect {
    transition: all 0.3s ease;
}

.cosmic-input-effect:focus {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(155, 135, 245, 0.15);
}

.cosmic-card {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    will-change: transform, box-shadow;
}

.cosmic-card:hover {
    box-shadow: 0 12px 48px rgba(155, 135, 245, 0.2);
}

/* Background patterns */
.bg-noise {
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
}

.constellation-pattern {
    background-image: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
    background-size: 25px 25px;
}

/* Stars backgrounds */
.stars-sm, .stars-md, .stars-lg {
    position: absolute;
    width: 100%;
    height: 100%;
    background-repeat: repeat;
}

.stars-sm {
    background-image: radial-gradient(1px 1px at calc(100% * var(--x)) calc(100% * var(--y)), white, transparent);
    background-size: 150px 150px;
    animation: twinkling 4s infinite;
}

.stars-md {
    background-image: radial-gradient(1.5px 1.5px at calc(100% * var(--x)) calc(100% * var(--y)), white, transparent);
    background-size: 200px 200px;
    animation: twinkling 6s infinite 1s;
}

.stars-lg {
    background-image: radial-gradient(2px 2px at calc(100% * var(--x)) calc(100% * var(--y)), white, transparent);
    background-size: 250px 250px;
    animation: twinkling 8s infinite 2s;
}

/* Animations */
@keyframes orbit {
    from { transform: rotate(0deg) translateX(10px) rotate(0deg); }
    to { transform: rotate(360deg) translateX(10px) rotate(-360deg); }
}

@keyframes pulse-animation {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 1; }
}

@keyframes pulse-subtle {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.05); opacity: 1; }
}

@keyframes twinkling {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.8; }
}

/* Set random star positions using CSS custom properties */
.stars-sm {
    --x: 0.3;
    --y: 0.7;
}

.stars-md {
    --x: 0.7;
    --y: 0.4;
}

.stars-lg {
    --x: 0.5;
    --y: 0.9;
}
</style>

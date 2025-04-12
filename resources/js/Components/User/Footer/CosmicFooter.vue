
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import EarthSystem from './EarthSystem.vue';
import CosmicTransition from './CosmicTransition.vue';
import FooterContent from './FooterContent.vue';
import { Link } from '@inertiajs/vue3';
import CosmicIcon from '@/Components/User/Navigation/CosmicIcon.vue';

// Footer visibility state
const isVisible = ref(false);
const observer = ref(null);
const footerRef = ref(null);

// Handle intersection observer for triggering animations
onMounted(() => {
    observer.value = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                isVisible.value = true;
            } else {
                isVisible.value = false;
            }
        });
    }, {
        rootMargin: '200px 0px 0px 0px', // Start observing 200px before footer
        threshold: 0.1
    });
    
    if (footerRef.value) {
        observer.value.observe(footerRef.value);
    }
});

onUnmounted(() => {
    if (observer.value && footerRef.value) {
        observer.value.unobserve(footerRef.value);
    }
});
</script>

<template>
    <footer ref="footerRef" class="relative mt-24 overflow-hidden">
        <!-- Cosmic Transition (Atmosphere) -->
        <CosmicTransition :is-visible="isVisible" />
        
        <!-- Earth System with Rocket -->
        <div class="absolute right-8 -top-32 lg:-top-40 z-10 pointer-events-none">
            <EarthSystem :is-animated="isVisible" />
        </div>
        
        <!-- Main Footer Content -->
        <div class="relative z-20 bg-gradient-to-b from-dark-lighter to-dark pt-16 pb-6">
            <!-- Cosmic Divider (Nebula Pattern) -->
            <div class="w-full h-px bg-gradient-to-r from-transparent via-primary/20 to-transparent mb-12"></div>
            
            <!-- Footer Content -->
            <FooterContent />
            
            <!-- Copyright Bar -->
            <div class="relative mt-12 pt-6 border-t border-primary/10 text-center">
                <div class="container mx-auto px-4">
                    <p class="text-sm text-gray-400 flex items-center justify-center">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary/10 mr-2">
                            <CosmicIcon name="copyright" size="sm" className="text-primary" />
                        </span>
                        <span>2025 NaelStore. All rights across the cosmos reserved.</span>
                    </p>
                </div>
                
                <!-- Shooting Stars (Random Decorative) -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-30">
                    <div class="shooting-star delay-3"></div>
                    <div class="shooting-star delay-7"></div>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
@keyframes shootingstar {
    0% {
        transform: translateX(0) translateY(0) rotate(45deg);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    100% {
        transform: translateX(-200px) translateY(200px) rotate(45deg);
        opacity: 0;
    }
}

.shooting-star {
    position: absolute;
    width: 2px;
    height: 2px;
    background: linear-gradient(45deg, #fff, transparent);
    border-radius: 50%;
    filter: drop-shadow(0 0 6px #fff);
    animation: shootingstar 3s ease-in-out infinite;
}

.shooting-star::before {
    content: '';
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 1px;
    background: linear-gradient(90deg, #fff, transparent);
}

.shooting-star:nth-child(1) {
    top: 30%;
    right: 10%;
}

.shooting-star:nth-child(2) {
    top: 60%;
    right: 30%;
}

.delay-3 {
    animation-delay: 3s;
}

.delay-7 {
    animation-delay: 7s;
}
</style>

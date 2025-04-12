
<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import CosmicIcon from '@/Components/User/Navigation/CosmicIcon.vue';
import FooterRocket from './FooterRocket.vue';
import Earth3D from './Earth3D.vue';
import CosmicDust from './CosmicDust.vue';
import SocialLinks from './SocialLinks.vue';
import NavLinks from './NavLinks.vue';
import CopyrightBar from './CopyrightBar.vue';
import PromoFooterBanner from './PromoFooterBanner.vue';

// We'll simulate getting this from the backend in a real implementation
// In a production app, this would come from props passed from the layout
const footerData = {
    meta_description: "The best place to buy game credits and top-ups at affordable prices with cosmic delivery speed.",
    support_instagram: "https://instagram.com/yourgame",
    support_whatsapp: "6281234567890",
    support_email: "support@yourgame.com",
    support_youtube: "https://youtube.com/yourgame",
    support_facebook: "https://facebook.com/yourgame",
};

const isPreludeVisible = ref(false);
const footerRef = ref(null);

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            isPreludeVisible.value = entries[0].isIntersecting;
        },
        { threshold: 0.1 }
    );
    
    if (footerRef.value) {
        observer.observe(footerRef.value);
    }
});

const navLinks = [
    { name: "Topup", route: "index" },
    { name: "Transactions", route: "index" },
    { name: "Leaderboard", route: "index" },
    { name: "Calculator", route: "index" },
];

const legalLinks = [
    { name: "Terms of Service", route: "index" },
    { name: "Privacy Policy", route: "index" },
    { name: "Refund Policy", route: "index" },
];

// Compute classes based on visibility for smooth transitions
const preludeClasses = computed(() => {
    return {
        'opacity-100': isPreludeVisible.value,
        'opacity-0': !isPreludeVisible.value,
        'transform translate-y-0': isPreludeVisible.value,
        'transform translate-y-10': !isPreludeVisible.value,
    };
});
</script>

<template>
    <footer ref="footerRef" class="relative mt-16">
        <!-- Cosmic Prelude with Earth and Rocket -->
        <div 
            :class="[
                'relative overflow-hidden transition-all duration-1000 ease-in-out py-16',
                preludeClasses
            ]"
        >
            <div class="absolute inset-0 bg-gradient-to-b from-content_background via-content_background/90 to-primary/10"></div>
            
            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center justify-center lg:justify-between">
                    <!-- Earth and cosmic elements section -->
                    <div class="relative w-full lg:w-1/3 flex justify-center lg:justify-start">
                        <Earth3D 
                            :size="{ 
                                sm: 80, 
                                md: 120, 
                                lg: 160 
                            }" 
                        />
                        <CosmicDust :particle-count="30" />
                    </div>
                    
                    <!-- Center text content -->
                    <div class="w-full lg:w-2/3 mt-10 lg:mt-0 text-center lg:text-left">
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-primary-text">
                            <span class="inline-block relative">
                                Explore the Gaming Universe
                                <span class="absolute -top-1 -right-2 w-2 h-2 bg-secondary rounded-full animate-ping"></span>
                            </span>
                        </h2>
                        <p class="mt-4 text-lg text-white/80 max-w-2xl mx-auto lg:mx-0">
                            Top up your favorite games and embark on cosmic adventures with the best prices across the galaxy.
                        </p>
                        
                        <div class="mt-8 flex flex-wrap justify-center lg:justify-start gap-4">
                            <Link 
                                :href="route('index')" 
                                class="px-6 py-3 rounded-full bg-primary hover:bg-primary-hover text-white font-medium transition-all duration-300 transform hover:scale-105"
                            >
                                Explore Games
                            </Link>
                            <Link 
                                :href="route('index')" 
                                class="px-6 py-3 rounded-full border border-white/20 hover:border-primary hover:bg-primary/10 text-white font-medium transition-all duration-300"
                            >
                                View Promotions
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Rocket Animation (absolutely positioned) -->
            <FooterRocket />
        </div>
        
        <!-- Promotional Banner -->
        <PromoFooterBanner />
        
        <!-- Main Footer Content -->
        <div class="bg-gradient-to-b from-dark/lighter to-dark relative overflow-hidden pt-12 pb-8">
            <!-- Cosmic background elements -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute top-10 left-1/4 w-1 h-1 bg-white rounded-full"></div>
                <div class="absolute top-20 left-1/3 w-1.5 h-1.5 bg-white rounded-full"></div>
                <div class="absolute bottom-10 right-1/4 w-1 h-1 bg-white rounded-full"></div>
                <div class="absolute bottom-20 right-1/3 w-2 h-2 bg-white rounded-full"></div>
                <!-- More stars scattered throughout -->
                <div class="absolute top-1/3 left-2/3 w-1 h-1 bg-white rounded-full"></div>
                <div class="absolute top-2/3 left-1/2 w-0.5 h-0.5 bg-white rounded-full"></div>
                <div class="absolute top-1/2 right-1/3 w-1 h-1 bg-white rounded-full"></div>
            </div>
            
            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
                    <!-- Brand Column -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <img src="/favicon.ico" alt="Logo" class="w-8 h-8" />
                            <span class="text-xl font-bold text-white">NaelStore</span>
                        </div>
                        
                        <p class="text-sm text-white/70">
                            {{ footerData.meta_description }}
                        </p>
                        
                        <!-- Social Links -->
                        <SocialLinks :social-data="footerData" />
                    </div>
                    
                    <!-- Quick Links Column -->
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Quick Links</h3>
                        <NavLinks :links="navLinks" />
                    </div>
                    
                    <!-- Legal Links Column -->
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-4">Legal</h3>
                        <NavLinks :links="legalLinks" />
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copyright Bar -->
        <CopyrightBar />
    </footer>
</template>

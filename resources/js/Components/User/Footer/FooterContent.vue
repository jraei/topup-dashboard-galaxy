
<script setup>
import { Link } from '@inertiajs/vue3';
import CosmicIcon from '@/Components/User/Navigation/CosmicIcon.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

// Footer navigation links
const quickLinks = [
    { name: 'Home', route: 'home', icon: 'home' },
    { name: 'Products', route: 'products', icon: 'topup' },
    { name: 'About Us', route: 'about', icon: 'planet' }
];

const supportLinks = [
    { name: 'Contact', route: 'contact', icon: 'satellite' },
    { name: 'FAQ', route: 'faq', icon: 'question' },
    { name: 'Terms', route: 'terms', icon: 'document' }
];

const socialLinks = [
    { name: 'Discord', url: '#', icon: 'message' },
    { name: 'Twitter', url: '#', icon: 'twitter' },
    { name: 'Instagram', url: '#', icon: 'instagram' }
];
</script>

<template>
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Branding Column -->
            <div class="space-y-4">
                <ApplicationLogo class="h-8 w-auto" />
                
                <p class="text-sm text-gray-400 mt-4">
                    Your gateway to digital products across the cosmic universe. Explore our vast catalog of games, services and more.
                </p>
                
                <!-- Starfield Background (Decorative) -->
                <div class="relative mt-6 p-4 rounded-lg bg-dark-lighter overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-xs text-primary-text/70">Join our newsletter</p>
                        <div class="flex mt-2">
                            <input 
                                type="email" 
                                placeholder="Your email" 
                                class="flex-1 bg-dark rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary text-primary-text"
                            />
                            <button class="bg-primary hover:bg-primary-hover text-white rounded-r-md px-3">
                                <CosmicIcon name="mail" size="sm" />
                            </button>
                        </div>
                    </div>
                    
                    <!-- Micro Stars -->
                    <div class="absolute inset-0 pointer-events-none">
                        <div v-for="i in 15" :key="`microstars-${i}`" 
                            class="absolute rounded-full bg-white"
                            :style="{
                                width: `${0.5 + Math.random()}px`,
                                height: `${0.5 + Math.random()}px`,
                                top: `${Math.random() * 100}%`,
                                left: `${Math.random() * 100}%`,
                                opacity: 0.1 + Math.random() * 0.3
                            }">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-primary-text font-medium mb-4 flex items-center">
                    <span class="relative w-5 h-5 flex items-center justify-center mr-2">
                        <span class="animate-ping absolute h-2 w-2 rounded-full bg-primary opacity-75"></span>
                        <span class="relative rounded-full h-1.5 w-1.5 bg-primary"></span>
                    </span>
                    Quick Links
                </h3>
                
                <ul class="space-y-3">
                    <li v-for="link in quickLinks" :key="link.name">
                        <Link 
                            :href="route(link.route)" 
                            class="group flex items-center text-gray-400 hover:text-primary transition-colors duration-200"
                        >
                            <span class="w-8 h-8 flex items-center justify-center rounded-full bg-dark-card group-hover:bg-primary/10 transition-colors duration-200 mr-2">
                                <CosmicIcon :name="link.icon" size="sm" className="text-gray-300 group-hover:text-primary" />
                            </span>
                            <span>{{ link.name }}</span>
                            
                            <span class="ml-2 w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        </Link>
                    </li>
                </ul>
            </div>
            
            <!-- Support Links -->
            <div>
                <h3 class="text-primary-text font-medium mb-4 flex items-center">
                    <span class="relative w-5 h-5 flex items-center justify-center mr-2">
                        <span class="animate-ping absolute h-2 w-2 rounded-full bg-secondary opacity-75"></span>
                        <span class="relative rounded-full h-1.5 w-1.5 bg-secondary"></span>
                    </span>
                    Support
                </h3>
                
                <ul class="space-y-3">
                    <li v-for="link in supportLinks" :key="link.name">
                        <Link 
                            :href="route(link.route)" 
                            class="group flex items-center text-gray-400 hover:text-primary transition-colors duration-200"
                        >
                            <span class="w-8 h-8 flex items-center justify-center rounded-full bg-dark-card group-hover:bg-primary/10 transition-colors duration-200 mr-2">
                                <CosmicIcon :name="link.icon" size="sm" className="text-gray-300 group-hover:text-primary" />
                            </span>
                            <span>{{ link.name }}</span>
                            
                            <span class="ml-2 w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        </Link>
                    </li>
                </ul>
            </div>
            
            <!-- Social Links -->
            <div>
                <h3 class="text-primary-text font-medium mb-4 flex items-center">
                    <span class="relative w-5 h-5 flex items-center justify-center mr-2">
                        <span class="animate-ping absolute h-2 w-2 rounded-full bg-primary opacity-75"></span>
                        <span class="relative rounded-full h-1.5 w-1.5 bg-primary"></span>
                    </span>
                    Connect
                </h3>
                
                <div class="flex space-x-3 mb-6">
                    <a 
                        v-for="social in socialLinks" 
                        :key="social.name"
                        :href="social.url"
                        class="group relative w-10 h-10 rounded-full bg-dark flex items-center justify-center hover:bg-primary/10 transition-colors duration-200"
                    >
                        <CosmicIcon :name="social.icon" size="md" className="text-gray-300 group-hover:text-primary" />
                        
                        <!-- Orbiting Satellite Effect -->
                        <span class="absolute w-1.5 h-1.5 rounded-full bg-secondary opacity-0 group-hover:opacity-100 transition-all duration-300"
                            :style="{ animation: 'orbit 3s linear infinite' }">
                        </span>
                    </a>
                </div>
                
                <div class="bg-dark-card p-4 rounded-lg">
                    <p class="text-xs text-gray-400">
                        Need help with your order? Contact our support team available 24/7 across all time zones.
                    </p>
                    <a href="#" class="inline-block mt-2 text-primary text-sm hover:text-primary-hover transition-colors duration-200">Get Support →</a>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes orbit {
    0% { transform: translate(8px, 0px); }
    25% { transform: translate(0px, 8px); }
    50% { transform: translate(-8px, 0px); }
    75% { transform: translate(0px, -8px); }
    100% { transform: translate(8px, 0px); }
}
</style>

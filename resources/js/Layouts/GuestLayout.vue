
<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import { Search, Globe, User, Rocket, Menu, X, ChevronDown } from "lucide-vue-next";

const showingNavigationDropdown = ref(false);
const showSearch = ref(false);
const showLanguageSelector = ref(false);
const currentLanguage = ref("ID");

const navigationLinks = [
    { name: "Home", route: "index", icon: "Home" },
    { name: "Games", route: "dashboard", icon: "GameController" },
    { name: "Top Up", route: "dashboard", icon: "Zap" },
    { name: "Vouchers", route: "dashboard", icon: "Gift" },
    { name: "Support", route: "dashboard", icon: "LifeBuoy" },
];

const toggleMobileMenu = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value;
    if (showingNavigationDropdown.value) {
        document.body.classList.add("overflow-hidden");
    } else {
        document.body.classList.remove("overflow-hidden");
    }
};

const toggleSearch = () => {
    showSearch.value = !showSearch.value;
};

const toggleLanguageSelector = () => {
    showLanguageSelector.value = !showLanguageSelector.value;
};

// Parallax starfield effect
const stars = ref([]);
const mousePosition = ref({ x: 0, y: 0 });

const handleMouseMove = (e) => {
    mousePosition.value = {
        x: e.clientX / window.innerWidth,
        y: e.clientY / window.innerHeight
    };
};

onMounted(() => {
    // Generate random stars for parallax effect
    for (let i = 0; i < 50; i++) {
        stars.value.push({
            x: Math.random() * 100,
            y: Math.random() * 100,
            size: Math.random() * 2 + 1,
            parallaxFactor: Math.random() * 20 + 5
        });
    }
    
    window.addEventListener('mousemove', handleMouseMove);
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
    document.body.classList.remove("overflow-hidden");
});
</script>

<template>
    <div class="bg-content_background min-h-screen flex flex-col">
        <!-- Cosmic Starfield Background -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
            <div 
                v-for="(star, index) in stars" 
                :key="index"
                class="absolute rounded-full bg-white opacity-70"
                :style="{
                    left: `${star.x - (mousePosition.x * star.parallaxFactor)}%`,
                    top: `${star.y - (mousePosition.y * star.parallaxFactor)}%`,
                    width: `${star.size}px`,
                    height: `${star.size}px`,
                    boxShadow: star.size > 1.5 ? '0 0 10px rgba(255, 255, 255, 0.8)' : 'none'
                }"
            ></div>
        </div>

        <!-- Dual-Tier Navigation System -->
        <div class="relative z-10">
            <!-- Tier 1: Cosmic Command Bar -->
            <div class="bg-header_background backdrop-blur-md bg-opacity-90 border-b border-primary/30">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Logo Section -->
                        <div class="flex items-center shrink-0">
                            <Link :href="route('index')" class="group">
                                <div class="relative">
                                    <ApplicationLogo class="w-auto h-9 text-white fill-current transition-all duration-300 group-hover:scale-105" />
                                    <div class="absolute inset-0 bg-primary opacity-0 blur-xl rounded-full group-hover:opacity-20 transition-opacity duration-700 animate-pulse-slow"></div>
                                </div>
                            </Link>
                        </div>

                        <!-- Center Search (Desktop Only) -->
                        <div class="hidden md:flex items-center justify-center flex-1 mx-8">
                            <div class="relative w-4/5 group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <Search class="w-5 h-5 text-secondary/80 transition duration-300 group-focus-within:text-secondary" />
                                </div>
                                <input 
                                    type="text" 
                                    class="w-full pl-10 pr-4 py-2 bg-dark/50 border border-white/10 rounded-full backdrop-blur-md text-white transition-all duration-300 
                                        focus:ring-2 focus:ring-primary/70 focus:bg-dark/70 focus:border-primary/30 focus:shadow-glow-primary"
                                    placeholder="Search games, items, services..."
                                />
                                <div class="absolute inset-0 rounded-full opacity-0 backdrop-blur-sm bg-secondary/5 group-hover:opacity-100 group-focus-within:opacity-100 transition-all duration-500"></div>
                            </div>
                        </div>

                        <!-- Right Controls -->
                        <div class="hidden md:flex items-center gap-4">
                            <!-- Language Selector -->
                            <div class="relative">
                                <button 
                                    @click="toggleLanguageSelector"
                                    class="flex items-center gap-2 text-white/90 hover:text-white rounded-full px-3 py-1.5 hover:bg-white/10 transition duration-200"
                                >
                                    <Globe class="w-4 h-4" />
                                    <span>{{ currentLanguage }}</span>
                                    <ChevronDown class="w-3 h-3 transition-transform" :class="{ 'rotate-180': showLanguageSelector }" />
                                </button>
                                
                                <!-- Language Dropdown -->
                                <div v-if="showLanguageSelector" class="absolute right-0 mt-2 w-40 rounded-lg overflow-hidden backdrop-blur-xl bg-dark/90 border border-white/10 shadow-float transition-all origin-top duration-200 z-50">
                                    <div class="py-1">
                                        <button 
                                            v-for="lang in ['ID', 'EN']" 
                                            :key="lang"
                                            @click="currentLanguage = lang; showLanguageSelector = false"
                                            class="flex items-center w-full px-4 py-2 text-sm hover:bg-primary/20 text-white/90 hover:text-white"
                                            :class="{ 'bg-primary/10': currentLanguage === lang }"
                                        >
                                            {{ lang }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Controls -->
                        <div class="flex items-center md:hidden gap-3">
                            <!-- Mobile Search Button -->
                            <button 
                                @click="toggleSearch"
                                class="flex items-center justify-center p-2 text-white/80 hover:text-white rounded-full hover:bg-white/10 transition duration-200"
                            >
                                <Search class="w-5 h-5" />
                            </button>
                            
                            <!-- Mobile Language Toggle -->
                            <button 
                                @click="toggleLanguageSelector"
                                class="flex items-center justify-center p-2 text-white/80 hover:text-white rounded-full hover:bg-white/10 transition duration-200"
                            >
                                <Globe class="w-5 h-5" />
                            </button>
                            
                            <!-- Mobile Menu Button -->
                            <button 
                                @click="toggleMobileMenu"
                                class="flex items-center justify-center p-2 text-white/80 hover:text-white rounded-full hover:bg-white/10 transition duration-200"
                            >
                                <Menu v-if="!showingNavigationDropdown" class="w-5 h-5" />
                                <X v-else class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asteroid Belt Divider -->
            <div class="h-0.5 bg-gradient-to-r from-transparent via-secondary/30 to-transparent overflow-hidden relative">
                <div class="absolute inset-0 flex items-center justify-around">
                    <div v-for="i in 30" :key="i" class="w-1 h-1 rounded-full bg-secondary opacity-50" :style="`left: ${i * 3.33}%`"></div>
                </div>
            </div>

            <!-- Tier 2: Stellar Navigation (Desktop Only) -->
            <div class="hidden md:block bg-dark/80 backdrop-blur-md border-b border-white/5">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-between">
                        <!-- Navigation Links -->
                        <div class="flex space-x-6">
                            <NavLink
                                v-for="link in navigationLinks"
                                :key="link.name"
                                :href="route(link.route)"
                                :active="route().current(link.route)"
                                class="group relative flex items-center px-2 py-3 text-white/80 hover:text-white transition-colors duration-300"
                            >
                                <!-- Icon would go here, using placeholder -->
                                <span class="mr-2 text-secondary group-hover:text-primary transition duration-300">
                                    <component :is="link.icon" class="w-4 h-4" v-if="false" />
                                    <div class="w-4 h-4 bg-secondary/50 rounded-full group-hover:bg-primary/80 transition-colors duration-300"></div>
                                </span>
                                <span>{{ link.name }}</span>
                                
                                <!-- Hover Trail Effect -->
                                <div class="absolute -bottom-[1px] left-0 w-0 h-0.5 bg-primary transition-all duration-300 opacity-0 group-hover:w-full group-hover:opacity-100"></div>
                                
                                <!-- Active Neutron Star Effect -->
                                <div v-if="route().current(link.route)" class="absolute -bottom-[1px] left-0 w-full h-0.5 bg-primary shadow-glow-primary animate-pulse-slow"></div>
                            </NavLink>
                        </div>

                        <!-- Auth Buttons -->
                        <div class="flex items-center space-x-4">
                            <Link
                                :href="route('login')"
                                class="flex items-center px-4 py-1.5 text-white/90 hover:text-white border border-white/20 rounded-full hover:border-primary/50 hover:bg-primary/10 transition duration-300 hover:shadow-glow-primary"
                            >
                                <User class="w-4 h-4 mr-2" />
                                <span>Login</span>
                            </Link>
                            <Link
                                :href="route('register')"
                                class="flex items-center px-4 py-1.5 bg-primary hover:bg-primary-hover text-white rounded-full transition duration-300 hover:shadow-glow-primary"
                            >
                                <Rocket class="w-4 h-4 mr-2" />
                                <span>Register</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Search Panel -->
            <div
                v-if="showSearch"
                class="md:hidden fixed inset-0 z-50 bg-dark/95 backdrop-blur-xl flex flex-col"
            >
                <div class="p-4 flex justify-between items-center border-b border-white/10">
                    <h3 class="text-white font-medium">Search</h3>
                    <button @click="toggleSearch" class="text-white/80 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <div class="p-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <Search class="w-5 h-5 text-white/50" />
                        </div>
                        <input 
                            type="text" 
                            class="w-full pl-10 pr-4 py-3 bg-dark/70 border border-white/10 rounded-lg text-white focus:ring-2 focus:ring-primary/70 focus:border-primary/30"
                            placeholder="Search games, items, services..." 
                            autofocus
                        />
                    </div>
                </div>
            </div>

            <!-- Mobile Language Panel -->
            <div
                v-if="showLanguageSelector && window.innerWidth < 768"
                class="md:hidden fixed inset-x-0 bottom-0 z-50 bg-dark/95 backdrop-blur-xl rounded-t-xl"
            >
                <div class="p-4 border-b border-white/10">
                    <div class="w-12 h-1 bg-white/20 rounded-full mx-auto"></div>
                </div>
                <div class="p-4">
                    <h3 class="text-white font-medium mb-4">Select Language</h3>
                    <div class="space-y-2">
                        <button 
                            v-for="lang in ['ID', 'EN']" 
                            :key="lang"
                            @click="currentLanguage = lang; showLanguageSelector = false"
                            class="flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-white/5 text-white"
                            :class="{ 'bg-primary/10 border border-primary/30': currentLanguage === lang }"
                        >
                            <span>{{ lang === 'ID' ? 'Bahasa Indonesia' : 'English' }}</span>
                            <div v-if="currentLanguage === lang" class="w-2 h-2 bg-primary rounded-full"></div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Panel -->
            <div
                v-if="showingNavigationDropdown"
                class="md:hidden fixed inset-0 z-40 bg-dark/95 backdrop-blur-xl overflow-y-auto"
            >
                <div class="p-4 flex justify-between items-center border-b border-white/10">
                    <Link :href="route('index')" @click="showingNavigationDropdown = false">
                        <ApplicationLogo class="w-auto h-8 text-white fill-current" />
                    </Link>
                    <button @click="toggleMobileMenu" class="text-white/80 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <!-- Mobile Nav Links -->
                <div class="px-4 pt-6 pb-8 space-y-2">
                    <ResponsiveNavLink
                        v-for="link in navigationLinks"
                        :key="link.name"
                        :href="route(link.route)"
                        :active="route().current(link.route)"
                        @click="showingNavigationDropdown = false"
                        class="rounded-lg flex items-center px-4 py-3"
                    >
                        <!-- Icon placeholder -->
                        <div class="mr-3 w-8 h-8 rounded-full bg-dark/50 border border-white/10 flex items-center justify-center">
                            <div class="w-4 h-4 bg-secondary/70 rounded-full"></div>
                        </div>
                        <span>{{ link.name }}</span>
                    </ResponsiveNavLink>
                </div>
                
                <!-- Mobile Auth Buttons -->
                <div class="px-4 py-6 border-t border-white/10 space-y-3">
                    <Link
                        :href="route('login')"
                        @click="showingNavigationDropdown = false"
                        class="flex items-center justify-center w-full px-4 py-3 text-white bg-dark/70 border border-white/20 rounded-lg hover:bg-dark/90 hover:border-primary/40 transition duration-200"
                    >
                        <User class="w-5 h-5 mr-2" />
                        <span>Login</span>
                    </Link>
                    <Link
                        :href="route('register')"
                        @click="showingNavigationDropdown = false"
                        class="flex items-center justify-center w-full px-4 py-3 text-white bg-primary hover:bg-primary-hover rounded-lg transition duration-200"
                    >
                        <Rocket class="w-5 h-5 mr-2" />
                        <span>Register</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="flex-grow">
            <slot />
        </main>
    </div>
</template>

<style>
.animate-pulse-slow {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.5;
    }
    50% {
        opacity: 1;
    }
}
</style>

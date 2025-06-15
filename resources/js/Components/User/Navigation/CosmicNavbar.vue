<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const showMobileMenu = ref(false);
const page = usePage();

const toggleMobileMenu = () => {
    showMobileMenu.value = !showMobileMenu.value;
};
</script>

<template>
    <nav class="bg-header_background border-b border-primary/20 sticky top-0 z-50">
        <!-- Desktop Navigation -->
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <!-- Logo -->
            <div>
                <Link :href="route('index')" class="text-white font-bold text-xl flex items-center">
                    <img :src="page.props.web_details.meta_logo" alt="Logo" class="h-8 mr-2" />
                    {{ page.props.web_details.meta_title }}
                </Link>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="toggleMobileMenu" class="lg:hidden text-gray-300 hover:text-white focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        
        <!-- Desktop Navigation -->
        <div class="hidden lg:flex lg:items-center lg:space-x-8">
            <Link
                :href="route('index')"
                class="text-gray-300 hover:text-primary transition-colors duration-200 font-medium"
                :class="{ 'text-primary': $page.component === 'Index' }"
            >
                Home
            </Link>
            <Link
                :href="route('leaderboard')"
                class="text-gray-300 hover:text-primary transition-colors duration-200 font-medium"
                :class="{ 'text-primary': $page.component === 'Leaderboard' }"
            >
                Leaderboard
            </Link>
            <Link
                :href="route('cek-transaksi')"
                class="text-gray-300 hover:text-primary transition-colors duration-200 font-medium"
                :class="{ 'text-primary': $page.component === 'CekTransaksi' }"
            >
                Cek Transaksi
            </Link>
            <Link
                :href="route('api-docs')"
                class="text-gray-300 hover:text-primary transition-colors duration-200 font-medium"
                :class="{ 'text-primary': $page.component === 'ApiDocs' }"
            >
                API Docs
            </Link>
            
            <!-- Calculator Dropdown -->
            <div class="relative group">
                <button class="text-gray-300 hover:text-primary transition-colors duration-200 font-medium focus:outline-none">
                    Calculator
                </button>
                <div class="absolute left-0 mt-2 w-48 bg-dark-card border border-primary/20 rounded-md shadow-lg z-10 hidden group-hover:block">
                    <Link
                        :href="route('calculator.winrate')"
                        class="block px-4 py-2 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                    >
                        Winrate
                    </Link>
                    <Link
                        :href="route('calculator.magic-wheel')"
                        class="block px-4 py-2 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                    >
                        Magic Wheel
                    </Link>
                    <Link
                        :href="route('calculator.zodiac')"
                        class="block px-4 py-2 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                    >
                        Zodiac
                    </Link>
                </div>
            </div>

            <!-- Authentication Buttons -->
            <div v-if="$page.props.auth.user">
                <Link :href="route('dashboard')" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Dashboard
                </Link>
            </div>
            <div v-else class="space-x-4">
                <Link :href="route('login')" class="text-gray-300 hover:text-primary transition-colors duration-200 font-medium">
                    Login
                </Link>
                <Link :href="route('register')" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Register
                </Link>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div v-if="showMobileMenu" class="lg:hidden">
            
            <Link
                :href="route('index')"
                class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                :class="{ 'text-primary bg-dark-lighter': $page.component === 'Index' }"
                @click="showMobileMenu = false"
            >
                Home
            </Link>
            <Link
                :href="route('leaderboard')"
                class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                :class="{ 'text-primary bg-dark-lighter': $page.component === 'Leaderboard' }"
                @click="showMobileMenu = false"
            >
                Leaderboard
            </Link>
            <Link
                :href="route('cek-transaksi')"
                class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                :class="{ 'text-primary bg-dark-lighter': $page.component === 'CekTransaksi' }"
                @click="showMobileMenu = false"
            >
                Cek Transaksi
            </Link>
            
            <Link
                :href="route('api-docs')"
                class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                :class="{ 'text-primary bg-dark-lighter': $page.component === 'ApiDocs' }"
                @click="showMobileMenu = false"
            >
                API Documentation
            </Link>
            
            <!-- Calculator Mobile Menu -->
            <div class="border-t border-primary/20">
                <Link
                    :href="route('calculator.winrate')"
                    class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                    @click="showMobileMenu = false"
                >
                    Calculator - Winrate
                </Link>
                <Link
                    :href="route('calculator.magic-wheel')"
                    class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                    @click="showMobileMenu = false"
                >
                    Calculator - Magic Wheel
                </Link>
                <Link
                    :href="route('calculator.zodiac')"
                    class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200"
                    @click="showMobileMenu = false"
                >
                    Calculator - Zodiac
                </Link>
            </div>

            <!-- Authentication Links -->
            <div v-if="$page.props.auth.user" class="border-t border-primary/20">
                <Link :href="route('dashboard')" class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200" @click="showMobileMenu = false">
                    Dashboard
                </Link>
            </div>
            <div v-else class="border-t border-primary/20">
                <Link :href="route('login')" class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200" @click="showMobileMenu = false">
                    Login
                </Link>
                <Link :href="route('register')" class="block px-4 py-3 text-gray-300 hover:text-primary hover:bg-dark-lighter transition-colors duration-200" @click="showMobileMenu = false">
                    Register
                </Link>
            </div>
        </div>
    </nav>
</template>

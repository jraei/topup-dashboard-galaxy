
<script setup>
// Import necessary dependencies
</script>

<template>
    <div class="relative w-full h-auto bg-dark overflow-hidden">
        <!-- Overlay gradient to help text contrast -->
        <div class="absolute inset-0 bg-gradient-to-r from-dark via-transparent to-dark z-10"></div>
        
        <!-- Cosmic noise texture overlay -->
        <div class="absolute inset-0 opacity-5 z-20">
            <svg width="100%" height="100%">
                <filter id="noise">
                    <feTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/>
                    <feColorMatrix type="saturate" values="0"/>
                </filter>
                <rect width="100%" height="100%" filter="url(#noise)" opacity="0.5"/>
            </svg>
        </div>
        
        <!-- Banner image -->
        <img 
            src="/img/footerBanner/bg-footer.webp" 
            alt="Cosmic Promotions" 
            class="w-full h-auto object-cover" 
            loading="lazy"
            fetchpriority="low"
        />
        
        <!-- Text overlay -->
        <div class="absolute inset-0 flex items-center z-30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full">
                <div class="max-w-lg">
                    <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">
                        Cosmic Promotions
                    </h3>
                    <p class="text-white/80 mb-6">
                        Get special deals across multiple top-up options
                    </p>
                    <a 
                        href="#" 
                        class="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-full inline-flex items-center transition-all duration-200 transform hover:translate-x-1"
                    >
                        Explore Offers
                        <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            class="h-4 w-4 ml-2" 
                            viewBox="0 0 20 20" 
                            fill="currentColor"
                        >
                            <path 
                                fill-rule="evenodd" 
                                d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" 
                                clip-rule="evenodd" 
                            />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

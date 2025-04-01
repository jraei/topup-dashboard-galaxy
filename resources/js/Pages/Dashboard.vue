<template>
    <AuthenticatedLayout>
        <div class="bg-[#111827] min-h-screen">
            <!-- Main Container -->
            <div class="content-body">
                <!-- Hero Carousel Section -->
                <div class="relative mt-4 overflow-hidden rounded-lg">
                    <div id="cyberCarousel" ref="carousel" class="relative">
                        <div
                            class="relative overflow-hidden rounded-lg carousel-inner"
                        >
                            <div
                                v-for="(slide, index) in banner"
                                :key="index"
                                class="relative carousel-item"
                                :class="{ active: currentSlide === index }"
                            >
                                <img
                                    :src="slide.path"
                                    class="object-cover w-full transition-transform duration-700 rounded d-block"
                                    style="min-height: 200px; max-height: 450px"
                                    loading="lazy"
                                />
                                <div class="carousel-overlay"></div>
                            </div>
                        </div>

                        <div
                            class="absolute flex justify-center w-full gap-2 carousel-indicators bottom-4"
                        >
                            <button
                                v-for="(slide, index) in banner"
                                :key="`indicator-${index}`"
                                type="button"
                                :class="{ active: currentSlide === index }"
                                class="w-3 h-3 transition-all duration-300 rounded-full bg-white/50"
                                @click="goToSlide(index)"
                            ></button>
                        </div>
                    </div>
                </div>

                <!-- Categories Section -->
                <div class="container px-4 py-8 mx-auto">
                    <h2
                        class="mb-6 text-2xl font-bold font-orbitron cyber-text"
                    >
                        Featured Categories
                    </h2>

                    <div
                        class="flex gap-4 pb-4 overflow-x-auto hide-scrollbar snap-x"
                    >
                        <div
                            v-for="(category, index) in categories"
                            :key="`category-${index}`"
                            class="category-pill"
                            :class="{ active: activeCategory === category.id }"
                            @click="setActiveCategory(category.id)"
                        >
                            {{ category.name }}
                        </div>
                    </div>
                </div>

                <!-- Mobile Game Section -->
                <section
                    v-if="activeCategory === 'mobile-game'"
                    class="container px-4 mx-auto mb-10 category-section"
                >
                    <h4 class="mb-6 text-xl font-bold font-orbitron cyber-text">
                        MOBILE GAME
                    </h4>
                    <div
                        class="grid grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6"
                    >
                        <div
                            v-for="category in filteredCategories('game')"
                            :key="category.kode"
                            class="cyber-card"
                        >
                            <a :href="`/order/${category.kode}`" class="block">
                                <div class="overflow-hidden aspect-square">
                                    <img
                                        class="object-cover w-full h-full transition-transform duration-500"
                                        :src="category.thumbnail"
                                        loading="lazy"
                                        :alt="category.nama"
                                    />
                                </div>
                                <div
                                    class="absolute bottom-0 left-0 w-full p-3 card-content"
                                >
                                    <p class="text-base font-bold text-white">
                                        {{ category.nama }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- PC Game Section -->
                <section
                    v-else-if="activeCategory === 'pc-game'"
                    class="container px-4 mx-auto mb-10 category-section"
                >
                    <h4 class="mb-6 text-xl font-bold font-orbitron cyber-text">
                        PC GAME
                    </h4>
                    <div
                        class="grid grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6"
                    >
                        <div
                            v-for="category in filteredCategories('gamepc')"
                            :key="category.kode"
                            class="cyber-card"
                        >
                            <a :href="`/order/${category.kode}`" class="block">
                                <div class="overflow-hidden aspect-square">
                                    <img
                                        class="object-cover w-full h-full transition-transform duration-500"
                                        :src="category.thumbnail"
                                        loading="lazy"
                                        :alt="category.nama"
                                    />
                                </div>
                                <div
                                    class="absolute bottom-0 left-0 w-full p-3 card-content"
                                >
                                    <p class="text-base font-bold text-white">
                                        {{ category.nama }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Social Media Section -->
                <section
                    v-else-if="activeCategory === 'social-media'"
                    class="container px-4 mx-auto mb-10 category-section"
                >
                    <h4 class="mb-6 text-xl font-bold font-orbitron cyber-text">
                        KEBUTUHAN SOSMED
                    </h4>
                    <div
                        class="grid grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6"
                    >
                        <div
                            v-for="category in filteredCategories('sosmed')"
                            :key="category.kode"
                            class="cyber-card"
                        >
                            <a :href="`/order/${category.kode}`" class="block">
                                <div class="overflow-hidden aspect-square">
                                    <img
                                        class="object-cover w-full h-full transition-transform duration-500"
                                        :src="category.thumbnail"
                                        loading="lazy"
                                        :alt="category.nama"
                                    />
                                </div>
                                <div
                                    class="absolute bottom-0 left-0 w-full p-3 card-content"
                                >
                                    <p class="text-base font-bold text-white">
                                        {{ category.nama }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Streaming App Section -->
                <section
                    v-else-if="activeCategory === 'streaming'"
                    class="container px-4 mx-auto mb-10 category-section"
                >
                    <h4 class="mb-6 text-xl font-bold font-orbitron cyber-text">
                        STREAMING APP
                    </h4>
                    <div
                        class="grid grid-cols-2 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6"
                    >
                        <div
                            v-for="category in filteredCategories(
                                'streamingapp'
                            )"
                            :key="category.kode"
                            class="cyber-card"
                        >
                            <a :href="`/order/${category.kode}`" class="block">
                                <div class="overflow-hidden aspect-square">
                                    <img
                                        class="object-cover w-full h-full transition-transform duration-500"
                                        :src="category.thumbnail"
                                        loading="lazy"
                                        :alt="category.nama"
                                    />
                                </div>
                                <div
                                    class="absolute bottom-0 left-0 w-full p-3 card-content"
                                >
                                    <p class="text-base font-bold text-white">
                                        {{ category.nama }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Search Results Section -->
            <section class="px-2 pb-8" :class="{ hidden: !showSearchResults }">
                <h4 class="mb-2 text-lg font-orbitron cyber-text">
                    Hasil Pencarian
                </h4>
                <br />
                <div class="product productresultsearch row">
                    <!-- Search results will be dynamically inserted here -->
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, computed, watch } from "vue";

// Props that would be passed from Inertia
const props = defineProps({
    banner: {
        type: Array,
        required: true,
        default: () => [],
    },
    kategori: {
        type: Array,
        required: true,
        default: () => [],
    },
});

// State variables
const currentSlide = ref(0);
const carouselInterval = ref(null);
const activeCategory = ref("mobile-game");
const showSearchResults = ref(false);
const searchQuery = ref("");
const searchResults = ref([]);
const carousel = ref(null);

// Predefined category list
const categories = ref([
    { id: "mobile-game", name: "MOBILE GAME" },
    { id: "pc-game", name: "PC GAME" },
    { id: "social-media", name: "KEBUTUHAN SOSMED" },
    { id: "streaming", name: "STREAMING APP" },
]);

const banner = ref([
    {
        id: "banner1",
        path: "/img/banner/banner.jpg",
    },
    {
        id: "banner2",
        path: "/img/banner/banner2.webp",
    },
    {
        id: "banner3",
        path: "/img/banner/banner3.webp",
    },
    {
        id: "banner4",
        path: "/img/banner/banner4.webp",
    },
]);

// Filtered categories based on type
const filteredCategories = (type) => {
    return props.kategori.filter((category) => category.tipe === type);
};

// Carousel functions
const startCarousel = () => {
    if (carouselInterval.value) clearInterval(carouselInterval.value);

    carouselInterval.value = setInterval(() => {
        nextSlide();
    }, 7000); // Auto rotate every 7 seconds
};

const stopCarousel = () => {
    if (carouselInterval.value) {
        clearInterval(carouselInterval.value);
        carouselInterval.value = null;
    }
};

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % props.banner.length;
};

const prevSlide = () => {
    currentSlide.value =
        (currentSlide.value - 1 + props.banner.length) % props.banner.length;
};

const goToSlide = (index) => {
    currentSlide.value = index;
    stopCarousel();
    startCarousel();
};

// Set active category
const setActiveCategory = (categoryId) => {
    activeCategory.value = categoryId;
};

// Search functionality
const performSearch = async () => {
    if (searchQuery.value.length < 1) {
        showSearchResults.value = false;
        return;
    }

    try {
        const response = await fetch("/cari/index", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({ data: searchQuery.value }),
        });

        if (response.ok) {
            const html = await response.text();
            document.querySelector(".productresultsearch").innerHTML = html;
            showSearchResults.value = true;
        }
    } catch (error) {
        console.error("Search error:", error);
    }
};

// Apply card hover effects
const initCardHoverEffects = () => {
    const cards = document.querySelectorAll(".cyber-card");

    cards.forEach((card) => {
        card.addEventListener("mousemove", (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const xPercent = (x / rect.width - 0.5) * 20;
            const yPercent = (y / rect.height - 0.5) * 20;

            card.style.transform = `perspective(1000px) rotateY(${xPercent}deg) rotateX(${-yPercent}deg) translateZ(10px)`;
        });

        card.addEventListener("mouseleave", () => {
            card.style.transform =
                "perspective(1000px) rotateY(0) rotateX(0) translateZ(0)";
        });
    });
};

// Add parallax effect to carousel
const initCarouselParallax = () => {
    if (!carousel.value) return;

    carousel.value.addEventListener("mousemove", (e) => {
        const items = carousel.value.querySelectorAll(
            ".carousel-item.active img"
        );
        items.forEach((img) => {
            const rect = carousel.value.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const xPercent = (x / rect.width - 0.5) * 10;
            const yPercent = (y / rect.height - 0.5) * 10;

            img.style.transform = `translate(${xPercent}px, ${yPercent}px) scale(1.05)`;
        });
    });

    carousel.value.addEventListener("mouseleave", () => {
        const items = carousel.value.querySelectorAll(".carousel-item img");
        items.forEach((img) => {
            img.style.transform = "translate(0, 0) scale(1)";
        });
    });
};

// Initialization on component mount
onMounted(() => {
    // Start carousel
    startCarousel();

    // Initialize hover effects
    initCardHoverEffects();

    // Initialize carousel parallax
    initCarouselParallax();

    // Handle keyup event for search
    const searchInput = document.getElementById("searchProds");
    if (searchInput) {
        searchInput.addEventListener("keyup", () => {
            searchQuery.value = searchInput.value;

            if (searchQuery.value.length < 1) {
                showSearchResults.value = false;
                return;
            }

            // Debounce search
            let timer;
            clearTimeout(timer);
            timer = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    // Cleanup
    return () => {
        stopCarousel();
    };
});
</script>

<style>
/* Base Cyberpunk Styles */
.font-orbitron {
    font-family: "Orbitron", sans-serif;
}

.text-glow {
    text-shadow: 0 0 10px rgba(110, 231, 183, 0.8),
        0 0 20px rgba(110, 231, 183, 0.5);
}

.neon-border {
    box-shadow: 0 0 5px #34d399, 0 0 10px rgba(110, 231, 183, 0.5);
}

.glass {
    backdrop-filter: blur(8px);
    background: rgba(31, 41, 55, 0.4);
    border: 1px solid rgba(110, 231, 183, 0.2);
}

.cyber-gradient {
    background: linear-gradient(135deg, #34d399 0%, #6ee7b7 100%);
}

.cyber-text {
    background: linear-gradient(90deg, #34d399, #6ee7b7);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

/* Card Styles */
.cyber-card {
    @apply relative overflow-hidden rounded-lg transition-all duration-300 bg-[#1F2937];
    border: 1px solid rgba(110, 231, 183, 0.2);
    transform-style: preserve-3d;
    perspective: 1000px;
}

.cyber-card:hover {
    transform: translateY(-5px) rotateX(5deg) rotateY(5deg);
    box-shadow: 0 10px 25px rgba(52, 211, 153, 0.3);
    border: 1px solid rgba(110, 231, 183, 0.5);
}

.cyber-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        45deg,
        transparent 60%,
        rgba(110, 231, 183, 0.2) 70%,
        transparent 80%
    );
    opacity: 0;
    transition: opacity 0.5s ease;
    z-index: 2;
}

.cyber-card:hover::before {
    opacity: 1;
    animation: hologramScan 2s linear infinite;
}

@keyframes hologramScan {
    0% {
        background-position: -100% -100%;
    }
    100% {
        background-position: 200% 200%;
    }
}

.cyber-card:hover img {
    transform: scale(1.08);
}

.cyber-card .card-content {
    background: linear-gradient(
        to top,
        rgba(31, 41, 55, 0.95) 0%,
        rgba(31, 41, 55, 0.7) 50%,
        rgba(31, 41, 55, 0) 100%
    );
}

/* Animation Keyframes */
@keyframes pulse {
    0% {
        opacity: 0.8;
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0.8;
    }
}

@keyframes borderScan {
    0% {
        background-position: 0% 0%;
    }
    100% {
        background-position: 100% 100%;
    }
}

/* Category Pill Styles */
.category-pill {
    @apply px-6 py-3 rounded-full text-white font-orbitron transition-all duration-300 cursor-pointer;
    background: rgba(31, 41, 55, 0.7);
    border: 1px solid rgba(110, 231, 183, 0.3);
}

.category-pill:hover,
.category-pill.active {
    @apply neon-border;
    background: rgba(52, 211, 153, 0.2);
    transform: scale(1.05);
}

.category-pill.active {
    animation: pulse 2s infinite;
}

/* Custom Mobile Styling */
.mobile {
    border-radius: 5px 5px 10px 10px;
    height: 50px;
    padding: 7px;
    background: linear-gradient(
        180deg,
        rgba(13, 31, 120, 0) 3%,
        rgb(44, 62, 80) 80%
    );
    margin-top: -50px;
}

/* Carousel Styles */
.carousel-item {
    position: relative;
    overflow: hidden;
    display: none;
}

.carousel-item.active {
    display: block;
}

.carousel-item::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(52, 211, 153, 0.2) 0%,
        rgba(110, 231, 183, 0.1) 100%
    );
    pointer-events: none;
}

.carousel-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(31, 41, 55, 0) 0%,
        rgba(31, 41, 55, 0.3) 50%,
        rgba(31, 41, 55, 0.8) 100%
    );
    pointer-events: none;
}

/* Image Effects */
.img-hover-effect {
    transition: transform 0.5s ease, filter 0.5s ease;
}

.img-hover-effect:hover {
    filter: brightness(1.2);
}

/* Hide scrollbar but allow scrolling */
.hide-scrollbar {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

/* Carousel Indicators */
.carousel-indicators button {
    @apply w-3 h-3 rounded-full bg-white/50 transition-all duration-300;
}

.carousel-indicators button.active {
    @apply bg-white w-6 neon-border;
}
</style>

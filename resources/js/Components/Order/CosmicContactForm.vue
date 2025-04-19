
<script setup>
import { ref, computed, watch, onMounted } from "vue";
import CosmicCard from "./CosmicCard.vue";

// Default countries (extend as needed)
const countries = [
    { code: "ID", name: "Indonesia", dial: "+62", flag: "🇮🇩" },
    { code: "US", name: "United States", dial: "+1", flag: "🇺🇸" },
    { code: "IN", name: "India", dial: "+91", flag: "🇮🇳" },
    { code: "MY", name: "Malaysia", dial: "+60", flag: "🇲🇾" },
    { code: "SG", name: "Singapore", dial: "+65", flag: "🇸🇬" },
    { code: "GB", name: "UK", dial: "+44", flag: "🇬🇧" },
    // ...add more as needed
];
const email = defineModel('email');
const phone = defineModel('phone');
const selectedCountry = ref(countries[0]);

const emailValid = computed(() =>
    email.value && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)
);

const formattedPhone = computed({
    get() {
        let base = selectedCountry.value.dial;
        let value = phone.value?.replace(/[^0-9]/g, '') || '';
        if (base === '+62') {
            // Indonesia: never double 0
            if (value.startsWith('0')) value = value.slice(1);
        }
        return base + value;
    },
    set(val) {
        let country = countries.find(c => val.startsWith(c.dial)) || countries[0];
        selectedCountry.value = country;
        phone.value = val.replace(country.dial, '');
    }
});

// FOCUS/active UI
const phoneRef = ref(null);
const focusSatOrbit = ref(false);

function openCountrySelect() {
    focusSatOrbit.value = true;
}
function closeCountrySelect() {
    focusSatOrbit.value = false;
}
</script>

<template>
    <CosmicCard title="Contact Details" :step-number="5">
        <form class="grid gap-6 md:grid-cols-2 relative">
            <!-- Email field with cosmic focus -->
            <div class="relative">
                <label for="email" class="block mb-1 text-white font-semibold">Email (optional)</label>
                <input
                    id="email"
                    v-model="email"
                    type="email"
                    placeholder="your@email.com"
                    class="w-full rounded-xl border-none bg-content_background/70 text-white px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary transition-shadow
                        focus:shadow-[0_0_16px_0_#33C3F0]
                        outline-none placeholder:text-secondary/40
                        animate-meteor-trail"
                    autocomplete="off"
                />

                <!-- Cosmic Transaction Alert -->
                <transition
                    enter-active-class="transition-all duration-300 ease-in"
                    leave-active-class="transition-all duration-200 ease-out"
                    enter-from-class="opacity-0 translate-y-6"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <div v-if="emailValid" class="flex items-center mt-2 px-2 py-1 rounded bg-secondary/20 text-xs text-secondary animate-pulse">
                        <span class="mr-2 animate-supernova">
                            <svg width="20" height="20" viewBox="0 0 20 20" class="inline align-middle">
                                <circle cx="10" cy="10" r="6" fill="#33C3F0" opacity="0.7"/>
                                <g>
                                  <ellipse cx="10" cy="10" rx="3" ry="7" fill="none" stroke="#fff" stroke-width="1"/>
                                  <ellipse cx="10" cy="10" rx="7" ry="3" fill="none" stroke="#fff" stroke-width="1"/>
                                </g>
                            </svg>
                        </span>
                        Transaction proof will be sent to above email (optional)
                    </div>
                </transition>
            </div>

            <!-- Intl Phone field -->
            <div class="relative">
                <label for="phone" class="block mb-1 text-white font-semibold">WhatsApp Number</label>
                <div class="flex items-center gap-2 bg-content_background/70 rounded-xl px-2 py-2">
                    <!-- Country Dropdown -->
                    <div class="relative group">
                        <button
                            type="button"
                            class="flex items-center gap-1 text-white px-2 py-1 rounded-lg bg-secondary/30 focus:bg-primary/30 transition group"
                            @click="openCountrySelect"
                            :aria-expanded="focusSatOrbit"
                        >
                            <span class="text-lg leading-none">{{ selectedCountry.flag }}</span>
                            <span class="font-medium tracking-wide">{{ selectedCountry.dial }}</span>
                            <svg class="ml-1 w-4 h-4 fill-secondary" viewBox="0 0 24 24">
                                <path d="M7 10l5 5 5-5H7z"/>
                            </svg>
                            <!-- Orbiting satellite animation -->
                            <span class="absolute -right-2 -top-2 w-3 h-3 bg-primary rounded-full animate-orbit"></span>
                        </button>
                        <div
                            v-if="focusSatOrbit"
                            @mouseleave="closeCountrySelect"
                            class="absolute z-20 mt-2 left-0 w-32 bg-dark/95 rounded-xl shadow-lg border border-secondary/20 overflow-auto max-h-48 animate-fade-in"
                        >
                            <div
                                v-for="country in countries"
                                :key="country.code"
                                @click="selectedCountry = country; closeCountrySelect();"
                                class="flex items-center p-2 cursor-pointer hover:bg-primary/15 text-white"
                            >
                                <span class="text-lg mr-1">{{ country.flag }}</span>
                                <span>{{ country.name }}</span>
                                <span class="ml-auto text-xs text-secondary">{{ country.dial }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Phone input -->
                    <input
                        id="phone"
                        ref="phoneRef"
                        v-model="phone"
                        type="tel"
                        :placeholder="selectedCountry.dial + ' 812-1234-5678'"
                        class="bg-transparent w-full border-none focus:ring-0 pl-2 text-white placeholder:text-secondary/30"
                        autocomplete="off"
                    />
                </div>
                <p class="text-xs text-secondary/70 mt-1">This number will be contacted if issues occur.</p>
            </div>

            <!-- Constellation background (low opacity, behind form) -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="h-full w-full bg-[radial-gradient(circle_at_60%_5%,rgba(155,135,245,0.05)_0px,transparent_80%)]" />
            </div>
        </form>
    </CosmicCard>
</template>

<style scoped>
.animate-meteor-trail:focus {
    box-shadow: 0 0 32px 2px #33C3F099, 0 0 4px 1.5px #9b87f5;
    transition: box-shadow 0.3s;
}
@keyframes supernova {
    0%,100% { filter: drop-shadow(0 0 4px #33C3F0c6);}
    50% { filter: drop-shadow(0 0 16px #33C3F0fa);}
}
.animate-supernova {
    animation: supernova 1.5s infinite alternate;
}
@keyframes orbit {
    0% {transform: rotate(0deg) translateX(0);}
    100% {transform: rotate(360deg) translateX(4px);}
}
.animate-orbit {
    animation: orbit 2.9s linear infinite;
}
</style>

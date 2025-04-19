
<script setup>
import { ref, computed, watch, nextTick } from "vue";
import CosmicCard from "@/Components/Order/CosmicCard.vue";
import { useToast } from "@/Composables/useToast";

// --- Countries mini-list (for brevity; full list can be expanded if needed) ---
const countries = [
    { code: "ID", name: "Indonesia", dial_code: "+62", flag: "🇮🇩", min_len: 9 },
    { code: "MY", name: "Malaysia", dial_code: "+60", flag: "🇲🇾", min_len: 9 },
    { code: "SG", name: "Singapore", dial_code: "+65", flag: "🇸🇬", min_len: 8 },
    { code: "TH", name: "Thailand", dial_code: "+66", flag: "🇹🇭", min_len: 8 },
    { code: "VN", name: "Vietnam", dial_code: "+84", flag: "🇻🇳", min_len: 9 },
    { code: "PH", name: "Philippines", dial_code: "+63", flag: "🇵🇭", min_len: 9 },
    { code: "US", name: "United States", dial_code: "+1", flag: "🇺🇸", min_len: 10 },
];

const props = defineProps({
    initialEmail: String,
    initialPhone: String,
    initialCountry: String,
});
const emit = defineEmits(["update:contact"]);

const email = ref(props.initialEmail || "");
const phone = ref(props.initialPhone || "");
const country = ref(props.initialCountry || "ID");

watch([email, phone, country], () => {
    emit("update:contact", {
        email: email.value,
        phone: phone.value,
        country: country.value,
    });
}, { immediate: true });

const emailError = ref("");
function validateEmail(val) {
    // Very basic email regex
    const re = /^\S+@\S+\.\S+$/;
    return re.test(val);
}
watch(email, (newVal) => {
    if (!newVal) { emailError.value = ""; }
    else if (!validateEmail(newVal)) { emailError.value = "Invalid email format"; }
    else { emailError.value = ""; }
});

const selectedCountry = computed(() => countries.find(c => c.code === country.value) || countries[0]);
const phoneError = ref("");
watch(phone, (val) => {
    if(val && (val.length < selectedCountry.value.min_len)) {
        phoneError.value = `Minimum ${selectedCountry.value.min_len} digits for ${selectedCountry.value.name}`;
    } else {
        phoneError.value = "";
    }
});

// Format phone number per country after input (simple)
function formatPhone(val) {
    return val.replace(/\D/g, "");
}

// Helper: show transaction alert only if email present and valid
const showAlert = computed(() => email.value && !emailError.value);

// Flag dropdown state
const dropdownOpen = ref(false);

function selectCountry(code) {
    country.value = code;
    dropdownOpen.value = false;
    nextTick(() => document.getElementById("contact-phone")?.focus());
}

</script>
<template>
<CosmicCard :title="'Contact Details'" :step-number="5">
    <form class="grid grid-cols-1 md:grid-cols-2 gap-6 relative bg-dark/60 rounded-lg p-5 border border-primary/30 shadow-glow-primary" autocomplete="off" @submit.prevent>
        <!-- Email -->
        <div class="flex flex-col">
            <label for="contact-email" class="text-primary_text font-medium mb-1">Email (optional)</label>
            <input
                id="contact-email"
                v-model="email"
                type="email"
                autocomplete="email"
                aria-label="Email address"
                :aria-invalid="!!emailError"
                class="bg-dark px-4 py-3 rounded text-primary_text focus:ring-2 focus:ring-secondary focus:border-primary/70 focus:bg-dark/90 border border-dark placeholder-secondary outline-none cosmic-focus-meteor"
                placeholder="example@email.com"
            >
            <transition name="fade">
                <div v-if="emailError" class="text-xs mt-1 text-red-300 cosmic-error-particles animate-pulse">
                    {{ emailError }}
                </div>
            </transition>
        </div>
        <!-- Phone with country -->
        <div class="flex flex-col">
            <label for="contact-phone" class="text-primary_text font-medium mb-1">WhatsApp Number</label>
            <div class="flex items-stretch">
                <!-- Country selector -->
                <div class="relative">
                    <button type="button"
                        class="flex items-center gap-1 px-3 py-2 bg-dark border border-secondary rounded-l-lg font-bold text-xl select-none shadow hover-scale transition-all"
                        @click="dropdownOpen = !dropdownOpen"
                        aria-label="Choose country code"
                    >
                        <span class="mr-1">{{ selectedCountry.value.flag }}</span>
                        <span class="text-sm font-semibold">{{ selectedCountry.value.dial_code }}</span>
                        <svg class="w-4 h-4 ml-1 text-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
                        <!-- satellite anim (orbit) -->
                        <span class="absolute -top-2 -right-2 block w-4 h-4 animate-orbit-satellite">
                            <span class="rounded-full bg-secondary block w-full h-full opacity-60"></span>
                        </span>
                    </button>
                    <!-- Dropdown -->
                    <transition name="fade">
                    <div v-show="dropdownOpen" class="absolute left-0 mt-2 py-1 space-y-1 z-30 bg-content_background border border-secondary rounded shadow-lg w-56 max-h-56 overflow-y-auto">
                        <button v-for="ct in countries" :key="ct.code"
                            @click="selectCountry(ct.code)"
                            type="button"
                            class="w-full flex items-center px-2 py-2 hover:bg-secondary/10 space-x-2 text-left"
                            :class="{'bg-secondary/10': ct.code === country }"
                        >
                            <span>{{ ct.flag }}</span>
                            <span class="font-bold text-xs w-10">{{ ct.dial_code }}</span>
                            <span class="text-primary_text">{{ ct.name }}</span>
                        </button>
                    </div>
                    </transition>
                </div>
                <!-- Phone input -->
                <input
                    id="contact-phone"
                    v-model="phone"
                    type="text"
                    inputmode="tel"
                    autocomplete="tel"
                    :placeholder="'8xxxxxxxxx'"
                    class="flex-1 px-4 py-3 border border-l-0 border-secondary rounded-r-lg bg-dark text-primary_text cosmic-focus-meteor"
                    @input="phone = formatPhone(phone)"
                >
            </div>
            <span class="text-xs text-secondary mt-2 block">
                This number will be contacted if issues occur.
            </span>
            <transition name="fade"><span v-if="phoneError" class="text-xs text-red-200 cosmic-error-particles mt-1 animate-pulse">{{ phoneError }}</span></transition>
        </div>

        <!-- Transaction alert (email provided) -->
        <transition name="slide-up">
            <div v-if="showAlert" class="col-span-1 md:col-span-2 flex items-center mt-2 space-x-2 bg-content_background px-4 py-2 rounded-lg border border-secondary shadow animate-pulse cosmic-alert-nebula">
                <!-- "Supernova icon" -->
                <svg class="w-6 h-6 text-secondary animate-pulse mr-2" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="7" fill="#33c3f0" fill-opacity=".13"/><path d="M12 2v4m0 12v4m10-10h-4M6 12H2m15.5-7.5l-2.9 2.9M8.4 18.4l-2.9 2.9m13.1 0l-2.9-2.9M8.4 5.6L5.5 2.7" stroke="#33C3F0" stroke-width="1.5" stroke-linecap="round"/></svg>
                <span class="text-secondary font-semibold text-sm">Transaction proof will be sent to above email (optional)</span>
            </div>
        </transition>
    </form>
</CosmicCard>
</template>

<style scoped>
.cosmic-error-particles {
    /* Simulated effect: particle fade based on error text */
    letter-spacing: 1.5px;
    text-shadow:
      0 1px 2px #9b87f5AA,
      1px 2px 1px #33C3F099;
    filter: blur(0.1px) contrast(120%);
}
.cosmic-alert-nebula { box-shadow: 0 0 10px 0 #33c3f077, 0 0 8px #9b87f511; }
.cosmic-focus-meteor:focus {
    --tw-ring-color: #7e69ab88;
    box-shadow: 0 0 6px 2px #9b87f533, 0 1px 0 #33C3F055;
    background: linear-gradient(80deg,#1f293740 80%,#33c3f009 100%);
    border-color: #7e69ab !important;
}
.animate-orbit-satellite {
    animation: orbit-satellite 2.2s linear infinite;
}
@keyframes orbit-satellite {
    0% { transform: rotate(0deg) translateX(0.7rem) rotate(0deg);}
    100% { transform: rotate(360deg) translateX(0.7rem) rotate(-360deg);}
}
.slide-up-enter-active, .slide-up-leave-active { transition: all 220ms cubic-bezier(.4,0,.6,1); }
.slide-up-enter, .slide-up-leave-to { opacity: 0; transform: translateY(36px);}
.fade-enter-active, .fade-leave-active { transition: opacity 0.22s cubic-bezier(.4,0,.6,1);}
.fade-enter, .fade-leave-to { opacity:0; }
</style>

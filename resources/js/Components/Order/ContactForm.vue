
<template>
    <CosmicCard :title="'Contact Information'">
        <form @submit.prevent>
            <!-- Email Input -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-primary-text mb-1">
                    Email (Optional)
                </label>
                <input
                    type="email"
                    id="email"
                    v-model="email"
                    placeholder="your@email.com"
                    class="w-full rounded-lg bg-secondary/20 border-secondary/30 placeholder-secondary/50 focus:ring-secondary focus:border-primary/70 focus:bg-secondary/20/90 text-primary-text cosmic-input-effect"
                />
            </div>

            <!-- Phone Input with Country Code -->
            <div class="mb-4">
                <label for="phone" class="block text-sm font-semibold text-primary-text mb-1">
                    WhatsApp Number <span class="text-secondary">*</span>
                </label>
                <div class="flex">
                    <!-- Country Code Selector -->
                    <div class="relative flex-shrink-0">
                        <select
                            id="country-code"
                            v-model="countryCode"
                            class="h-full rounded-l-lg bg-secondary/30 border-secondary/30 text-primary-text focus:ring-secondary focus:border-primary/70 pr-8"
                        >
                            <option v-for="country in countries" :key="country.code" :value="country.code">
                                {{ country.code }}
                            </option>
                        </select>
                    </div>

                    <!-- Phone Number Input -->
                    <input
                        type="tel"
                        id="phone"
                        v-model="phoneNumber"
                        placeholder="8123456789"
                        class="flex-grow rounded-r-lg bg-secondary/20 border-secondary/30 placeholder-secondary/50 focus:ring-secondary focus:border-primary/70 focus:bg-secondary/20/90 text-primary-text cosmic-input-effect"
                    />
                </div>
                <div v-if="phoneError" class="mt-1 text-xs text-red-400">
                    {{ phoneError }}
                </div>
                <div class="mt-1 text-xs text-gray-400">
                    Enter your WhatsApp number without leading zeros
                </div>
            </div>

            <!-- Save Contact Info Checkbox -->
            <div class="flex items-center mt-6">
                <Checkbox v-model:checked="saveContactInfo" />
                <label for="saveContact" class="ml-2 text-sm text-primary-text">
                    Save contact info for future orders
                </label>
            </div>
        </form>
    </CosmicCard>
</template>

<script setup>
import { ref, watch, onMounted, computed } from "vue";
import Checkbox from "@/Components/Checkbox.vue";
import CosmicCard from "@/Components/Order/CosmicCard.vue";

const props = defineProps({
    initialEmail: {
        type: String,
        default: ''
    },
    initialPhone: {
        type: String,
        default: ''
    },
    initialCountry: {
        type: String,
        default: 'ID'
    }
});

const emit = defineEmits(['update:contact']);

// Contact form state
const email = ref(props.initialEmail || '');
const phoneNumber = ref('');
const countryCode = ref('+62'); // Default to Indonesia
const phoneError = ref('');
const saveContactInfo = ref(false);

// List of available country codes
const countries = [
    { code: '+62', country: 'ID', minLength: 9, maxLength: 12 }, // Indonesia
    { code: '+60', country: 'MY', minLength: 9, maxLength: 10 }, // Malaysia
    { code: '+65', country: 'SG', minLength: 8, maxLength: 8 },  // Singapore
    { code: '+63', country: 'PH', minLength: 10, maxLength: 10 }, // Philippines
    { code: '+66', country: 'TH', minLength: 9, maxLength: 10 }, // Thailand
    { code: '+84', country: 'VN', minLength: 9, maxLength: 10 }, // Vietnam
    { code: '+1', country: 'US', minLength: 10, maxLength: 10 }  // USA
];

// Get the currently selected country rules
const selectedCountry = computed(() => {
    return countries.find(c => c.code === countryCode.value) || countries[0];
});

// Process the initial phone value on component mount
onMounted(() => {
    if (props.initialPhone) {
        const rawPhone = props.initialPhone.trim();
        
        // Try to extract country code from the phone
        for (const country of countries) {
            if (rawPhone.startsWith(country.code)) {
                countryCode.value = country.code;
                phoneNumber.value = rawPhone.substring(country.code.length);
                break;
            } else if (rawPhone.startsWith('0')) {
                // Handle local format starting with 0
                phoneNumber.value = rawPhone.substring(1);
                break;
            } else {
                phoneNumber.value = rawPhone;
            }
        }
    }
    
    // Set the country code from prop if provided
    if (props.initialCountry) {
        const foundCountry = countries.find(c => c.country === props.initialCountry);
        if (foundCountry) {
            countryCode.value = foundCountry.code;
        }
    }

    // Initial validation
    validatePhone();
});

// Validate phone number based on country rules
const validatePhone = () => {
    const country = selectedCountry.value;
    const cleanPhone = phoneNumber.value.replace(/\D/g, '');
    
    if (!cleanPhone) {
        phoneError.value = 'Phone number is required';
        return false;
    }
    
    if (cleanPhone.length < country.minLength) {
        phoneError.value = `Phone must be at least ${country.minLength} digits for ${country.code}`;
        return false;
    }
    
    if (cleanPhone.length > country.maxLength) {
        phoneError.value = `Phone cannot exceed ${country.maxLength} digits for ${country.code}`;
        return false;
    }
    
    phoneError.value = '';
    return true;
};

// Format the phone number for display
const formatFullPhone = () => {
    const cleanPhone = phoneNumber.value.replace(/\D/g, '');
    if (!cleanPhone) return '';
    
    // Remove leading zeros
    const noLeadingZero = cleanPhone.replace(/^0+/, '');
    
    return `${countryCode.value}${noLeadingZero}`;
};

// Emit contact info updates
const emitContactUpdate = () => {
    if (validatePhone()) {
        emit('update:contact', {
            email: email.value,
            phone: formatFullPhone(),
            country: selectedCountry.value.country
        });
    }
};

// Watch for changes and emit updates
watch([email, phoneNumber, countryCode], () => {
    validatePhone();
    emitContactUpdate();
}, { deep: true });

// Save contact info to localStorage when checkbox is checked
watch(saveContactInfo, (newVal) => {
    if (newVal && validatePhone()) {
        try {
            localStorage.setItem('contactInfo', JSON.stringify({
                email: email.value,
                phone: formatFullPhone(),
                country: selectedCountry.value.country
            }));
        } catch (err) {
            console.error('Error saving contact info:', err);
        }
    }
});
</script>

<style scoped>
.cosmic-input-effect {
    transition: all 0.3s ease;
    transform: translateZ(0);
    will-change: transform, box-shadow;
}

.cosmic-input-effect:focus {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(155, 135, 245, 0.15);
}
</style>

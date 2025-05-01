
<template>
  <CosmicCard :title="'Kontak untuk Info Pesanan'" :step-number="4">
    <div class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label for="email" class="block text-sm font-medium text-primary-text">
            Email <span class="text-gray-500">(Opsional)</span>
          </label>
          <input
            id="email"
            type="email"
            v-model="email"
            placeholder="email@example.com"
            class="w-full rounded-lg bg-secondary/20 border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/50 text-primary-text cosmic-input-effect"
          />
        </div>

        <div class="space-y-2">
          <label for="phone" class="block text-sm font-medium text-primary-text">
            WhatsApp <span class="text-secondary">*</span>
          </label>
          <div class="flex items-center">
            <div class="relative w-1/4">
              <select
                v-model="selectedCountry"
                class="w-full rounded-l-lg bg-secondary/20 border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/50 text-primary-text cosmic-input-effect pr-10"
              >
                <option 
                  v-for="country in countries" 
                  :key="country.code" 
                  :value="country"
                >
                  +{{ country.code }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <input
              id="phone"
              type="tel"
              v-model="phone"
              placeholder="8123456789"
              @input="validatePhone"
              class="w-3/4 rounded-r-lg bg-secondary/20 border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/50 text-primary-text cosmic-input-effect"
            />
          </div>
          <p v-if="phoneError" class="text-xs text-red-500 mt-1">{{ phoneError }}</p>
        </div>
      </div>

      <div class="p-4 rounded-lg bg-secondary/10">
        <div class="flex items-start gap-3">
          <div class="flex-shrink-0 pt-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <p class="text-sm text-gray-300">
            Informasi WhatsApp <span class="text-secondary font-medium">wajib diisi</span> untuk mendapatkan notifikasi status pesanan. 
            Pastikan nomor WhatsApp aktif dan dapat menerima pesan.
          </p>
        </div>
      </div>
    </div>
  </CosmicCard>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
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

const email = ref(props.initialEmail || '');
const phone = ref(props.initialPhone || '');
const phoneError = ref('');

const countries = [
  { code: '62', country: 'ID', pattern: /^[1-9][0-9]{7,11}$/ }, // Indonesia
  { code: '60', country: 'MY', pattern: /^[1-9][0-9]{7,9}$/ },  // Malaysia
  { code: '65', country: 'SG', pattern: /^[1-9][0-9]{7,8}$/ },  // Singapore
  { code: '63', country: 'PH', pattern: /^[1-9][0-9]{8,10}$/ }, // Philippines
  { code: '66', country: 'TH', pattern: /^[1-9][0-9]{8,9}$/ },  // Thailand
];

// Find default country
const selectedCountry = ref(countries.find(c => c.country === props.initialCountry) || countries[0]);

const validatePhone = () => {
  // Remove any non-digit characters
  phone.value = phone.value.replace(/\D/g, '');
  
  // Remove leading zero if present
  if (phone.value.startsWith('0')) {
    phone.value = phone.value.substring(1);
  }
  
  // Validate against the pattern for selected country
  if (phone.value && !selectedCountry.value.pattern.test(phone.value)) {
    phoneError.value = 'Please enter a valid phone number';
  } else {
    phoneError.value = '';
  }
  
  updateContact();
};

const updateContact = () => {
  if (phone.value || email.value) {
    emit('update:contact', {
      email: email.value,
      phone: phone.value,
      countryCode: selectedCountry.value.code,
      country: selectedCountry.value.country,
      isValid: phone.value && !phoneError.value
    });
  }
};

watch(email, updateContact);
watch(selectedCountry, () => {
  validatePhone();
});

onMounted(() => {
  updateContact();
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

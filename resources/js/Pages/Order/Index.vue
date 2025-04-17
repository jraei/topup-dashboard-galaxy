
<script setup>
import { ref, computed, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import CosmicCard from "@/Components/User/Order/CosmicCard.vue";
import CosmicCheckbox from "@/Components/User/Order/CosmicCheckbox.vue";
import PulsarLoading from "@/Components/User/Order/PulsarLoading.vue";
import { CreditCard, Rocket, MessageCircle, Shield, Info } from "lucide-vue-next";

const { produk, layanans, user } = defineProps({
  produk: Object,
  layanans: Object,
  user: Object,
});

const formData = ref({});
const showGuideModal = ref(false);
const saveId = ref(false);
const isLoading = ref(false);

// Initialize form data based on input fields
onMounted(() => {
  if (produk.inputFields) {
    produk.inputFields.forEach(field => {
      formData.value[field.name] = '';
    });
  }
});

// Sort input fields by order property
const sortedInputFields = computed(() => {
  if (!produk.inputFields) return [];
  return [...produk.inputFields].sort((a, b) => a.order - b.order);
});

const bannerImage = computed(() => {
  return produk.banner 
    ? `/storage/${produk.banner}` 
    : 'https://images.unsplash.com/photo-1470813740244-df37b8c1edcb?w=1280&h=400&fit=crop';
});

const thumbnailImage = computed(() => {
  return produk.thumbnail 
    ? `/storage/${produk.thumbnail}` 
    : 'https://via.placeholder.com/300';
});
</script>

<template>
  <GuestLayout>
    <!-- Product Banner Section -->
    <div class="relative w-full overflow-hidden">
      <img 
        :src="bannerImage" 
        :alt="produk.nama" 
        class="w-full min-h-[14rem] object-cover object-center lg:object-contain" 
        loading="lazy"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-dark to-transparent"></div>
    </div>

    <!-- Product Information Section -->
    <section class="relative bg-dark-lighter py-8 overflow-hidden">
      <!-- Saturn Illustration (Desktop only) -->
      <div class="absolute right-[20%] top-1/2 -translate-y-1/2 w-48 h-48 hidden lg:block opacity-20">
        <img src="/img/saturn.svg" alt="Saturn" class="w-full h-full transform rotate-15" />
      </div>

      <div class="container mx-auto px-4">
        <!-- Product Details -->
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 mb-8 relative z-10">
          <!-- Product Thumbnail with Cosmic Effects -->
          <div class="relative w-32 md:w-60 flex-shrink-0 transform perspective-3d hover:scale-105 transition-transform duration-300">
            <div class="absolute inset-0 rounded-lg bg-primary/20 blur-xl"></div>
            <img 
              :src="thumbnailImage" 
              :alt="produk.nama" 
              class="relative w-full h-auto rounded-lg object-cover shadow-glow-primary"
            />
            <div class="cosmic-particles absolute inset-0 pointer-events-none"></div>
          </div>

          <!-- Product Info -->
          <div class="text-center md:text-left">
            <h1 class="text-2xl md:text-4xl font-bold text-white mb-2 cosmic-text">
              {{ produk.nama }}
            </h1>
            <p v-if="produk.developer" class="text-secondary text-lg mb-4 quasar-underline">
              {{ produk.developer }}
            </p>
            <p v-if="produk.deskripsi_game" class="text-gray-300 mb-4 max-w-2xl">
              {{ produk.deskripsi_game }}
            </p>
          </div>
        </div>

        <!-- Service Icons -->
        <div class="grid grid-cols-3 gap-4 justify-evenly py-4 max-w-3xl mx-auto">
          <div class="flex flex-col items-center text-center">
            <div class="w-12 h-12 flex items-center justify-center bg-primary/20 rounded-full mb-2 hover:shadow-glow-primary transition-all duration-300">
              <Rocket class="w-6 h-6 text-primary" />
            </div>
            <span class="text-xs md:text-sm text-gray-300">Proses Cepat</span>
          </div>
          <div class="flex flex-col items-center text-center">
            <div class="w-12 h-12 flex items-center justify-center bg-primary/20 rounded-full mb-2 hover:shadow-glow-primary transition-all duration-300">
              <MessageCircle class="w-6 h-6 text-primary" />
            </div>
            <span class="text-xs md:text-sm text-gray-300">Layanan Chat 24/7</span>
          </div>
          <div class="flex flex-col items-center text-center">
            <div class="w-12 h-12 flex items-center justify-center bg-primary/20 rounded-full mb-2 hover:shadow-glow-primary transition-all duration-300">
              <Shield class="w-6 h-6 text-primary" />
            </div>
            <span class="text-xs md:text-sm text-gray-300">Pembayaran Aman</span>
          </div>
        </div>
      </div>
    </section>

    <!-- User Data Section -->
    <section class="py-8 bg-dark">
      <div class="container mx-auto px-4">
        <CosmicCard 
          title="Step 1 of 6: Masukkan Data Akun" 
          :progress="16.67"
          class="max-w-3xl mx-auto"
        >
          <div v-if="isLoading" class="flex justify-center py-8">
            <PulsarLoading />
          </div>
          <div v-else>
            <!-- Dynamic Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
              <template v-for="field in sortedInputFields" :key="field.id">
                <div class="space-y-2" :class="{ 'md:col-span-2': field.type === 'select' }">
                  <label :for="field.name" class="block text-sm font-medium text-gray-300">
                    {{ field.label }}
                    <span v-if="field.required" class="text-red-500">*</span>
                  </label>
                  
                  <!-- Text/Number Input -->
                  <input 
                    v-if="field.type !== 'select'"
                    :type="field.type" 
                    :id="field.name"
                    v-model="formData[field.name]"
                    :required="field.required"
                    class="mt-1 block w-full rounded-md bg-dark-card border-gray-600 shadow-sm focus:border-primary focus:ring focus:ring-primary/50 text-white"
                  />
                  
                  <!-- Select Dropdown -->
                  <select 
                    v-else 
                    :id="field.name"
                    v-model="formData[field.name]"
                    :required="field.required"
                    class="mt-1 block w-full rounded-md bg-dark-card border-gray-600 shadow-sm focus:border-primary focus:ring focus:ring-primary/50 text-white"
                  >
                    <option value="" disabled selected>Pilih {{ field.label }}</option>
                    <option 
                      v-for="option in field.options" 
                      :key="option.id" 
                      :value="option.value"
                    >
                      {{ option.label }}
                    </option>
                  </select>
                </div>
              </template>
            </div>

            <!-- Footer Elements -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
              <div class="flex items-center mb-4 sm:mb-0">
                <CosmicCheckbox v-model:checked="saveId" />
                <label for="saveId" class="ml-2 text-sm text-gray-300">
                  Simpan ID untuk pembelian berikutnya
                </label>
              </div>
              
              <button 
                @click="showGuideModal = true" 
                class="inline-flex items-center text-sm text-secondary hover:text-secondary-hover transition-colors"
              >
                <Info class="w-4 h-4 mr-1" />
                Butuh bantuan?
              </button>
            </div>
          </div>
        </CosmicCard>
      </div>
    </section>

    <!-- Guide Modal -->
    <Modal :show="showGuideModal" @close="showGuideModal = false" max-width="2xl">
      <div class="p-4 md:p-8">
        <h2 class="text-xl md:text-2xl font-bold mb-4 text-primary">Petunjuk Pengisian</h2>
        
        <div v-if="produk.petunjuk_field" class="mb-6">
          <img 
            :src="`/storage/${produk.petunjuk_field}`" 
            alt="Petunjuk Pengisian" 
            class="w-full rounded-lg shadow-md mx-auto" 
          />
        </div>
        
        <div v-if="produk.deskripsi_game" class="text-gray-200 prose prose-invert max-w-full">
          <div v-html="produk.deskripsi_game"></div>
        </div>
        
        <div class="mt-6 flex justify-end">
          <PrimaryButton @click="showGuideModal = false">
            Tutup
          </PrimaryButton>
        </div>
      </div>
    </Modal>
  </GuestLayout>
</template>

<style scoped>
.cosmic-text {
  background: linear-gradient(to right, #9b87f5, #33C3F0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-fill-color: transparent;
}

.quasar-underline {
  position: relative;
}

.quasar-underline::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -2px;
  width: 100%;
  height: 2px;
  background: linear-gradient(90deg, transparent, #33C3F0, transparent);
  animation: pulse-slow 3s infinite;
}

.perspective-3d {
  perspective: 25em;
}

.shadow-glow-primary {
  box-shadow: 0 0 15px rgba(155, 135, 245, 0.5);
}

/* Animation keyframes */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 1; }
}
</style>

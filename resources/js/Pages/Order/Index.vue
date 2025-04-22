<template>
  <AppLayout title="Order">
    <template #header>
      <h2 class="font-semibold text-xl text-secondary leading-tight">
        {{ props.title }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Original order content -->
        <Head :title="title" />

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800">
                            {{ produk.nama }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Top up your favorite game here!
                        </p>
                    </div>
                    <div>
                        <img :src="produk.thumbnail" alt="Game Thumbnail" class="w-full rounded-md" />
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="font-semibold text-md text-gray-800">
                        Select Top Up Amount
                    </h4>
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div v-for="layanan in layanans" :key="layanan.id" class="rounded-md border p-3 cursor-pointer hover:shadow-md transition duration-300 ease-in-out" @click="selectLayanan(layanan)">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ layanan.nama_layanan }}</p>
                                    <p class="text-xs text-gray-500">Rp {{ layanan.harga_jual }}</p>
                                </div>
                                <img :src="layanan.thumbnail" alt="Item Thumbnail" class="w-12 h-12 object-cover rounded-md" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="font-semibold text-md text-gray-800">
                        Enter Player Information
                    </h4>
                    <div class="mt-2">
                        <div v-for="field in inputFields" :key="field.id" class="mb-4">
                            <label :for="field.name" class="block text-sm font-medium text-gray-700">{{ field.label }}</label>
                            <input type="text" :name="field.name" :id="field.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" :required="field.required" />
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="font-semibold text-md text-gray-800">
                        Payment Method
                    </h4>
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">
                            Total: Rp {{ currentTotal }}
                        </p>
                        <button class="mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                            Pay Now
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Voucher Application Module -->
        <div class="p-2 sm:p-0">
          <VoucherCard 
            :vouchers="props.activeVouchers || []"
            :current-total="props.currentTotal || 0"
          />
        </div>
        
        <!-- Section 2: Product Description Display -->
        <div class="p-2 sm:p-0">
          <ProductDescription 
            :description="props.produk?.deskripsi_game || ''"
          />
        </div>
        
        <!-- Section 3: FAQ Accordion -->
        <div class="p-2 sm:p-0">
          <FaqAccordion 
            :faqs="props.faqs || []"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import VoucherCard from '@/Components/Order/VoucherCard.vue';
import ProductDescription from '@/Components/Order/ProductDescription.vue';
import FaqAccordion from '@/Components/Order/FaqAccordion.vue';
import CosmicStars from '@/Components/Order/CosmicStars.vue';

const props = defineProps({
  title: String,
  produk: Object,
  layanans: Array,
  inputFields: Array,
  currentTotal: Number,
  activeVouchers: Array,
  faqs: Array
});

const selectLayanan = (layanan) => {
    console.log('Selected Layanan:', layanan);
};
</script>

<style>
@import '@/css/cosmic-animations.css';

/* Import this CSS to customize the cosmic theme */
.bg-cosmic-gradient {
  background: linear-gradient(135deg, #111827 0%, #1F2937 100%);
}

.text-gradient-cosmic {
  background: linear-gradient(90deg, #9b87f5, #33C3F0);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>

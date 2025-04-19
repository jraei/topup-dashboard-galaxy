<script setup>
import { ref } from "vue";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
    inputFields: Array,
    produk: Object,
});

const showModal = ref(false);
const savedIdForFuture = ref(false);
</script>

<template>
    <!-- Cosmic Card Component -->
    <div class="mb-8 overflow-hidden rounded-2xl cosmic-card">
        <!-- Card Header -->
        <div class="relative p-4 text-white bg-primary/20">
            <!-- Constellation pattern -->
            <div class="absolute inset-0 constellation-pattern"></div>

            <div class="relative z-10">
                <h3 class="text-lg font-bold md:text-xl">
                    Step 1 of 6: Masukkan Data Akun
                </h3>
                <!-- Cosmic progress bar -->
                <div class="h-1 mt-2 rounded bg-primary/30 cosmic-progress">
                    <div
                        class="w-1/6 h-full rounded bg-gradient-to-r from-primary to-secondary"
                    >
                        <!-- Micro stars in progress bar -->
                        <div
                            v-for="i in 3"
                            :key="`star-${i}`"
                            class="absolute w-px h-px bg-white rounded-full cosmic-star"
                            :style="{
                                left: `${2 + i * 4}%`,
                                top: '50%',
                                transform: 'translate(-50%, -50%)',
                                animationDelay: `${i * 0.5}s`,
                            }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div
            class="relative p-6 border bg-gradient-to-b from-primary/20 to-primary/5 border-secondary/20"
        >
            <!-- Dynamic Input Fields -->
            <form class="relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <template v-for="field in inputFields" :key="field.id">
                        <div class="space-y-2">
                            <label
                                :for="`field_${field.id}`"
                                class="block text-sm font-semibold text-primary-text"
                            >
                                {{ field.label }}
                                <span
                                    v-if="field.required"
                                    class="ml-1 text-secondary"
                                    >*</span
                                >
                            </label>

                            <!-- Text/Number Input -->
                            <input
                                v-if="
                                    field.type === 'text' ||
                                    field.type === 'number'
                                "
                                :type="field.type"
                                :id="`field_${field.id}`"
                                :name="field.name"
                                :required="field.required"
                                class="w-full rounded-lg bg-secondary/20 border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/50 text-primary-text cosmic-input-effect"
                            />

                            <!-- Select Input -->
                            <select
                                v-else-if="field.type === 'select'"
                                :id="`field_${field.id}`"
                                :name="field.name"
                                :required="field.required"
                                class="w-full rounded-lg bg-secondary/20 border-secondary/30 focus:border-secondary focus:ring focus:ring-secondary/50 text-primary-text cosmic-input-effect"
                            >
                                <option value="" disabled selected>
                                    Select an option
                                </option>
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
                <div
                    class="flex flex-col gap-4 mt-8 md:flex-row md:items-center md:justify-between"
                >
                    <!-- Save ID Checkbox -->
                    <div class="flex items-center">
                        <Checkbox v-model:checked="savedIdForFuture" class="" />
                        <label
                            for="saveId"
                            class="ml-2 text-sm text-primary-text"
                        >
                            Simpan ID untuk pembelian berikutnya
                        </label>
                    </div>

                    <!-- Purchase Guide Button -->
                    <button
                        type="button"
                        @click="showModal = true"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-white transition-colors rounded-md bg-primary/80 hover:bg-primary"
                    >
                        <span>Panduan Pembelian</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cosmic Modal -->
    <Modal :show="showModal" @close="showModal = false" max-width="xl">
        <div
            class="p-4 border rounded-lg md:p-6 bg-gradient-to-br from-primary/90 to-secondary/50 border-secondary/50 text-primary-text"
        >
            <h3 class="mb-4 text-xl font-bold text-center">
                Panduan Pembelian
            </h3>

            <div class="space-y-6">
                <!-- Purchase Guide Image -->
                <div
                    v-if="produk.petunjuk_field"
                    class="max-w-full mx-auto overflow-hidden rounded-lg"
                >
                    <img
                        :src="`/storage/${produk.petunjuk_field}`"
                        alt="Purchase Guide"
                        class="w-full object-contain max-h-[60vh]"
                    />
                </div>

                <!-- Purchase Guide Text -->
                <div
                    v-if="produk.deskripsi_game"
                    class="prose-sm prose md:prose-base max-w-none text-primary-text"
                >
                    <p>{{ produk.deskripsi_game }}</p>
                </div>
            </div>

            <!-- Close Button -->
            <div class="mt-6 text-center">
                <button
                    type="button"
                    @click="showModal = false"
                    class="px-6 py-2 text-white transition-colors rounded-md bg-primary hover:bg-primary-hover"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.cosmic-card {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    will-change: transform, box-shadow;
    border: 1px solid transparent;
    background-clip: padding-box;
    position: relative;
}

.cosmic-card::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    margin: -1px;
    border-radius: inherit;
    background: linear-gradient(
        120deg,
        rgba(51, 195, 240, 0.1),
        rgba(155, 135, 245, 0.3)
    );
    animation: border-pulse 4s ease-in-out infinite;
}

.cosmic-card:hover {
    box-shadow: 0 6px 24px rgba(155, 135, 245, 0.2);
}

.cosmic-input-effect {
    transition: all 0.3s ease;
    transform: translateZ(0);
    will-change: transform, box-shadow;
}

.cosmic-input-effect:focus {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(155, 135, 245, 0.15);
}

.constellation-pattern {
    background-image: radial-gradient(
        circle,
        rgba(155, 135, 245, 0.2) 1px,
        transparent 1px
    );
    background-size: 25px 25px;
}

.cosmic-progress {
    overflow: hidden;
    position: relative;
}

.cosmic-star {
    animation: star-twinkle 2s ease-in-out infinite;
}

@keyframes border-pulse {
    0%,
    100% {
        opacity: 0.3;
    }
    50% {
        opacity: 0.7;
    }
}

@keyframes star-twinkle {
    0%,
    100% {
        opacity: 0.3;
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.5);
    }
}

/* @media (hover: hover) {
    .cosmic-card:hover {
        transform: skewY(0.5deg) translateY(-2px);
    }
} */
</style>

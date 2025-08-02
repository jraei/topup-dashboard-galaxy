<script setup>
import { ref, onMounted, watch } from "vue";
import Modal from "@/Components/Modal.vue";
import { useCookies } from "@/Composables/useCookies.js";

const props = defineProps({
    popupImage: {
        type: String,
        default: null,
    },
    popupHtml: {
        type: String,
        default: null,
    },
});

const { getCookie, setCookie } = useCookies();
const showPopup = ref(false);

// Check if popup should be shown
const checkShowPopup = () => {
    // Check if user has disabled popup for 7 days
    const popupDisabled = getCookie("popup_disabled_7_days");
    if (popupDisabled) return false;

    // Check if popup has been shown this session
    const popupShown = sessionStorage.getItem("popup_shown");
    if (popupShown) return false;

    // Show popup if there's content
    return props.popupImage || props.popupHtml;
};

const closePopup = () => {
    showPopup.value = false;
    // Mark as shown for this session
    sessionStorage.setItem("popup_shown", "true");
};

const dontShowAgain = () => {
    // Set cookie for 7 days
    setCookie("popup_disabled_7_days", true, 7);
    closePopup();
};

onMounted(() => {
    // Small delay to ensure smooth page load
    setTimeout(() => {
        if (checkShowPopup()) {
            showPopup.value = true;
        }
    }, 1000);
});
</script>

<template>
    <Modal :show="showPopup" @close="closePopup" max-width="2xl">
        <div class="relative overflow-hidden rounded-xl bg-content_background">
            <!-- Image Section -->
            <div v-if="popupImage" class="relative">
                <img
                    :src="
                        popupImage.startsWith('http')
                            ? popupImage
                            : `/storage/${popupImage}`
                    "
                    alt="Popup Image"
                    class="object-cover w-full h-48 md:h-64"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-content_background/80 to-transparent"
                ></div>
            </div>

            <!-- Content Section -->
            <div class="p-6 space-y-4">
                <div
                    v-if="popupHtml"
                    v-html="popupHtml"
                    class="prose prose-invert max-w-none text-primary-text/80"
                ></div>

                <!-- Action Buttons -->
                <div
                    class="flex flex-col gap-3 pt-4 border-t border-gray-600 sm:flex-row"
                >
                    <button
                        @click="dontShowAgain"
                        class="px-4 py-2 text-sm transition-colors rounded-lg text-primary-text/70 hover:text-primary bg-primary/25 hover:bg-primary/20"
                    >
                        Jangan tampilkan lagi (7 hari)
                    </button>
                    <button
                        @click="closePopup"
                        class="px-6 py-2 ml-auto font-medium text-white transition-colors rounded-lg bg-primary hover:bg-primary_hover"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import { useCookies } from '@/Composables/useCookies.js';

const props = defineProps({
    popupImage: {
        type: String,
        default: null
    },
    popupHtml: {
        type: String,
        default: null
    }
});

const { getCookie, setCookie } = useCookies();
const showPopup = ref(false);

// Check if popup should be shown
const checkShowPopup = () => {
    // Check if user has disabled popup for 7 days
    const popupDisabled = getCookie('popup_disabled_7_days');
    if (popupDisabled) return false;

    // Check if popup has been shown this session
    const popupShown = sessionStorage.getItem('popup_shown');
    if (popupShown) return false;

    // Show popup if there's content
    return props.popupImage || props.popupHtml;
};

const closePopup = () => {
    showPopup.value = false;
    // Mark as shown for this session
    sessionStorage.setItem('popup_shown', 'true');
};

const dontShowAgain = () => {
    // Set cookie for 7 days
    setCookie('popup_disabled_7_days', true, 7);
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
    <Modal :show="showPopup" @close="closePopup" max-width="md">
        <div class="relative overflow-hidden bg-content_background rounded-lg">
            <!-- Image Section -->
            <div v-if="popupImage" class="relative">
                <img 
                    :src="popupImage.startsWith('http') ? popupImage : `/storage/${popupImage}`"
                    alt="Popup Image"
                    class="w-full h-48 md:h-64 object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-content_background/80 to-transparent"></div>
            </div>

            <!-- Content Section -->
            <div class="p-6 space-y-4">
                <div 
                    v-if="popupHtml" 
                    v-html="popupHtml"
                    class="prose prose-invert max-w-none text-text_primary"
                ></div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-600">
                    <button
                        @click="dontShowAgain"
                        class="px-4 py-2 text-sm text-text_secondary hover:text-text_primary transition-colors"
                    >
                        Don't show again (7 days)
                    </button>
                    <button
                        @click="closePopup"
                        class="px-6 py-2 bg-primary_color hover:bg-primary_hover text-white rounded-lg transition-colors font-medium ml-auto"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>
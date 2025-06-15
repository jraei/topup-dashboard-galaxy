<template>
    <div class="w-full mb-8">
        <CosmicCard title="Pertanyaan Yang Sering Ditanyakan (FAQ)">
            <!-- FAQ Accordion -->
            <div class="space-y-4">
                <div
                    v-for="(faq, index) in faqs"
                    :key="`faq-${index}`"
                    class="relative"
                >
                    <!-- Mini planet decorations -->
                    <div
                        v-if="index > 0"
                        class="absolute flex items-center justify-center w-6 h-1 transform -translate-x-1/2 -top-3 left-1/2"
                    >
                        <div
                            class="w-1.5 h-1.5 bg-secondary/30 rounded-full"
                        ></div>
                        <div
                            class="absolute w-1 h-1 ml-3 rounded-full bg-primary/30"
                        ></div>
                    </div>

                    <!-- Question Header -->
                    <button
                        @click="toggleFaq(index)"
                        class="flex items-center justify-between w-full p-4 text-left transition-colors duration-200 border border-transparent rounded-lg focus:outline-none focus:border-secondary/30 bg-primary/10 hover:bg-primary/20"
                        :class="{ 'bg-primary/20': activeFaq === index }"
                    >
                        <span class="font-medium text-white">{{
                            faq.question
                        }}</span>
                        <span
                            class="transition-transform duration-200 transform text-secondary"
                            :class="{ 'rotate-180': activeFaq === index }"
                        >
                            ▼
                        </span>
                    </button>

                    <!-- Answer Content -->
                    <div
                        class="overflow-hidden transition-all duration-300 ease-in-out"
                        :style="{
                            maxHeight:
                                activeFaq === index
                                    ? `${faqContentHeight[index] || 0}px`
                                    : '0px',
                            opacity: activeFaq === index ? 1 : 0,
                        }"
                    >
                        <div
                            class="p-4 text-gray-300 transition-all border-t-0 rounded-b-lg bg-dark-lighter/50"
                            :ref="
                                (el) => {
                                    if (el) faqRefs[index] = el;
                                }
                            "
                            :class="{ 'interstellar-bg': activeFaq === index }"
                        >
                            <div v-html="faq.answer"></div>
                        </div>
                    </div>

                    <!-- Pulsing energy for active item -->
                    <div
                        v-if="activeFaq === index"
                        class="absolute inset-0 pointer-events-none"
                    >
                        <div
                            class="absolute inset-0 border rounded-lg border-secondary/20 animate-pulse-slow"
                        ></div>
                    </div>
                </div>
            </div>
        </CosmicCard>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import CosmicCard from "@/Components/Order/CosmicCard.vue";

const props = defineProps({
    faqs: {
        type: Array,
        default: () => [],
    },
});

const activeFaq = ref(null);
const faqRefs = ref([]);
const faqContentHeight = ref([]);

// Toggle FAQ item
const toggleFaq = (index) => {
    if (activeFaq.value === index) {
        activeFaq.value = null;
    } else {
        activeFaq.value = index;
        // Wait for the next render cycle to calculate height
        setTimeout(() => {
            calculateHeights();
        }, 10);
    }
};

// Calculate heights for smooth animation
const calculateHeights = () => {
    props.faqs.forEach((_, index) => {
        if (faqRefs.value[index]) {
            faqContentHeight.value[index] = faqRefs.value[index].scrollHeight;
        }
    });
};

// Watch for changes in FAQs to recalculate heights
watch(
    () => props.faqs,
    () => {
        faqRefs.value = [];
        faqContentHeight.value = [];
        activeFaq.value = null;
    },
    { deep: true }
);

onMounted(() => {
    // Initialize refs array
    faqRefs.value = new Array(props.faqs.length).fill(null);
    faqContentHeight.value = new Array(props.faqs.length).fill(0);

    // Allow a bit of time for rendering
    setTimeout(() => {
        calculateHeights();
    }, 100);

    // Recalculate on window resize
    window.addEventListener("resize", calculateHeights);
});
</script>

<style scoped>
.interstellar-bg {
    position: relative;
    overflow: hidden;
}

.interstellar-bg::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(
        circle at 50% 50%,
        rgba(51, 195, 240, 0.05) 0%,
        transparent 70%
    );
    opacity: 0.5;
    animation: interstellar-pulse 4s infinite alternate;
}

@keyframes interstellar-pulse {
    0% {
        opacity: 0.2;
        transform: scale(0.8);
    }
    100% {
        opacity: 0.5;
        transform: scale(1.2);
    }
}
</style>

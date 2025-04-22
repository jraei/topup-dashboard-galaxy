
<template>
  <div class="cosmic-card bg-content-background border border-primary/30 rounded-lg overflow-hidden shadow-lg">
    <!-- Card Header -->
    <div class="px-4 py-3 bg-header-background border-b border-primary/30 flex items-center">
      <div class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="text-primary h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
          <path d="M12 17h.01" />
        </svg>
        <h3 class="text-lg font-medium text-secondary">Frequently Asked Questions</h3>
      </div>
    </div>

    <!-- Card Body -->
    <div class="p-4 space-y-4">
      <!-- No FAQs message -->
      <p v-if="!faqs.length" class="text-center py-6 text-secondary/70 italic">
        No FAQs available
      </p>
      
      <!-- FAQ Items -->
      <div v-else>
        <div 
          v-for="(faq, index) in faqs" 
          :key="index"
          class="cosmic-accordion"
        >
          <!-- Accordion Header -->
          <button 
            class="w-full text-left px-4 py-3 flex justify-between items-center rounded-lg transition-all duration-200 relative overflow-hidden hover:bg-header-background group"
            @click="toggleFaq(index)"
            :class="{ 'bg-header-background': activeFaq === index }"
          >
            <!-- Planet decoration between items (except first) -->
            <div v-if="index > 0" class="absolute -top-3 left-1/2 transform -translate-x-1/2 planet-decoration opacity-30"></div>
            
            <!-- Question -->
            <span class="text-secondary font-medium pr-8">{{ faq.question }}</span>
            
            <!-- Caret Icon -->
            <div 
              class="text-primary transition-transform duration-200 transform"
              :class="{ 'rotate-180': activeFaq === index }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6" />
              </svg>
            </div>
            
            <!-- Gravitational pull effect on hover -->
            <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100">
              <div 
                v-for="i in 4" 
                :key="i" 
                class="absolute w-1 h-1 rounded-full bg-primary/30"
                :style="getParticleStyle(i)"
              ></div>
            </div>
          </button>
          
          <!-- Accordion Content -->
          <div 
            class="overflow-hidden transition-all duration-300 transform gpu-accelerated"
            :style="{ 
              maxHeight: activeFaq === index ? `${faqHeights[index] || 1000}px` : '0px',
              opacity: activeFaq === index ? 1 : 0
            }"
          >
            <div 
              class="px-4 py-3 text-secondary/90 text-sm md:text-base" 
              :ref="el => { if (el) faqRefs[index] = el }"
              v-html="sanitizeFaqAnswer(faq.answer)"
            >
            </div>
            
            <!-- Optional feedback -->
            <div v-if="activeFaq === index" class="px-4 pb-3 flex space-x-4 text-xs text-secondary/70 animate-fade-in">
              <span>Was this helpful?</span>
              <button class="hover:text-primary transition-colors" @click="submitFeedback(index, true)">Yes</button>
              <button class="hover:text-primary transition-colors" @click="submitFeedback(index, false)">No</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue';
import DOMPurify from 'dompurify';

const props = defineProps({
  faqs: {
    type: Array,
    default: () => []
  }
});

// State
const activeFaq = ref(null);
const faqRefs = ref([]);
const faqHeights = ref([]);

// Toggle FAQ item
const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index;
  updateHeights();
};

// Update accordion content heights
const updateHeights = async () => {
  await nextTick();
  props.faqs.forEach((_, index) => {
    if (faqRefs.value[index]) {
      faqHeights.value[index] = faqRefs.value[index].scrollHeight;
    }
  });
};

// Sanitize FAQ answer HTML
const sanitizeFaqAnswer = (answer) => {
  if (!answer) return '';
  return DOMPurify.sanitize(answer, {
    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'a', 'ul', 'ol', 'li', 'span'],
    ALLOWED_ATTR: ['href', 'target', 'rel']
  });
};

// Handle feedback (placeholder)
const submitFeedback = (index, isHelpful) => {
  // This would normally send feedback to the server
  console.log(`FAQ ${index} feedback: ${isHelpful ? 'helpful' : 'not helpful'}`);
  // Show thank you message
  alert('Thank you for your feedback!');
};

// Generate random particle positions for gravitational effect
const getParticleStyle = (index) => {
  const positions = [
    { left: '20%', top: '30%' },
    { left: '70%', top: '20%' },
    { left: '40%', top: '70%' },
    { left: '80%', top: '60%' }
  ];
  
  return positions[index - 1];
};

// Calculate heights after mounting
onMounted(() => {
  updateHeights();
});

// Recalculate heights when faqs change
watch(() => props.faqs, updateHeights, { deep: true });
</script>

<style scoped>
.cosmic-accordion + .cosmic-accordion {
  margin-top: 1rem;
  position: relative;
}

.planet-decoration {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: radial-gradient(circle at center, #33C3F0, #9b87f5);
  box-shadow: 0 0 5px rgba(51, 195, 240, 0.7);
}

/* Pulse animation for active accordion */
.cosmic-accordion button:has(+ div[style*="opacity: 1"])::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 60%;
  background: linear-gradient(to bottom, transparent, #9b87f5, transparent);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 0.5; }
  50% { opacity: 1; }
}

/* GPU acceleration */
.gpu-accelerated {
  will-change: max-height, opacity;
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}
</style>

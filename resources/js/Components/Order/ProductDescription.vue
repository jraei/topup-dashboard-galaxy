
<template>
  <div class="cosmic-card bg-content-background border border-primary/30 rounded-lg overflow-hidden shadow-lg mb-6">
    <!-- Card Header -->
    <div class="px-4 py-3 bg-header-background border-b border-primary/30 flex items-center">
      <div class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="text-primary h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <path d="M12 16v-4" />
          <path d="M12 8h.01" />
        </svg>
        <h3 class="text-lg font-medium text-secondary">Product Details</h3>
      </div>
    </div>

    <!-- Card Body with Nebula Background -->
    <div class="p-4 md:p-6 product-description-container relative overflow-hidden">
      <!-- Nebula Background Effect -->
      <div class="absolute inset-0 nebula-bg opacity-10"></div>
      
      <!-- Shooting Stars -->
      <div class="shooting-stars absolute inset-0 overflow-hidden">
        <div v-for="i in 3" :key="i" class="shooting-star" :style="getShootingStarStyle(i)"></div>
      </div>
      
      <!-- Product Description Content -->
      <div class="relative z-10">
        <div 
          v-if="description" 
          class="prose prose-sm md:prose-base max-w-none text-secondary cosmic-content"
          v-html="sanitizedDescription"
        ></div>
        <div v-else class="text-center py-6 text-secondary/70 italic">
          No product description available
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import DOMPurify from 'dompurify';

const props = defineProps({
  description: {
    type: String,
    default: ''
  }
});

// Sanitize HTML content
const sanitizedDescription = computed(() => {
  if (!props.description) return '';
  // Configure DOMPurify to allow basic HTML tags
  const sanitizedHtml = DOMPurify.sanitize(props.description, {
    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'ul', 'ol', 'li', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'img'],
    ALLOWED_ATTR: ['src', 'alt', 'href', 'target', 'style', 'class']
  });
  return sanitizedHtml;
});

// Generate random shooting star style
const getShootingStarStyle = (index) => {
  const delay = index * 4 + Math.random() * 5;
  const top = 10 + Math.random() * 80;
  
  return {
    animationDelay: `${delay}s`,
    top: `${top}%`,
  };
};
</script>

<style scoped>
.product-description-container {
  min-height: 100px;
}

.nebula-bg {
  background-image: radial-gradient(circle at 50% 50%, rgba(51, 195, 240, 0.3), rgba(155, 135, 245, 0.3), rgba(33, 33, 33, 0.1));
  filter: blur(40px);
}

@keyframes shooting {
  0% {
    transform: translateX(0) translateY(0) rotate(-35deg);
    opacity: 0;
    width: 0;
  }
  10% {
    opacity: 1;
    width: 0;
  }
  20% {
    width: 100px;
  }
  100% {
    transform: translateX(calc(100vw + 100px)) translateY(100px) rotate(-35deg);
    opacity: 0;
    width: 0;
  }
}

.shooting-star {
  position: absolute;
  height: 1px;
  background: linear-gradient(to right, transparent, #9b87f5, #33C3F0, transparent);
  animation: shooting 12s linear infinite;
  transform: translateX(-100px) rotate(-35deg);
}

/* Cosmic content styling */
:deep(.cosmic-content) {
  color: rgba(255, 255, 255, 0.9);
}

:deep(.cosmic-content h1),
:deep(.cosmic-content h2),
:deep(.cosmic-content h3),
:deep(.cosmic-content h4),
:deep(.cosmic-content h5),
:deep(.cosmic-content h6) {
  color: #9b87f5;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

:deep(.cosmic-content p) {
  margin-bottom: 0.75rem;
  line-height: 1.6;
}

:deep(.cosmic-content ul),
:deep(.cosmic-content ol) {
  margin-left: 1.5rem;
  margin-bottom: 1rem;
}

:deep(.cosmic-content li) {
  margin-bottom: 0.25rem;
}

:deep(.cosmic-content ul li::marker) {
  content: '★ ';
  color: #33C3F0;
}

:deep(.cosmic-content img) {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
  margin: 1rem 0;
  border: 1px solid rgba(155, 135, 245, 0.3);
}

@media (min-width: 768px) {
  :deep(.cosmic-content) {
    font-size: 18px;
  }
  
  :deep(.cosmic-content p + p)::before,
  :deep(.cosmic-content h2 + p)::before,
  :deep(.cosmic-content h3 + p)::before {
    content: '';
    display: block;
    width: 100%;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(155, 135, 245, 0.3), transparent);
    margin-bottom: 1rem;
  }
}

/* Mobile spacing */
@media (max-width: 767px) {
  :deep(.cosmic-content * + *) {
    margin-top: 0.25rem;
  }
}
</style>

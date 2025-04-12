
<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import CosmicTransition from './CosmicTransition.vue';
import RocketAnimation from './RocketAnimation.vue';
import FooterContent from './FooterContent.vue';
import FooterCopyright from './FooterCopyright.vue';
import { Link } from '@inertiajs/vue3';

// Footer link configuration
const quickLinks = [
  { name: 'Home', icon: 'home', route: 'dashboard' },
  { name: 'Products', icon: 'package', route: 'dashboard' },
  { name: 'About', icon: 'info', route: 'dashboard' },
];

const supportLinks = [
  { name: 'Contact', icon: 'mail', route: 'dashboard' },
  { name: 'FAQ', icon: 'help-circle', route: 'dashboard' },
  { name: 'Terms', icon: 'file-text', route: 'dashboard' },
];

const socialLinks = [
  { name: 'Facebook', icon: 'facebook', url: '#' },
  { name: 'Instagram', icon: 'instagram', url: '#' },
  { name: 'Twitter', icon: 'twitter', url: '#' },
];

// Transition visibility state
const isTransitionVisible = ref(false);

// Scroll handler for the transition effect
const handleScroll = () => {
  const footer = document.getElementById('cosmic-footer');
  if (!footer) return;
  
  const footerPosition = footer.getBoundingClientRect().top;
  const triggerPoint = window.innerHeight - 200;
  
  isTransitionVisible.value = footerPosition < triggerPoint;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  // Initial check
  handleScroll();
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
  <footer class="relative mt-16" id="cosmic-footer">
    <!-- Cosmic transition effect -->
    <CosmicTransition :is-visible="isTransitionVisible" />
    
    <!-- Rocket animation -->
    <RocketAnimation />
    
    <!-- Main footer content -->
    <div class="relative bg-gradient-to-b from-dark-lighter to-dark pt-16 pb-8 overflow-hidden">
      <div class="container px-4 mx-auto max-w-7xl">
        <FooterContent 
          :quick-links="quickLinks" 
          :support-links="supportLinks"
          :social-links="socialLinks"
        />
      </div>
      
      <!-- Copyright bar -->
      <FooterCopyright />
    </div>
  </footer>
</template>

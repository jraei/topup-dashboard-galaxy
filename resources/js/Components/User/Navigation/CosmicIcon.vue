
<script setup>
import { h } from 'vue';
import { 
  Home, 
  Package, 
  Info, 
  Mail, 
  HelpCircle, 
  FileText,
  Facebook,
  Instagram,
  Twitter,
  Send
} from 'lucide-vue-next';

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  class: {
    type: String,
    default: ''
  }
});

// Icon mapping
const icons = {
  // Navigation icons
  home: Home,
  package: Package,
  info: Info,
  mail: Mail,
  'help-circle': HelpCircle,
  'file-text': FileText,
  
  // Social icons
  facebook: Facebook,
  instagram: Instagram,
  twitter: Twitter,
  
  // Utility icons
  send: Send
};

// Render the appropriate icon or emoji fallback
const renderIcon = () => {
  const IconComponent = icons[props.name];
  
  if (IconComponent) {
    return h(IconComponent, {
      class: props.class,
      size: 24,
      'stroke-width': 1.5
    });
  }
  
  // Emoji fallback
  return h('span', { class: props.class }, props.name);
};
</script>

<template>
  <component :is="renderIcon()" />
</template>

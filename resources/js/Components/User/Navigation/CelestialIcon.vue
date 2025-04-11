
<script setup>
import { computed } from 'vue';
import { 
    Home, 
    Gamepad2, 
    Zap, 
    Gift, 
    LifeBuoy,
    Search,
    Globe,
    User,
    Rocket,
    Menu,
    X,
    ChevronDown
} from 'lucide-vue-next';

const props = defineProps({
    name: {
        type: String,
        required: true
    },
    size: {
        type: Number,
        default: 18
    },
    active: {
        type: Boolean,
        default: false
    }
});

const iconMap = {
    'Home': Home,
    'GameController': Gamepad2,
    'Zap': Zap,
    'Gift': Gift,
    'LifeBuoy': LifeBuoy,
    'Search': Search,
    'Globe': Globe,
    'User': User,
    'Rocket': Rocket,
    'Menu': Menu,
    'X': X,
    'ChevronDown': ChevronDown
};

const component = computed(() => {
    return iconMap[props.name] || null;
});
</script>

<template>
    <div class="celestial-icon relative" :class="{ 'is-active': active }">
        <div class="relative z-10">
            <component 
                :is="component" 
                :size="size" 
                v-if="component"
                class="transition-all duration-300"
                :class="{ 'text-primary': active, 'text-secondary': !active }"
            />
        </div>
        
        <!-- Glow effect for active state -->
        <div 
            v-if="active" 
            class="absolute inset-0 bg-primary opacity-20 blur-md rounded-full animate-pulse-slow"
        ></div>
        
        <!-- Particle trail on hover (visible in CSS) -->
        <div class="particle-trail"></div>
    </div>
</template>

<style scoped>
.celestial-icon {
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

.celestial-icon:hover .particle-trail::before,
.celestial-icon:hover .particle-trail::after {
    opacity: 0.6;
}

.particle-trail::before,
.particle-trail::after {
    content: '';
    position: absolute;
    width: 2px;
    height: 2px;
    border-radius: 50%;
    background-color: var(--color-primary);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.particle-trail::before {
    bottom: -4px;
    left: 40%;
    box-shadow: 0 0 4px var(--color-primary);
}

.particle-trail::after {
    bottom: -8px;
    left: 60%;
    box-shadow: 0 0 6px var(--color-primary);
}
</style>

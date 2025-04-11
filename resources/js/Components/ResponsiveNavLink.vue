
<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import CelestialIcon from '@/Components/User/Navigation/CelestialIcon.vue';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    icon: {
        type: String,
        default: '',
    }
});

const classes = computed(() => {
    return props.active
        ? 'bg-primary/10 border-l-4 border-primary text-white'
        : 'border-l-4 border-transparent text-white/80 hover:text-white hover:bg-primary/5 hover:border-primary/30';
});
</script>

<template>
    <Link
        :href="href"
        :class="classes"
        class="flex items-center w-full py-3 px-3 transition duration-150 ease-in-out focus:outline-none focus:bg-primary/10 rounded-lg relative overflow-hidden"
    >
        <div v-if="icon" class="mr-3">
            <CelestialIcon :name="icon" :active="active" />
        </div>
        <div class="flex-grow">
            <slot />
        </div>
        
        <!-- Cosmic background effect -->
        <div 
            class="absolute inset-0 bg-gradient-to-r from-transparent via-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            :class="{ 'opacity-30': active }"
        ></div>
        
        <!-- Cosmic particle for active state -->
        <div 
            v-if="active" 
            class="absolute right-3 w-1.5 h-1.5 rounded-full bg-primary shadow-glow-primary animate-pulse-slow"
        ></div>
    </Link>
</template>

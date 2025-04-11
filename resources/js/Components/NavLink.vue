
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
        ? 'border-primary text-primary transition duration-150 ease-in-out focus:border-primary'
        : 'border-transparent text-white/80 hover:text-white hover:border-secondary transition duration-150 ease-in-out focus:border-secondary';
});
</script>

<template>
    <Link :href="href" :class="classes" class="group inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
        <div class="relative flex items-center space-x-1">
            <CelestialIcon v-if="icon" :name="icon" :active="active" class="mr-1.5" />
            <slot />
            
            <!-- Cosmic trail effect (only visible on hover) -->
            <div class="cosmic-trail absolute bottom-0 left-0 w-full h-0.5 opacity-0 group-hover:opacity-100">
                <div class="absolute bottom-0 left-0 w-0 h-full bg-primary group-hover:w-full transition-all duration-500 ease-out"></div>
                <div class="absolute -bottom-1 left-1/2 w-1 h-1 bg-primary rounded-full opacity-0 group-hover:opacity-100 transition-opacity delay-300"></div>
                <div class="absolute -bottom-2 left-1/4 w-0.5 h-0.5 bg-primary rounded-full opacity-0 group-hover:opacity-70 transition-opacity delay-500"></div>
            </div>
        </div>
    </Link>
</template>

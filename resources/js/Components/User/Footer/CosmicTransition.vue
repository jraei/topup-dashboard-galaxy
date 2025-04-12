
<script setup>
defineProps({
    isVisible: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div class="absolute inset-0 w-full pointer-events-none">
        <!-- Atmospheric Transition Effect -->
        <div class="absolute left-0 right-0 bottom-0 h-96 bg-gradient-to-b from-transparent via-content_background/80 to-dark/90"></div>
        
        <!-- Parallax Stars (Three Layers) -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Distant Stars (Slowest) -->
            <div class="absolute inset-0" :class="{ 'star-field-slow': isVisible }">
                <div v-for="i in 50" :key="`star-slow-${i}`" 
                    class="absolute rounded-full bg-white"
                    :style="{
                        width: `${1 + Math.random()}px`,
                        height: `${1 + Math.random()}px`,
                        top: `${Math.random() * 100}%`,
                        left: `${Math.random() * 100}%`,
                        opacity: 0.3 + Math.random() * 0.7,
                        animationDelay: `${Math.random() * 5}s`
                    }">
                </div>
            </div>
            
            <!-- Mid-distance Stars (Medium Speed) -->
            <div class="absolute inset-0" :class="{ 'star-field-medium': isVisible }">
                <div v-for="i in 30" :key="`star-medium-${i}`" 
                    class="absolute rounded-full bg-white"
                    :style="{
                        width: `${1 + Math.random() * 1.5}px`,
                        height: `${1 + Math.random() * 1.5}px`,
                        top: `${Math.random() * 100}%`,
                        left: `${Math.random() * 100}%`,
                        opacity: 0.4 + Math.random() * 0.6,
                        animationDelay: `${Math.random() * 3}s`
                    }">
                </div>
            </div>
            
            <!-- Near Stars (Fastest) -->
            <div class="absolute inset-0" :class="{ 'star-field-fast': isVisible }">
                <div v-for="i in 20" :key="`star-fast-${i}`" 
                    class="absolute rounded-full bg-white"
                    :style="{
                        width: `${1.5 + Math.random() * 2}px`,
                        height: `${1.5 + Math.random() * 2}px`,
                        top: `${Math.random() * 100}%`,
                        left: `${Math.random() * 100}%`,
                        opacity: 0.5 + Math.random() * 0.5,
                        animationDelay: `${Math.random() * 2}s`
                    }">
                </div>
            </div>
        </div>
        
        <!-- Space Dust -->
        <div class="absolute inset-0 overflow-hidden">
            <div v-for="i in 80" :key="`dust-${i}`" 
                class="absolute rounded-full bg-white"
                :style="{
                    width: `${0.5 + Math.random()}px`,
                    height: `${0.5 + Math.random()}px`,
                    top: `${Math.random() * 100}%`,
                    left: `${Math.random() * 100}%`,
                    opacity: 0.05 + Math.random() * 0.15
                }">
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes twinkle {
    0%, 100% { opacity: 0.1; }
    50% { opacity: 1; }
}

@keyframes parallax-slow {
    from { transform: translateY(0); }
    to { transform: translateY(20px); }
}

@keyframes parallax-medium {
    from { transform: translateY(0); }
    to { transform: translateY(40px); }
}

@keyframes parallax-fast {
    from { transform: translateY(0); }
    to { transform: translateY(60px); }
}

.star-field-slow {
    animation: parallax-slow 15s linear infinite alternate;
}

.star-field-medium {
    animation: parallax-medium 10s linear infinite alternate;
}

.star-field-fast {
    animation: parallax-fast 8s linear infinite alternate;
}

.star-field-slow > div,
.star-field-medium > div,
.star-field-fast > div {
    animation: twinkle 5s infinite ease-in-out;
}
</style>

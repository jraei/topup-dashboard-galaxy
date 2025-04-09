
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    eventName: {
        type: String,
        required: true
    },
    endTime: {
        type: Number,
        required: true
    },
    serverTime: {
        type: String,
        required: true
    }
});

const now = ref(new Date().getTime());
const timeOffset = ref(0);

// Calculate time difference between server and client
onMounted(() => {
    const serverTimeMs = new Date(props.serverTime).getTime();
    const clientTimeMs = new Date().getTime();
    timeOffset.value = serverTimeMs - clientTimeMs;
    
    // Start timer
    startTimer();
});

// Compute remaining time with server-client time sync
const remainingTime = computed(() => {
    const actualTime = now.value + timeOffset.value;
    const diff = props.endTime - actualTime;
    
    if (diff <= 0) {
        return { days: 0, hours: 0, minutes: 0, seconds: 0, total: 0 };
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    return {
        days,
        hours,
        minutes,
        seconds,
        total: diff
    };
});

// Check if less than 1 hour remaining
const isAlmostEnding = computed(() => {
    return remainingTime.value.total > 0 && remainingTime.value.total < 3600000; // 1 hour in ms
});

let timer = null;

const startTimer = () => {
    timer = setInterval(() => {
        now.value = new Date().getTime();
    }, 1000);
};

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

// Format number to always have 2 digits
const formatNumber = (num) => {
    return num.toString().padStart(2, '0');
};
</script>

<template>
    <div class="mb-6">
        <h2 class="text-2xl md:text-3xl font-bold flex items-center mb-2">
            <!-- Lightning bolt icon -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                class="h-8 w-8 text-primary mr-2 animate-pulse-slow" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2"
                stroke-linecap="round" 
                stroke-linejoin="round"
            >
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
            </svg>
            <span class="text-primary font-bold" :class="{'animate-pulse': isAlmostEnding}">
                {{ eventName }} 
            </span>
        </h2>
        
        <!-- Countdown timer -->
        <div class="flex justify-start items-center space-x-3 mb-2">
            <div class="timer-container flex space-x-1.5">
                <!-- Days -->
                <div class="flex flex-col items-center">
                    <div class="timer-card" :class="{'text-red-500': isAlmostEnding}">
                        {{ formatNumber(remainingTime.days) }}
                    </div>
                    <span class="text-xs text-gray-300">Hari</span>
                </div>
                
                <div class="text-lg font-bold text-white self-start mt-1">:</div>
                
                <!-- Hours -->
                <div class="flex flex-col items-center">
                    <div class="timer-card" :class="{'text-red-500': isAlmostEnding}">
                        {{ formatNumber(remainingTime.hours) }}
                    </div>
                    <span class="text-xs text-gray-300">Jam</span>
                </div>
                
                <div class="text-lg font-bold text-white self-start mt-1">:</div>
                
                <!-- Minutes -->
                <div class="flex flex-col items-center">
                    <div class="timer-card" :class="{'text-red-500': isAlmostEnding}">
                        {{ formatNumber(remainingTime.minutes) }}
                    </div>
                    <span class="text-xs text-gray-300">Menit</span>
                </div>
                
                <div class="text-lg font-bold text-white self-start mt-1">:</div>
                
                <!-- Seconds -->
                <div class="flex flex-col items-center">
                    <div class="timer-card" :class="{'text-red-500': isAlmostEnding}">
                        {{ formatNumber(remainingTime.seconds) }}
                    </div>
                    <span class="text-xs text-gray-300">Detik</span>
                </div>
            </div>
        </div>
        
        <!-- Subtext with pulsing animation -->
        <p class="text-sm text-primary-text animate-pulse-slow">
            Pesan sekarang! Persediaan terbatas.
        </p>
    </div>
</template>

<style scoped>
.timer-card {
    background-color: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(155, 135, 245, 0.3);
    border-radius: 4px;
    padding: 4px 8px;
    min-width: 40px;
    text-align: center;
    color: white;
    font-weight: bold;
    font-size: 1.25rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(155, 135, 245, 0.3);
}

.timer-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(155, 135, 245, 0.2), transparent);
    animation: shimmer 2s infinite;
    z-index: 1;
}

@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse-slow {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>

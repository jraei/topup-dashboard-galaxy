
<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";

const props = defineProps({
    eventName: {
        type: String,
        required: true,
    },
    endTime: {
        type: Number,
        required: true,
    },
    serverTime: {
        type: String,
        required: true,
    },
});

const remainingTime = ref({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
});

let countdownInterval = null;
const serverClientDiff = ref(0);

// Sync client time with server time
onMounted(() => {
    const serverDate = new Date(props.serverTime).getTime();
    const clientDate = Date.now();
    serverClientDiff.value = serverDate - clientDate;

    startCountdown();
});

onUnmounted(() => {
    clearInterval(countdownInterval);
});

const startCountdown = () => {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
};

const updateCountdown = () => {
    const now = Date.now() + serverClientDiff.value;
    const timeLeft = props.endTime - now;

    if (timeLeft <= 0) {
        remainingTime.value = {
            days: 0,
            hours: 0,
            minutes: 0,
            seconds: 0,
        };
        clearInterval(countdownInterval);
        return;
    }

    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor(
        (timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
    );
    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

    remainingTime.value = { days, hours, minutes, seconds };
};

// Check if less than 1 hour remains (for alert state)
const isAlmostEnding = computed(() => {
    return (
        remainingTime.value.days === 0 && remainingTime.value.hours === 0
    );
});

// Format numbers with leading zeros
const formatNumber = (num) => {
    return num.toString().padStart(2, "0");
};
</script>

<template>
    <div class="mb-6">
        <!-- Event Title with Lightning Bolt Icon -->
        <div class="flex items-center mb-2 space-x-2">
            <div class="lightning-icon-container">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="lightning-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary glow-text">{{ eventName }}</h2>
        </div>

        <!-- Enhanced Countdown Timer -->
        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
            <!-- Segmented Display -->
            <div 
                class="countdown-container" 
                :class="{ 'alert-state': isAlmostEnding }"
            >
                <div class="countdown-segments">
                    <!-- Days -->
                    <div class="countdown-segment" v-if="remainingTime.days > 0">
                        <div class="segment-card">{{ formatNumber(remainingTime.days) }}</div>
                        <div class="segment-label">Hari</div>
                    </div>

                    <!-- Hours -->
                    <div class="countdown-segment">
                        <div class="segment-card">{{ formatNumber(remainingTime.hours) }}</div>
                        <div class="segment-label">Jam</div>
                    </div>

                    <!-- Minutes -->
                    <div class="countdown-segment">
                        <div class="segment-card">{{ formatNumber(remainingTime.minutes) }}</div>
                        <div class="segment-label">Menit</div>
                    </div>

                    <!-- Seconds -->
                    <div class="countdown-segment">
                        <div class="segment-card">{{ formatNumber(remainingTime.seconds) }}</div>
                        <div class="segment-label">Detik</div>
                    </div>
                </div>
            </div>

            <!-- Pulsing Subtext -->
            <div class="pulsing-text">
                Pesan sekarang! Persediaan terbatas.
            </div>
        </div>
    </div>
</template>

<style scoped>
.glow-text {
    text-shadow: 0 0 10px rgba(155, 135, 245, 0.7);
}

.lightning-icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(155, 135, 245, 0.15);
    border-radius: 50%;
    animation: pulse-glow 2s infinite alternate;
    border: 1px solid rgba(155, 135, 245, 0.3);
}

.lightning-icon {
    width: 24px;
    height: 24px;
    color: #33C3F0; /* secondary color */
    filter: drop-shadow(0 0 5px rgba(51, 195, 240, 0.7));
}

.countdown-container {
    padding: 0.75rem 1rem;
    background-color: rgba(26, 34, 54, 0.8);
    border: 1px solid rgba(155, 135, 245, 0.2);
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.countdown-container.alert-state {
    border-color: rgba(239, 68, 68, 0.5);
    background-color: rgba(239, 68, 68, 0.1);
    animation: alert-pulse 1s infinite alternate;
}

.countdown-segments {
    display: flex;
    gap: 0.75rem;
}

.countdown-segment {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.segment-card {
    background-color: rgba(155, 135, 245, 0.1);
    border: 1px solid rgba(155, 135, 245, 0.3);
    color: white;
    font-weight: bold;
    padding: 0.5rem;
    width: 2.5rem;
    text-align: center;
    border-radius: 0.25rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease;
    box-shadow: 0 0 10px rgba(155, 135, 245, 0.2);
}

.segment-card::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    height: 1px;
    top: 50%;
    background-color: rgba(155, 135, 245, 0.2);
}

.segment-label {
    font-size: 0.7rem;
    color: #cbd5e1;
    margin-top: 0.25rem;
}

.pulsing-text {
    color: #cbd5e1;
    animation: text-pulse 2s infinite;
    font-size: 0.875rem;
    margin-left: 0.5rem;
}

@keyframes pulse-glow {
    0% {
        box-shadow: 0 0 5px rgba(155, 135, 245, 0.3);
    }
    100% {
        box-shadow: 0 0 15px rgba(155, 135, 245, 0.7);
    }
}

@keyframes alert-pulse {
    0% {
        box-shadow: 0 0 5px rgba(239, 68, 68, 0.3);
    }
    100% {
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.7);
    }
}

@keyframes text-pulse {
    0%, 100% {
        opacity: 0.8;
    }
    50% {
        opacity: 1;
        transform: scale(1.03);
    }
}

/* Animated flip card effect when numbers change */
.segment-card {
    position: relative;
    perspective: 1000px;
}

.segment-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, 
        rgba(255, 255, 255, 0.2) 0%, 
        transparent 50%, 
        rgba(0, 0, 0, 0.2) 100%
    );
    pointer-events: none;
}

/* Media queries */
@media (max-width: 640px) {
    .countdown-segments {
        gap: 0.5rem;
    }

    .segment-card {
        width: 2rem;
        padding: 0.25rem;
        font-size: 0.875rem;
    }
}
</style>

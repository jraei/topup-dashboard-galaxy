<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    stepNumber: {
        type: Number,
        default: null,
    },
    totalSteps: {
        type: Number,
        default: 6,
    },
});
</script>

<template>
    <!-- Cosmic Card Component -->
    <div class="mb-8 overflow-hidden rounded-2xl cosmic-card">
        <!-- Card Header -->
        <div class="relative p-4 text-white bg-primary/20">
            <!-- Constellation pattern -->
            <div class="absolute inset-0 constellation-pattern"></div>

            <div class="relative z-10">
                <h3 class="text-lg font-bold md:text-xl">
                    <template v-if="stepNumber !== null">
                        Step {{ stepNumber }} of {{ totalSteps }}:
                    </template>
                    {{ title }}
                </h3>
                <!-- Cosmic progress bar -->
                <div
                    v-if="stepNumber !== null"
                    class="h-1 mt-2 rounded bg-primary/30 cosmic-progress"
                >
                    <div
                        :style="`width: ${(stepNumber / totalSteps) * 100}%`"
                        class="h-full rounded bg-gradient-to-r from-primary to-secondary"
                    >
                        <!-- Micro stars in progress bar -->
                        <div
                            v-for="i in 3"
                            :key="`star-${i}`"
                            class="absolute w-px h-px bg-white rounded-full cosmic-star"
                            :style="{
                                left: `${
                                    (stepNumber / totalSteps) * 100 * (i * 0.3)
                                }%`,
                                top: '50%',
                                transform: 'translate(-50%, -50%)',
                                animationDelay: `${i * 0.5}s`,
                            }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div
            class="relative p-6 border bg-gradient-to-b from-primary/20 to-primary/5 border-secondary/20"
        >
            <!-- Micro-planet border accents -->
            <div
                v-for="i in 6"
                :key="`planet-${i}`"
                class="absolute w-1.5 h-1.5 rounded-full bg-secondary/50 micro-planet"
                :style="{
                    top: `${(i % 3) * 33 + 5}%`,
                    left: i <= 3 ? '3px' : 'auto',
                    right: i > 3 ? '3px' : 'auto',
                    animationDelay: `${i * 0.7}s`,
                }"
            ></div>

            <div class="relative z-10">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cosmic-card {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    will-change: transform, box-shadow;
    border: 1px solid transparent;
    background-clip: padding-box;
    position: relative;
}

.cosmic-card::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    margin: -1px;
    border-radius: inherit;
    background: linear-gradient(
        120deg,
        rgba(51, 195, 240, 0.1),
        rgba(155, 135, 245, 0.3)
    );
    animation: border-pulse 4s ease-in-out infinite;
}

/* .cosmic-card:hover::before {
    animation: warp-field 1.5s ease-out forwards;
} */
.cosmic-card:hover {
    box-shadow: 0 6px 24px rgba(155, 135, 245, 0.2);
}

.constellation-pattern {
    background-image: radial-gradient(
        circle,
        rgba(155, 135, 245, 0.2) 1px,
        transparent 1px
    );
    background-size: 25px 25px;
}

.cosmic-progress {
    overflow: hidden;
    position: relative;
}

.cosmic-star {
    animation: star-twinkle 2s ease-in-out infinite;
}

.micro-planet {
    transform: scale(0.8);
    animation: planet-pulse 3s ease-in-out infinite;
}

@keyframes border-pulse {
    0%,
    100% {
        opacity: 0.3;
    }
    50% {
        opacity: 0.7;
    }
}

@keyframes warp-field {
    0% {
        transform: skewY(0deg);
    }
    50% {
        transform: skewY(0.5deg);
    }
    100% {
        transform: skewY(0deg);
    }
}

@keyframes star-twinkle {
    0%,
    100% {
        opacity: 0.3;
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.5);
    }
}

@keyframes planet-pulse {
    0%,
    100% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.8;
    }
}

/* @media (hover: hover) {
    .cosmic-card:hover {
        transform: skewY(0.5deg) translateY(-2px);
    }
} */
</style>

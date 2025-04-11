<script setup>
import { computed } from "vue";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const productName = computed(() => props.product.nama || "Unnamed Product");
const developer = computed(
    () => props.product.developer || "Unknown Developer"
);
const thumbnail = computed(() => {
    if (props.product.thumbnail && props.product.thumbnail.startsWith("http")) {
        return props.product.thumbnail;
    } else if (props.product.thumbnail) {
        return `/storage/${props.product.thumbnail}`;
    }
    return "/img/default-product.png";
});
</script>

<template>
    <div
        class="relative overflow-hidden transition-all duration-300 ease-out border rounded-lg bg-gradient-to-br from-secondary/10 to-content_background border-primary/20 group hover:border-primary hover:border-2 hover:shadow-glow-primary"
    >
        <!-- Cosmic Comet -->
        <div
            class="absolute inset-0 overflow-hidden pointer-events-none opacity-30 group-hover:opacity-60"
        >
            <div
                class="absolute w-20 h-1 rounded-full comet bg-primary top-1/3 -right-20 shadow-glow-primary animate-comet"
            ></div>

            <div class="absolute inset-0 stars">
                <div
                    class="star animate-twinkle"
                    style="top: 20%; left: 80%; width: 1px; height: 1px"
                ></div>
                <div
                    class="star animate-twinkle"
                    style="
                        top: 60%;
                        left: 30%;
                        width: 2px;
                        height: 2px;
                        animation-delay: 1s;
                    "
                ></div>
                <div
                    class="star animate-twinkle"
                    style="
                        top: 80%;
                        left: 70%;
                        width: 1px;
                        height: 1px;
                        animation-delay: 0.5s;
                    "
                ></div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="flex p-1 h-[80%]">
            <!-- Product Thumbnail -->
            <div class="flex items-center justify-center w-2/5">
                <div
                    class="w-16 h-16 overflow-hidden transition-transform duration-300 ease-out border rounded-lg md:w-24 md:h-24 border-primary/20 group-hover:scale-105"
                >
                    <img
                        :src="thumbnail"
                        :alt="productName"
                        loading="lazy"
                        class="object-cover w-full h-full"
                    />
                </div>
            </div>

            <!-- Product Info -->
            <div class="flex flex-col justify-center w-3/5">
                <h3
                    class="mb-1 text-xs font-bold md:text-lg text-primary-text line-clamp-2"
                >
                    {{ productName }}
                </h3>
                <p class="text-xs md:text-sm text-primary-text/80">
                    {{ developer }}
                </p>
            </div>
        </div>

        <!-- Card Footer -->
        <div
            class="border-t border-primary/20 bg-primary/30 py-2 px-3 h-[20%] flex items-center justify-end"
        ></div>
    </div>
</template>

<style scoped>
.star {
    @apply bg-primary rounded-full absolute;
}

@keyframes twinkle {
    0%,
    100% {
        opacity: 0.2;
        transform: scale(0.8);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.2);
    }
}

@keyframes comet {
    0% {
        transform: translateX(0) translateY(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateX(-400%) translateY(200%);
        opacity: 0;
    }
}

.animate-twinkle {
    animation: twinkle 3s ease-in-out infinite;
}

.animate-comet {
    animation: comet 4s linear infinite;
}

/* Support for reduced motion */
@media (prefers-reduced-motion: reduce) {
    .animate-twinkle,
    .animate-comet {
        animation: none;
    }
}
</style>

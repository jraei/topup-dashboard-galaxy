<script setup>
defineProps({
    banner: String,
});
</script>

<template>
    <div class="relative z-10 mb-16 md:mb-28" v-if="banner">
        <img
            :src="`/storage/${banner}`"
            alt="Product Banner"
            class="object-cover object-center w-full h-full min-h-56 bg-muted lg:object-contain cosmic-banner"
            width="1280"
            height="1280"
            loading="lazy"
        />
    </div>
    <!-- di bawah banner -->
    <!-- <div
        class="absolute bottom-0 left-0 right-0 z-0 h-12 bg-gradient-to-b from-transparent to-bg-content_background"
    ></div> -->
</template>

<style scoped>
.cosmic-banner {
    position: relative;
    z-index: 1; /* Ensure it's below other elements that need to overlap */
}

@media (min-width: 1024px) {
    .cosmic-banner {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
    }
}
</style>

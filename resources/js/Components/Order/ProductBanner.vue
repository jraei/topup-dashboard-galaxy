
<script setup>
defineProps({
    banner: String,
});
</script>

<template>
    <div class="relative" v-if="banner">
        <img
            :src="`/storage/${banner}`"
            alt="Product Banner"
            class="object-cover object-center w-full h-full min-h-56 bg-muted lg:object-contain cosmic-banner"
            width="1280"
            height="1280"
        />
    </div>
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

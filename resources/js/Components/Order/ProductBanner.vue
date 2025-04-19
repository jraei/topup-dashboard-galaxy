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
            class="object-cover object-center w-full h-full min-h-56 bg-muted lg:object-contain"
            width="1280"
            height="1280"
        />
    </div>
</template>

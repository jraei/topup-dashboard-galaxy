
<script setup>
defineProps({
    banner: String,
});
</script>

<template>
    <div v-if="banner" class="relative w-full overflow-hidden lg:w-screen">
        <img
            :src="`/storage/${banner}`"
            alt="Product Banner"
            class="object-cover object-center h-full min-h-56 bg-muted lg:object-contain"
            width="1280"
            height="1280"
        />
    </div>
</template>

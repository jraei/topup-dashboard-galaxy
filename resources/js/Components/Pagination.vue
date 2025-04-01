<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links && links.length > 3" class="mt-4">
        <div class="flex flex-wrap justify-center sm:justify-end">
            <template v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="px-3 py-2 mb-1 mr-1 text-sm text-gray-500 border border-gray-700 rounded-md bg-dark-lighter"
                    v-html="link.label"
                />
                <Link
                    v-else
                    class="px-3 py-2 mb-1 mr-1 text-sm transition-colors border border-gray-700 rounded-md hover:bg-primary/20 focus:border-primary focus:text-primary"
                    :class="{
                        'bg-primary/20 text-primary': link.active,
                        'text-gray-300 bg-dark-lighter': !link.active,
                    }"
                    :href="link.url"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const { produk } = defineProps({
    produk: Object,
    layanans: Object,
    user: Object,
});

console.log(produk);
</script>

<template>
    <p v-if="user">
        Halo {{ user.username }}, role kamu {{ user.user_role_id }}
    </p>
    <ul>
        <li v-for="item in layanans" :key="item.id">
            Nama: {{ item.nama_layanan }} Harga: {{ item.harga_beli }} Harga
            Jual : {{ item.harga_jual }}
        </li>
    </ul>
</template>

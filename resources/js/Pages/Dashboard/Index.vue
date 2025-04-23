
<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import DashboardSidebar from "@/Components/Dashboard/Sidebar.vue";
import { ref } from "vue";

const sidebarCollapsed = ref(false);
function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
}
</script>

<template>
    <GuestLayout>
        <div class="flex flex-col lg:flex-row min-h-screen bg-gradient-to-br from-[#151b32] to-[#123266]">
            <!-- Sidebar (collapsible) -->
            <DashboardSidebar :collapsed="sidebarCollapsed" @toggle="toggleSidebar" />

            <!-- Main Dashboard Content -->
            <div class="flex-1 p-4 lg:p-8 transition-all duration-300">
                <!-- Security Callout -->
                <div class="mb-6">
                    <div class="flex items-center p-4 bg-primary/20 rounded-lg shadow text-primary gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-secondary animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-opacity="0.15" fill="none"/>
                                <rect x="9" y="9" width="6" height="6" rx="1" class="fill-secondary opacity-30"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-primary-text">Tingkatkan keamanan!</div>
                            <div class="text-xs text-white/70">Gunakan fitur 2FA agar akun kamu lebih aman. Klik <a href="#" class="underline text-secondary">disini</a> untuk melakukan pengaturan!</div>
                        </div>
                    </div>
                </div>

                <!-- Main Cards Row -->
                <div class="flex flex-col lg:flex-row gap-6 mb-6">
                    <!-- Profile Card -->
                    <div class="flex-1 lg:max-w-sm">
                        <div class="bg-[#192854] rounded-2xl shadow-md p-6 flex items-center gap-5 relative min-h-[170px]">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-secondary to-primary border-4 border-white flex items-center justify-center text-3xl uppercase text-white shadow ring-2 ring-primary">
                                JU
                            </div>
                            <div>
                                <div class="font-semibold text-xl text-white">Justin</div>
                                <div class="text-xs text-secondary uppercase">Member</div>
                                <div class="text-sm text-white/80 mt-2 flex items-center gap-2">
                                    <span>&#x260E;</span> <span>+6285931018333</span>
                                </div>
                            </div>
                            <button class="absolute top-2 right-2 text-secondary hover:text-primary">
                                <svg width="22" height="22" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="9" stroke="currentColor" /><path d="M11 7v4l2 2" stroke="currentColor" stroke-width="2"/></svg>
                            </button>
                        </div>
                    </div>
                    <!-- Balance Card -->
                    <div class="flex-1">
                        <div class="relative bg-[#192854] rounded-2xl shadow-md p-6 flex flex-col gap-3 min-h-[170px]">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2 font-semibold text-secondary"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M16 12l-4 4-4-4"/></svg> VSCredits</div>
                                <div class="flex gap-2">
                                    <button class="bg-gradient-to-r from-primary to-secondary text-white rounded-lg px-4 py-1 text-sm font-bold shadow hover:scale-105 transition">Top Up</button>
                                    <button class="bg-gray-500/60 text-white rounded-lg px-4 py-1 text-sm font-semibold hover:bg-gray-700/80 transition">Redeem</button>
                                </div>
                            </div>
                            <div class="flex items-end flex-wrap gap-1">
                                <div class="text-2xl font-bold text-white">0 <span class="text-base text-secondary font-normal">VSC</span></div>
                                <div class="text-xs text-white/50 ml-2">IDR</div>
                            </div>
                            <div class="h-10 mt-3">
                                <!-- Placeholder for micro-graph (empty for now) -->
                                <div class="w-full h-full rounded bg-gradient-to-r from-secondary/30 to-primary/10"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Overview -->
                <div>
                    <div class="mb-3 text-lg font-bold text-white">Transaksi Hari Ini</div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="rounded-xl bg-[#173461] text-white flex flex-col items-center p-5">
                            <div class="text-lg font-bold">0</div>
                            <div class="text-xs mt-2 text-white/70">Total Transaksi</div>
                        </div>
                        <div class="rounded-xl bg-[#173461] text-white flex flex-col items-center p-5">
                            <div class="text-lg font-bold">0</div>
                            <div class="text-xs mt-2 text-white/70">Total Penjualan</div>
                        </div>
                        <div class="rounded-xl bg-yellow-600 text-white flex flex-col items-center p-5">
                            <div class="text-lg font-bold">0</div>
                            <div class="text-xs mt-2">Menunggu</div>
                        </div>
                        <div class="rounded-xl bg-green-700 text-white flex flex-col items-center p-5">
                            <div class="text-lg font-bold">0</div>
                            <div class="text-xs mt-2">Sukses</div>
                        </div>
                        <div class="rounded-xl bg-red-700 text-white flex flex-col items-center p-5">
                            <div class="text-lg font-bold">0</div>
                            <div class="text-xs mt-2">Gagal</div>
                        </div>
                    </div>
                </div>

                <!-- Latest Transaction Table -->
                <div class="mt-8">
                    <div class="mb-3 text-lg font-semibold text-white">Riwayat Transaksi Terbaru Hari Ini</div>
                    <div class="overflow-x-auto rounded-lg shadow">
                        <table class="w-full min-w-[600px] bg-[#142853] text-primary-text">
                            <thead>
                                <tr class="bg-primary/10 text-secondary">
                                    <th class="p-3 text-left">Nomor Invoice</th>
                                    <th class="p-3 text-left">ID Trx</th>
                                    <th class="p-3 text-left">Item</th>
                                    <th class="p-3 text-left">User Input</th>
                                    <th class="p-3 text-right">Harga</th>
                                    <th class="p-3 text-left">Tanggal</th>
                                    <th class="p-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-white/5">
                                    <td class="p-3">-</td>
                                    <td class="p-3">-</td>
                                    <td class="p-3">-</td>
                                    <td class="p-3">-</td>
                                    <td class="p-3 text-right">-</td>
                                    <td class="p-3">-</td>
                                    <td class="p-3">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

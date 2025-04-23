<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import DashboardSidebar from "@/Components/Dashboard/Sidebar.vue";
</script>

<template>
    <GuestLayout>
        <div class="flex min-h-screen mx-auto bg-transparent max-w-7xl">
            <DashboardSidebar />
            <div class="flex-1 p-6">
                <h1 class="mb-6 text-2xl font-bold text-white">Balance</h1>
                <!-- Data Table Placeholder -->
                <div
                    class="p-6 shadow-md bg-secondary/20 rounded-2xl text-secondary"
                >
                    <!-- Placeholder for future DataTable with filters -->
                    Data table for transaksi coming soon.
                </div>
            </div>
        </div>
    </GuestLayout>
</template>


<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";
  
const props = defineProps({
    balance: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(["topup", "redeem"]);

function formatCurrency(amount) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(amount);
}

</script>

<template>
    <div class="relative p-6 overflow-hidden rounded-2xl shadow-xl bg-gradient-to-b from-primary/30 to-content-background/80 border border-secondary/10 mb-8">
        <div class="absolute inset-0 pointer-events-none select-none animate-fade-in z-0">
            <!-- Subtle animated cosmic particles background -->
            <svg class="w-full h-full opacity-50" viewBox="0 0 400 140" preserveAspectRatio="none">
                <circle v-for="n in 12" :key="n"
                    :cx="Math.random()*400"
                    :cy="Math.random()*140"
                    :r="n%3 === 0 ? 2 : 1.2"
                    :fill="n%2==0 ? '#9b87f577' : '#33C3F077'">
                    <animate attributeName="cx" from="0" :to="400" dur="9s" repeatCount="indefinite"/>
                </circle>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col items-start sm:flex-row sm:justify-between sm:items-center">
            <div>
                <div class="uppercase text-xs tracking-widest text-secondary font-semibold mb-1">Balance</div>
                <div class="text-3xl md:text-4xl font-bold text-white mb-2" data-testid="balance-amount">
                    {{ formatCurrency(balance) }}
                </div>
                <div class="text-xs text-secondary/80">
                    Your NaelCoin balance
                </div>
            </div>
            <div class="flex flex-col gap-2 mt-6 sm:mt-0 sm:flex-row">
                <PrimaryButton @click="$emit('topup')" class="flex-1 shadow-glow transition pulse animate-fade-in">
                    <span class="inline-flex items-center">
                        <svg class="w-5 h-5 mr-1 -ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v12M6 12h12" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        Topup
                    </span>
                </PrimaryButton>
                <PrimaryButton @click="$emit('redeem')" class="flex-1 bg-secondary/90 hover:bg-secondary text-gray-900 shadow-glow-secondary transition">
                    <span class="inline-flex items-center">
                        <svg class="w-5 h-5 mr-1 -ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        Redeem Voucher
                    </span>
                </PrimaryButton>
            </div>
        </div>
        <!-- Decorative flying planet -->
        <svg class="absolute -bottom-10 -right-10 w-36 opacity-40 rotate-6 pointer-events-none" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="26" fill="#9b87f5" fill-opacity="0.2"/>
            <ellipse cx="62" cy="40" rx="8" ry="4" fill="#33C3F0" fill-opacity="0.28"/>
        </svg>
    </div>
</template>

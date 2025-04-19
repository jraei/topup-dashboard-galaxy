
<script setup>
import { ref, computed, watch } from "vue";
import { useToast } from "@/Composables/useToast";
import CosmicCard from "@/Components/Order/CosmicCard.vue";

const props = defineProps({
    staticMethods: Object,
    dynamicMethods: Object,
    selectedService: Object,
    selectedPayment: [Object, String, Number, null],
    basePrice: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(["update:selectedPayment", "update:fee"]);

// Utility for fee calculation (mirrors backend)
function calculateTotal(price, fee, feeType) {
    if (feeType === "percent") {
        return Math.round(price * (1 + fee / 100));
    }
    return price + fee;
}

// UI state
const expandedGroups = ref([]);

// Returns visual best price badge
const BestPriceBadge = () => (
    <span class="absolute top-2 right-2 px-2 py-1 bg-primary/90 text-primary_text text-xs font-bold rounded shadow pulse z-20 select-none" style="background-image: repeating-linear-gradient(45deg,rgba(51,195,240,0.2) 0 3px,transparent 0 8px);">BEST PRICE</span>
);

function selectStatic(type) {
    emit("update:selectedPayment", { type, channel: null });
    emit("update:fee", {
        fee: props.staticMethods[type]?.fee ?? 0,
        feeType: props.staticMethods[type]?.fee_type ?? "fixed",
        methodLabel: type === "saldo" ? "NaelCoin" : "QRIS (Semua Pembayaran)",
        image: props.staticMethods[type]?.gambar,
    });
}

function selectDynamic(group, method) {
    emit("update:selectedPayment", { type: group, channel: method.id });
    emit("update:fee", {
        fee: method.fee,
        feeType: method.fee_type,
        methodLabel: method.nama,
        image: method.gambar,
    });
}

const isSelected = (payment) => {
    if (!props.selectedPayment) return false;
    if (payment.type) return (props.selectedPayment.type === payment.type && !props.selectedPayment.channel);
    return (props.selectedPayment.type === payment.group && props.selectedPayment.channel === payment.id);
};
</script>

<template>
    <CosmicCard :title="'Select Payment'" :step-number="4">
        <div class="space-y-4">
            <!-- Static Methods -->
            <div class="flex flex-col md:flex-row gap-4 relative">
                <!-- NaelCoin -->
                <button
                    @click="selectStatic('saldo')"
                    type="button"
                    class="relative group flex-1 min-w-0 bg-dark rounded-lg p-4 pr-8 flex items-center shadow-glow-primary transition-all outline-none border-2"
                    :class="[isSelected({type:'saldo'}) ? 'border-primary animate-pulse ring-2 ring-primary/40' : 'border-dark']"
                >
                    <img :src="staticMethods.saldo?.gambar" alt="NaelCoin" class="h-10 w-10 rounded mr-4" />
                    <span class="flex-1 font-bold text-primary_text text-left">NaelCoin</span>
                    <span class="ml-auto text-secondary text-lg font-bold">
                        {{ calculateTotal(basePrice, staticMethods.saldo?.fee??0, staticMethods.saldo?.fee_type??'fixed').toLocaleString() }}
                    </span>
                    <BestPriceBadge v-if="true" />
                </button>
                <!-- QRIS -->
                <button
                    @click="selectStatic('qris')"
                    type="button"
                    class="relative group flex-1 min-w-0 bg-dark rounded-lg p-4 pr-8 flex items-center shadow-glow-secondary transition-all outline-none border-2"
                    :class="[isSelected({type:'qris'}) ? 'border-secondary animate-pulse ring-2 ring-secondary/40' : 'border-dark']"
                >
                    <img :src="staticMethods.qris?.gambar" alt="QRIS" class="h-10 w-10 rounded mr-4" />
                    <span class="flex-1 font-bold text-primary_text text-left">QRIS (Semua Pembayaran)</span>
                    <span class="ml-auto text-primary text-lg font-bold">
                        {{ calculateTotal(basePrice, staticMethods.qris?.fee??0, staticMethods.qris?.fee_type??'fixed').toLocaleString() }}
                    </span>
                </button>
            </div>

            <!-- Dynamic Payment Groups -->
            <div v-for="(group, tipe) in dynamicMethods" :key="tipe">
                <div class="rounded-lg overflow-hidden mb-3 border-t-2 border-b-2 border-dark">
                    <!-- Collapsed Header -->
                    <button
                        type="button"
                        class="w-full flex items-center justify-between bg-dark/80 hover:bg-primary/5 transition-all p-4 relative"
                        @click="expandedGroups = expandedGroups.includes(tipe) ? expandedGroups.filter(g => g!==tipe) : [...expandedGroups, tipe]"
                    >
                        <div class="flex items-center space-x-3">
                            <span class="font-semibold text-primary_text">{{ tipe }}</span>
                            <!-- Icon collage -->
                            <div class="flex -space-x-2">
                                <img v-for="meth in group.slice(0,3)" :key="meth.id" :src="meth.gambar" class="inline h-7 w-7 rounded-full border-2 border-dark bg-black/40 shadow" :alt="meth.nama" />
                            </div>
                        </div>
                        <div class="text-secondary font-bold flex items-center space-x-1 text-sm">
                            <span>
                                {{
                                    Math.min(...group.map(m => calculateTotal(basePrice, m.fee, m.fee_type))).toLocaleString()
                                }}
                            </span>
                            <span class="text-xs">min</span>
                        </div>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-secondary">
                            <svg v-if="expandedGroups.includes(tipe)" xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </span>
                    </button>
                    <!-- Expanded Body -->
                    <transition name="fade">
                    <div v-if="expandedGroups.includes(tipe)" class="bg-dark/90 px-2 pb-3 pt-2 grid grid-cols-1 md:grid-cols-2 gap-3 cosmic-card-border">
                        <div
                            v-for="meth in group"
                            :key="meth.id"
                            @click="selectDynamic(tipe, meth)"
                            class="cursor-pointer group relative bg-content_background rounded-md flex px-3 py-3 border-2 transition-all"
                            :class="isSelected({group: tipe, id: meth.id}) ? 'border-primary animate-pulse ring-2 ring-primary/40 shadow-glow-primary' : 'border-dark'"
                        >
                            <img :src="meth.gambar" alt="" class="w-8 h-8 mr-3 rounded bg-dark/70 border border-dark" loading="lazy" />
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-primary_text">{{ meth.nama }}</span>
                                    <span v-if="meth.is_recommended" class="bg-secondary/20 text-secondary px-1 rounded text-xs font-bold cosmic-pulse ml-2">Recommended</span>
                                </div>
                                <div class="text-xs text-secondary/80">
                                    {{ meth.fee_type === 'percent'
                                        ? `Fee: ${meth.fee}%`
                                        : `Fee: ${meth.fee?.toLocaleString()}`
                                    }}
                                </div>
                                <div class="text-sm mt-1 font-bold text-primary">
                                    Total: {{ calculateTotal(basePrice, meth.fee, meth.fee_type).toLocaleString() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    </transition>
                </div>
            </div>
        </div>
    </CosmicCard>
</template>

<style scoped>
.shadow-glow-primary { box-shadow: 0 0 12px 0 #9b87f566 !important; }
.shadow-glow-secondary { box-shadow: 0 0 12px 0 #33C3F088 !important; }
.cosmic-card-border { transition: box-shadow .28s cubic-bezier(.4,0,.2,1), border .25s cubic-bezier(.4,0,.2,1); }
.cosmic-pulse {
    animation: cosmicPulse 1s infinite;
}
@keyframes cosmicPulse {
    0%, 100% { opacity: .8; }
    50% { opacity: 1; box-shadow: 0 0 6px 2px #33c3f044;}
}
.pulse {
    animation: pulse 1s cubic-bezier(.4,0,.6,1) infinite;
}
</style>

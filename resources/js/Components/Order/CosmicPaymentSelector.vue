
<script setup>
import { ref, computed, watch } from "vue";
import CosmicCard from "./CosmicCard.vue";

// Props: staticMethods, dynamicGroups, basePrice, modelValue, emits
const props = defineProps({
    staticMethods: Object,
    dynamicGroups: Object,
    basePrice: Number,
    modelValue: Object, // { type: 'static'|'dynamic', code: string, channelId: number|null }
});
const emit = defineEmits(["update:modelValue", "update:fee"]);

// Helper: Fee calc
function calculateFee(base, fee, feeType) {
    if (feeType === "fixed") return base + Number(fee || 0);
    if (feeType === "percent")
        return Math.round(base * (1 + Number(fee) / 100));
    return base;
}

// Compose all dynamic channels for lookup/selection.
const allDynamicChannels = computed(() => {
    let ch = [];
    Object.entries(props.dynamicGroups).forEach(([tipe, group]) => {
        group.forEach(method => {
            if (Array.isArray(method.payment_channels)) {
                method.payment_channels.forEach(chan =>
                    ch.push({ ...chan, groupTipe: tipe })
                );
            }
        });
    });
    return ch;
});

// Model local selection
const localSelected = ref(props.modelValue ? { ...props.modelValue } : null);
watch(
    () => props.modelValue,
    nv => {
        if (nv) localSelected.value = { ...nv };
    }
);

// Card selection/emitters
function selectStatic(code) {
    localSelected.value = { type: "static", code, channelId: null };
    let method = props.staticMethods[code];
    let fee = method?.fee || 0;
    let feeType = method?.fee_type || "fixed";
    const amount = calculateFee(props.basePrice, fee, feeType);
    emit("update:fee", { fee, feeType, amount });
    emit("update:modelValue", { ...localSelected.value });
}
function selectDynamic(channelId) {
    const channel = allDynamicChannels.value.find(c => c.id === channelId);
    if (!channel) return;
    localSelected.value = { type: "dynamic", code: channel.kode, channelId };
    let fee = channel.fee || 0;
    let feeType = channel.fee_type || "fixed";
    const amount = calculateFee(props.basePrice, fee, feeType);
    emit("update:fee", { fee, feeType, amount });
    emit("update:modelValue", { ...localSelected.value });
}
// Utility for selection states
function isSelectedStatic(code) {
    return (
        localSelected.value &&
        localSelected.value.type === "static" &&
        localSelected.value.code === code
    );
}
function isSelectedChannel(id) {
    return (
        localSelected.value &&
        localSelected.value.type === "dynamic" &&
        localSelected.value.channelId === id
    );
}
</script>

<template>
    <CosmicCard title="Select Payment Method" :step-number="4">
        <div class="grid md:grid-cols-2 gap-4">
            <!-- STATIC PAYMENT METHODS -->
            <div
                v-for="(method, key) in staticMethods"
                :key="key"
                class="relative group"
            >
                <div
                    :class="[
                        'flex items-center justify-between px-4 py-3 rounded-2xl bg-content_background/90 cursor-pointer overflow-hidden transition-all border-2',
                        isSelectedStatic(key)
                            ? 'border-primary shadow-[0_0_16px_2px_#9b87f5] animate-pulse'
                            : 'border-transparent hover:border-secondary/70',
                    ]"
                    @click="selectStatic(key)"
                    tabindex="0"
                >
                    <!-- Cosmic "BEST PRICE" Ribbon -->
                    <div
                        v-if="key === 'saldo'"
                        class="absolute -top-3 -left-3 z-20"
                    >
                        <div
                            class="bg-primary text-xs uppercase text-white px-2 py-0.5 rounded-full shadow-[0_0_8px_2px_#9b87f577] tracking-widest flex items-center gap-1 cosmic-stars-pattern animate-pulse-slow"
                        >
                            <span>★</span> Best Price
                        </div>
                    </div>
                    <!-- Icon + name -->
                    <div class="flex items-center gap-2 min-w-0">
                        <img
                            v-if="method?.gambar"
                            :src="'/storage/' + method.gambar"
                            alt="icon"
                            class="w-8 h-8 rounded-lg object-contain cosmic-border"
                        />
                        <span class="font-bold text-white truncate">
                            {{
                                key === "saldo"
                                    ? "NaelCoin"
                                    : "QRIS (Semua Pembayaran)"
                            }}
                        </span>
                    </div>
                    <!-- Fee preview -->
                    <div class="text-right flex flex-col items-end">
                        <span class="text-primary font-bold text-lg">
                            {{
                                method && method.fee_type
                                    ? method.fee_type === "fixed"
                                        ? calculateFee(
                                              basePrice,
                                              method.fee,
                                              "fixed"
                                          )
                                        : calculateFee(
                                              basePrice,
                                              method.fee,
                                              "percent"
                                          )
                                    : basePrice
                            }}
                        </span>
                        <span
                            v-if="method?.fee"
                            class="text-xs text-secondary/80"
                        >
                            + Fee
                        </span>
                    </div>
                    <!-- Micro blackholes -->
                    <span
                        class="absolute bottom-2 right-2 w-3 h-3 bg-black rounded-full shadow-[0_0_6px_3px_rgba(155,135,245,0.65)] animate-blackhole-spin"
                    ></span>
                </div>
            </div>
            <!-- DYNAMIC PAYMENT METHODS ACCORDIONS -->
            <div
                v-for="(group, tipe) in dynamicGroups"
                :key="tipe"
                class="group relative"
            >
                <!-- Accordion header -->
                <details class="group" :open="false">
                    <summary
                        class="flex items-center justify-between px-4 py-3 rounded-2xl bg-content_background/90 cursor-pointer font-bold border-b-2 border-b-secondary/40 select-none"
                    >
                        <div class="flex items-center gap-2">
                            <span
                                class="text-secondary uppercase tracking-wide text-sm"
                                >{{ tipe }}</span
                            >
                        </div>
                        <span class="text-primary font-bold">
                            <!-- Preview min value in this group -->
                            {{
                                Math.min(
                                    ...group.map((m) =>
                                        m.payment_channels?.length
                                            ? Math.min(
                                                  ...m.payment_channels.map(
                                                      (ch) =>
                                                          ch.fee_type ===
                                                          "fixed"
                                                              ? calculateFee(
                                                                    basePrice,
                                                                    ch.fee,
                                                                    "fixed"
                                                                )
                                                              : calculateFee(
                                                                    basePrice,
                                                                    ch.fee,
                                                                    "percent"
                                                                )
                                                  )
                                              )
                                            : basePrice
                                    )
                                )
                            }}
                        </span>
                        <!-- Icon collage -->
                        <div class="flex -space-x-2 ml-4">
                            <img
                                v-for="channel in (group.length>0 && group[0].payment_channels?.slice(0, 4))"
                                :key="channel?.id"
                                v-if="channel && channel.gambar"
                                :src="'/storage/' + channel.gambar"
                                class="w-7 h-7 rounded object-contain border-2 border-dark shadow-[0_0_4px_#9b87f5a7] cosmic-border"
                                alt="Payment Channel"
                            />
                        </div>
                    </summary>
                    <!-- Channel grid -->
                    <div class="grid grid-cols-2 gap-3 py-2">
                        <div
                            v-for="channel in group[0].payment_channels"
                            :key="'ch' + channel.id"
                            class="relative"
                        >
                            <div
                                :class="[
                                    'flex flex-col items-center justify-center p-3 rounded-xl cursor-pointer transition-all border-2 bg-dark/90 hover:scale-105 overflow-hidden',
                                    isSelectedChannel(channel.id)
                                        ? 'border-primary shadow-[0_0_12px_2px_#9b87f5] animate-pulse'
                                        : 'border-secondary/30 hover:border-secondary',
                                ]"
                                @click="selectDynamic(channel.id)"
                                tabindex="0"
                                :aria-selected="isSelectedChannel(channel.id)"
                            >
                                <img
                                    v-if="channel.gambar"
                                    :src="'/storage/' + channel.gambar"
                                    alt="icon"
                                    class="w-9 h-9 object-contain cosmic-border rounded"
                                />
                                <span
                                    class="mt-2 text-sm text-white font-semibold truncate w-full text-center"
                                    >{{ channel.nama }}</span
                                >
                                <!-- Fee/right -->
                                <span class="mt-1 text-xs text-secondary/90">
                                    {{
                                        channel.fee_type === "fixed"
                                            ? calculateFee(
                                                  basePrice,
                                                  channel.fee,
                                                  "fixed"
                                              )
                                            : calculateFee(
                                                  basePrice,
                                                  channel.fee,
                                                  "percent"
                                              )
                                    }}
                                </span>
                                <span
                                    v-if="channel.is_recommended"
                                    class="absolute -top-2 right-2 bg-primary text-white text-2xs px-1.5 py-0.5 rounded-full animate-pulse border border-white cosmic-verification-badge"
                                >
                                    <span>✔️</span>
                                </span>
                                <!-- Cosmic effect: micro blackhole in bottom corner -->
                                <span
                                    class="absolute bottom-1 left-2 w-2 h-2 bg-black rounded-full shadow-[0_0_10px_1.5px_#33c3f0bb] animate-blackhole-spin"
                                ></span>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </CosmicCard>
</template>

<style scoped>
.cosmic-stars-pattern {
    background: repeating-linear-gradient(
        30deg,
        rgba(255, 255, 255, 0.13) 0px,
        rgba(255, 255, 255, 0.13) 0.5px,
        transparent 1px,
        transparent 5px
    );
}
.cosmic-border {
    box-shadow: 0 0 8px 1.5px #9b87f5bb;
}
@keyframes blackhole-spin {
    100% {
        transform: rotate(360deg);
    }
}
.animate-blackhole-spin {
    animation: blackhole-spin 20s linear infinite;
}
.cosmic-verification-badge {
    box-shadow: 0 0 8px 2px #33c3f0a0;
}
</style>


<script setup>
import { ref, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { Compass, QuantumLedger, EnergyTransfer, OrbitalNetwork } from "lucide-vue-next";
const props = defineProps({ collapsed: Boolean });
const emit = defineEmits(['update:collapsed']);

const nav = [
  { name: "Dashboard", icon: Compass, route: "dashboard" },
  { name: "Transactions", icon: QuantumLedger, route: "dashboard.transactions" },
  { name: "Mutations", icon: EnergyTransfer, route: "dashboard.mutations" },
  { name: "Affiliate", icon: OrbitalNetwork, route: "dashboard.affiliate" }
];

const page = usePage();
const current = () => page.component;
function toggle() { emit("update:collapsed", !props.collapsed); }
</script>

<template>
  <nav class="hidden lg:flex flex-col cosmic-sidebar"
       :class="{ 'w-20': collapsed, 'w-60': !collapsed }"
       aria-label="Main dashboard navigation">
    <button class="mt-4 mb-2 self-end mr-4 text-primary" @click="toggle" aria-label="Toggle sidebar">
      <span v-if="!collapsed">⫶</span>
      <span v-else>⫷</span>
    </button>
    <ul class="flex-1 space-y-4 mt-8">
      <li v-for="item in nav" :key="item.name">
        <Link :href="route(item.route)" as="button"
              :class="['flex items-center px-4 py-2 rounded-md transition-all relative group', {
                'bg-primary/20 shadow-lg pulsate': route().current(item.route)
              }]"
              :aria-current="route().current(item.route) ? 'page' : undefined">
          <item.icon class="w-6 h-6 mr-3" :aria-label="item.name + ' icon'" />
          <span v-if="!collapsed" class="font-medium tracking-wide">{{ item.name }}</span>
          <span v-else class="sr-only">{{ item.name }}</span>
          <span v-if="route().current(item.route)"
            class="absolute left-0 top-1/2 -translate-y-1/2 h-8 w-1 bg-primary pulse"></span>
        </Link>
      </li>
    </ul>
  </nav>
</template>

<style scoped>
.cosmic-sidebar { background: linear-gradient(160deg, #1F2937, #111827 80%); min-height: 100vh; }
.pulsate { animation: cosmic-pulse 1.5s infinite; }
@keyframes cosmic-pulse {
  0%,100% { box-shadow: 0 0 8px #9b87f5, 0 0 1px #33C3F0; }
  50% { box-shadow: 0 0 24px #9b87f5, 0 0 8px #33C3F0; }
}
.pulse {
  animation: cosmic-bar-pulse 0.6s cubic-bezier(.77,0,.175,1) infinite alternate;
}
@keyframes cosmic-bar-pulse {
  0% { background: #9b87f5; }
  100% { background: #33C3F0; }
}
</style>

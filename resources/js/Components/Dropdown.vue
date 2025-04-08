<script setup>
import { ref, onMounted, onUnmounted, nextTick } from "vue";

const props = defineProps({
    align: {
        type: String,
        default: "right",
    },
    width: {
        type: String,
        default: "48",
    },
});

const open = ref(false);
const dropdownRef = ref(null);
const triggerRef = ref(null);
const dropdownStyles = ref({});

const emit = defineEmits(["toggleDropdown"]);

const toggleDropdown = async () => {
    open.value = !open.value;
    emit("toggleDropdown", open.value);

    if (open.value) {
        await nextTick();
        positionDropdown();
    }
};

const positionDropdown = () => {
    if (!dropdownRef.value || !triggerRef.value) return;

    const triggerRect = triggerRef.value.getBoundingClientRect();
    const dropdownRect = dropdownRef.value.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    let top = triggerRect.bottom + 4; // Default posisi di bawah tombol
    let left = triggerRect.left;

    // Jika dropdown keluar dari layar kanan, geser ke kiri
    if (triggerRect.right + dropdownRect.width > viewportWidth) {
        left = triggerRect.right - dropdownRect.width;
    }

    // Jika dropdown keluar dari layar bawah, pindah ke atas
    if (triggerRect.bottom + dropdownRect.height > viewportHeight) {
        top = triggerRect.top - dropdownRect.height - 4;
    }

    dropdownStyles.value = {
        position: "fixed",
        top: `${top}px`,
        left: `${left}px`,
        zIndex: 9999,
    };
};

const closeDropdown = () => {
    open.value = false;
};

onMounted(() => {
    window.addEventListener("resize", positionDropdown);
    window.addEventListener("scroll", positionDropdown, true);
});

onUnmounted(() => {
    window.removeEventListener("resize", positionDropdown);
    window.removeEventListener("scroll", positionDropdown, true);
});
</script>

<template>
    <div class="relative">
        <button ref="triggerRef" @click="toggleDropdown">
            <slot name="trigger" />
        </button>

        <div
            v-if="open"
            class="fixed inset-0 z-40"
            @click="closeDropdown"
        ></div>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
        >
            <div
                v-show="open"
                ref="dropdownRef"
                class="w-48 bg-white border rounded-md shadow-lg border-primary/60 dark:bg-gray-700"
                :style="dropdownStyles"
            >
                <slot name="content" />
            </div>
        </Transition>
    </div>
</template>

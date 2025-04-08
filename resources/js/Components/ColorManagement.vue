
<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import axios from 'axios';
import { debounce } from 'lodash';

const props = defineProps({
    initialColors: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['colorUpdated', 'applyColors']);

const toast = useToast();
const colors = reactive({ ...props.initialColors });
const isUpdating = ref(false);
const hasChanges = ref(false);
const colorPreview = ref(null);

// Prepare debounce function for API calls
const updateColor = debounce(async (key, value) => {
    if (!value) return;
    
    isUpdating.value = true;
    try {
        const response = await axios.patch(route('admin.settings.colors'), {
            key: key,
            value: value
        });
        
        if (response.data.success) {
            // Update CSS variable immediately for real-time preview
            document.documentElement.style.setProperty(
                mapColorKeyToCssVar(key), 
                value
            );
            
            toast.success('Color updated successfully');
            emit('colorUpdated', { key, value });
        }
    } catch (error) {
        console.error('Failed to update color:', error);
        toast.error(error.response?.data?.message || 'Failed to update color');
        
        // Revert to the previous color
        colors[key] = props.initialColors[key];
    } finally {
        isUpdating.value = false;
    }
}, 500);

// Watch for color changes
watch(colors, (newColors, oldColors) => {
    const changedKey = Object.keys(newColors).find(key => newColors[key] !== oldColors[key]);
    
    if (changedKey) {
        hasChanges.value = true;
        updateColor(changedKey, newColors[changedKey]);
    }
}, { deep: true });

// Map color keys to CSS variable names
function mapColorKeyToCssVar(key) {
    const mappings = {
        'primary_color': '--color-primary',
        'primary_hover': '--color-primary-hover',
        'secondary_color': '--color-secondary',
        'secondary_hover': '--color-secondary-hover',
        'text_primary': '--color-primary-text',
        'text_secondary': '--color-secondary-text',
        'header_bg': '--color-header-bg',
        'footer_bg': '--color-footer-bg',
        'content_bg': '--color-content-bg'
    };
    
    return mappings[key] || `--${key.replace('_', '-')}`;
}

// Color presets for quick selection
const colorPresets = [
    {
        name: 'Neo Futuristic',
        primary_color: '#6366F1',
        primary_hover: '#818CF8',
        secondary_color: '#8B5CF6',
        secondary_hover: '#A78BFA',
        text_primary: '#F9FAFB',
        text_secondary: '#E5E7EB',
        content_bg: '#1F2937',
        header_bg: '#111827',
        footer_bg: '#111827'
    },
    {
        name: 'Cyber Blue',
        primary_color: '#0EA5E9',
        primary_hover: '#38BDF8',
        secondary_color: '#6366F1',
        secondary_hover: '#818CF8',
        text_primary: '#F9FAFB',
        text_secondary: '#E5E7EB',
        content_bg: '#0F172A',
        header_bg: '#020617',
        footer_bg: '#020617'
    },
    {
        name: 'Neon Dreams',
        primary_color: '#EC4899',
        primary_hover: '#F472B6',
        secondary_color: '#8B5CF6',
        secondary_hover: '#A78BFA',
        text_primary: '#F9FAFB',
        text_secondary: '#E5E7EB',
        content_bg: '#18181B',
        header_bg: '#09090B',
        footer_bg: '#09090B'
    }
];

// Apply a preset
function applyPreset(preset) {
    Object.keys(preset).forEach(key => {
        if (key !== 'name' && colors[key] !== undefined) {
            colors[key] = preset[key];
        }
    });
    
    toast.info('Color preset applied - click Apply Colors to save');
    hasChanges.value = true;
}

// Force refresh to apply all colors
function applyAllColors() {
    emit('applyColors', colors);
}

// Initialize color preview component
onMounted(() => {
    // Create a floating preview element
    colorPreview.value = document.createElement('div');
    colorPreview.value.className = 'fixed top-2 right-2 p-2 rounded-md shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 z-50 hidden transition-all duration-300 transform scale-95 opacity-0';
    document.body.appendChild(colorPreview.value);
    
    // Add event listeners for color input hover
    document.querySelectorAll('.color-input').forEach(input => {
        input.addEventListener('mouseenter', (e) => {
            const color = e.target.value;
            const colorName = e.target.getAttribute('data-color-name');
            
            colorPreview.value.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded" style="background-color: ${color}"></div>
                    <div>${colorName}: ${color}</div>
                </div>
            `;
            
            colorPreview.value.classList.remove('hidden', 'scale-95', 'opacity-0');
            colorPreview.value.classList.add('scale-100', 'opacity-100');
        });
        
        input.addEventListener('mouseleave', () => {
            colorPreview.value.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                colorPreview.value.classList.add('hidden');
            }, 300);
        });
    });
});
</script>

<template>
    <div class="space-y-6">
        <!-- Status indicator -->
        <div v-if="isUpdating" class="flex items-center justify-center p-2 mb-4 bg-blue-100 text-blue-800 rounded-md animate-pulse">
            <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Updating colors...
        </div>

        <!-- Color Management UI -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Primary Colors -->
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Primary Colors</h3>
                
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Primary Color
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.primary_color" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Primary Color"
                            />
                            <input 
                                v-model="colors.primary_color" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded" :style="{ backgroundColor: colors.primary_color }"></div>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Primary Hover
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.primary_hover" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Primary Hover"
                            />
                            <input 
                                v-model="colors.primary_hover" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded" :style="{ backgroundColor: colors.primary_hover }"></div>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Primary Text
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.text_primary" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Primary Text"
                            />
                            <input 
                                v-model="colors.text_primary" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded flex items-center justify-center" 
                             :style="{ backgroundColor: colors.primary_color, color: colors.text_primary }">
                            Sample Text
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Secondary Colors -->
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Secondary Colors</h3>
                
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Secondary Color
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.secondary_color" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Secondary Color"
                            />
                            <input 
                                v-model="colors.secondary_color" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded" :style="{ backgroundColor: colors.secondary_color }"></div>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Secondary Hover
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.secondary_hover" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Secondary Hover"
                            />
                            <input 
                                v-model="colors.secondary_hover" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded" :style="{ backgroundColor: colors.secondary_hover }"></div>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Secondary Text
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.text_secondary" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Secondary Text"
                            />
                            <input 
                                v-model="colors.text_secondary" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded flex items-center justify-center" 
                             :style="{ backgroundColor: colors.secondary_color, color: colors.text_secondary }">
                            Sample Text
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Background Colors -->
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Background Colors</h3>
                
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Content Background
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.content_bg" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Content Background"
                            />
                            <input 
                                v-model="colors.content_bg" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded" :style="{ backgroundColor: colors.content_bg }"></div>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Header Background
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.header_bg" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Header Background"
                            />
                            <input 
                                v-model="colors.header_bg" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded" :style="{ backgroundColor: colors.header_bg }"></div>
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Footer Background
                        </label>
                        <div class="flex items-center gap-2">
                            <input 
                                v-model="colors.footer_bg" 
                                type="color" 
                                class="h-10 w-20 cursor-pointer rounded border border-gray-300 dark:border-gray-600 color-input"
                                data-color-name="Footer Background"
                            />
                            <input 
                                v-model="colors.footer_bg" 
                                type="text" 
                                class="form-input flex-1"
                                pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                placeholder="#000000"
                            />
                        </div>
                        <div class="mt-2 h-10 rounded" :style="{ backgroundColor: colors.footer_bg }"></div>
                    </div>
                </div>
            </div>
            
            <!-- Color Presets -->
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Color Presets</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-3">
                        <button 
                            v-for="preset in colorPresets" 
                            :key="preset.name"
                            @click="applyPreset(preset)"
                            class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <div class="flex space-x-1 mr-3">
                                <div class="w-6 h-6 rounded-full" :style="{ backgroundColor: preset.primary_color }"></div>
                                <div class="w-6 h-6 rounded-full" :style="{ backgroundColor: preset.secondary_color }"></div>
                                <div class="w-6 h-6 rounded-full" :style="{ backgroundColor: preset.content_bg }"></div>
                            </div>
                            <span>{{ preset.name }}</span>
                        </button>
                    </div>
                    
                    <!-- Preview and Apply Section -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-md font-medium mb-3 text-gray-800 dark:text-gray-200">Live Preview</h4>
                        
                        <div class="p-4 rounded-lg" :style="{ backgroundColor: colors.content_bg }">
                            <div class="p-2 mb-3 rounded" :style="{ backgroundColor: colors.header_bg }">
                                <div class="text-sm" :style="{ color: colors.text_primary }">Header Area</div>
                            </div>
                            
                            <div class="flex space-x-2 mb-3">
                                <button class="px-3 py-1 rounded" :style="{ backgroundColor: colors.primary_color, color: colors.text_primary }">
                                    Primary Button
                                </button>
                                <button class="px-3 py-1 rounded" :style="{ backgroundColor: colors.secondary_color, color: colors.text_secondary }">
                                    Secondary
                                </button>
                            </div>
                            
                            <div class="p-2 rounded" :style="{ backgroundColor: colors.footer_bg }">
                                <div class="text-sm" :style="{ color: colors.text_secondary }">Footer Area</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Apply Colors Button -->
        <div class="flex justify-end mt-6">
            <button 
                @click="applyAllColors"
                :disabled="!hasChanges || isUpdating"
                :class="[
                    'px-4 py-2 rounded-md transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2',
                    hasChanges && !isUpdating 
                        ? 'bg-primary text-primary-text hover:bg-primary-hover' 
                        : 'bg-gray-300 text-gray-600 cursor-not-allowed'
                ]"
            >
                Apply Colors
            </button>
        </div>
    </div>
</template>

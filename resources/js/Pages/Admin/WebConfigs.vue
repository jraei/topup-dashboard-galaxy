<script setup>
import { ref, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import ColorManagement from '@/Components/ColorManagement.vue';

const props = defineProps({
    generalSettings: Object,
    appearanceSettings: Object,
    providers: Array,
    status: Object,
});

// Forms
const generalForm = useForm({
    judul_web: props.generalSettings.judul_web,
    meta_title: props.generalSettings.meta_title,
    meta_description: props.generalSettings.meta_description,
    meta_keywords: props.generalSettings.meta_keywords,
    support_instagram: props.generalSettings.support_instagram,
    support_whatsapp: props.generalSettings.support_whatsapp,
    support_email: props.generalSettings.support_email,
    support_youtube: props.generalSettings.support_youtube,
    support_facebook: props.generalSettings.support_facebook,
});

const appearanceForm = useForm({
    primary_color: props.appearanceSettings.primary_color,
    primary_hover: props.appearanceSettings.primary_hover,
    secondary_color: props.appearanceSettings.secondary_color,
    secondary_hover: props.appearanceSettings.secondary_hover,
    content_bg: props.appearanceSettings.content_bg,
    footer_bg: props.appearanceSettings.footer_bg,
    header_bg: props.appearanceSettings.header_bg,
    text_primary: props.appearanceSettings.text_primary,
    text_secondary: props.appearanceSettings.text_secondary,
    logo_header: null,
    logo_footer: null,
    logo_favicon: null,
});

// API connections form
const providersForm = reactive({});
props.providers.forEach(provider => {
    providersForm[provider.id] = {
        api_username: provider.api_username,
        api_key: provider.api_key,
        api_private_key: provider.api_private_key,
        status: provider.status,
    };
});

const apiForm = useForm({
    providers: providersForm,
});

// Active tab state
const activeTab = ref('general');

// Form submission handlers
const submitGeneralForm = () => {
    generalForm.patch(route('admin.settings.general'));
};

const submitAppearanceForm = () => {
    appearanceForm.post(route('admin.settings.appearance'), {
        forceFormData: true,
    });
};

const submitApiForm = () => {
    apiForm.patch(route('admin.settings.api'));
};

// Logo preview and deletion
const logoHeaderPreview = ref(props.appearanceSettings.logo_header);
const logoFooterPreview = ref(props.appearanceSettings.logo_footer);
const logoFaviconPreview = ref(props.appearanceSettings.logo_favicon);

const previewLogo = (event, type) => {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        if (type === 'header') {
            logoHeaderPreview.value = e.target.result;
        } else if (type === 'footer') {
            logoFooterPreview.value = e.target.result;
        } else if (type === 'favicon') {
            logoFaviconPreview.value = e.target.result;
        }
    };
    reader.readAsDataURL(file);
};

const deleteLogo = (field) => {
    if (confirm('Are you sure you want to delete this logo?')) {
        window.axios.delete(route('admin.settings.logo.delete', field))
            .then(() => {
                if (field === 'logo_header') {
                    logoHeaderPreview.value = null;
                } else if (field === 'logo_footer') {
                    logoFooterPreview.value = null;
                } else if (field === 'logo_favicon') {
                    logoFaviconPreview.value = null;
                }
                
                // Show success message
                Swal.fire({
                    title: 'Logo Deleted',
                    text: 'The logo has been deleted successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            })
            .catch(error => {
                console.error('Error deleting logo:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'There was an error deleting the logo.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }
};

// Handle color updates from the ColorManagement component
const handleColorUpdated = ({ key, value }) => {
    // Update the form values to keep them in sync
    if (appearanceForm[key] !== undefined) {
        appearanceForm[key] = value;
    }
};

// Handle apply colors action (manual refresh)
const handleApplyColors = (colors) => {
    // Update appearance form with the latest colors
    Object.keys(colors).forEach(key => {
        if (appearanceForm[key] !== undefined) {
            appearanceForm[key] = colors[key];
        }
    });
    
    // Submit the form to save all colors at once
    submitAppearanceForm();
    
    // Show a message
    Swal.fire({
        title: 'Colors Applied',
        text: 'All colors have been applied and saved.',
        icon: 'success',
        confirmButtonText: 'OK'
    });
};
</script>

<template>
    <Head title="Web Configurations" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Web Configurations
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Tabs -->
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                            <ul class="flex flex-wrap -mb-px">
                                <li class="mr-2">
                                    <button 
                                        :class="['inline-block px-4 py-2 rounded-t-lg border-b-2', 
                                        activeTab === 'general' 
                                            ? 'border-primary text-primary font-medium' 
                                            : 'border-transparent hover:border-gray-300']"
                                        @click="activeTab = 'general'"
                                    >
                                        General
                                    </button>
                                </li>
                                <li class="mr-2">
                                    <button 
                                        :class="['inline-block px-4 py-2 rounded-t-lg border-b-2', 
                                        activeTab === 'appearance' 
                                            ? 'border-primary text-primary font-medium' 
                                            : 'border-transparent hover:border-gray-300']"
                                        @click="activeTab = 'appearance'"
                                    >
                                        Appearance
                                    </button>
                                </li>
                                <li class="mr-2">
                                    <button 
                                        :class="['inline-block px-4 py-2 rounded-t-lg border-b-2', 
                                        activeTab === 'api' 
                                            ? 'border-primary text-primary font-medium' 
                                            : 'border-transparent hover:border-gray-300']"
                                        @click="activeTab = 'api'"
                                    >
                                        API Connections
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- General Tab -->
                        <div v-if="activeTab === 'general'" class="space-y-6">
                            <form @submit.prevent="submitGeneralForm">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Website Title -->
                                    <div>
                                        <InputLabel for="judul_web" value="Website Title" />
                                        <TextInput
                                            id="judul_web"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="generalForm.judul_web"
                                            required
                                            autofocus
                                        />
                                        <InputError class="mt-2" :message="generalForm.errors.judul_web" />
                                    </div>

                                    <!-- Meta Title -->
                                    <div>
                                        <InputLabel for="meta_title" value="Meta Title" />
                                        <TextInput
                                            id="meta_title"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="generalForm.meta_title"
                                            required
                                        />
                                        <InputError class="mt-2" :message="generalForm.errors.meta_title" />
                                    </div>

                                    <!-- Meta Description -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="meta_description" value="Meta Description" />
                                        <textarea
                                            id="meta_description"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="generalForm.meta_description"
                                            required
                                            rows="3"
                                        ></textarea>
                                        <InputError class="mt-2" :message="generalForm.errors.meta_description" />
                                    </div>

                                    <!-- Meta Keywords -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="meta_keywords" value="Meta Keywords" />
                                        <TextInput
                                            id="meta_keywords"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="generalForm.meta_keywords"
                                            required
                                        />
                                        <InputError class="mt-2" :message="generalForm.errors.meta_keywords" />
                                    </div>

                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 md:col-span-2 mt-4">
                                        Support Links
                                    </h3>

                                    <!-- Instagram -->
                                    <div>
                                        <InputLabel for="support_instagram" value="Instagram URL" />
                                        <TextInput
                                            id="support_instagram"
                                            type="url"
                                            class="mt-1 block w-full"
                                            v-model="generalForm.support_instagram"
                                            required
                                        />
                                        <InputError class="mt-2" :message="generalForm.errors.support_instagram" />
                                    </div>

                                    <!-- WhatsApp -->
                                    <div>
                                        <InputLabel for="support_whatsapp" value="WhatsApp Number" />
                                        <TextInput
                                            id="support_whatsapp"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="generalForm.support_whatsapp"
                                            required
                                        />
                                        <InputError class="mt-2" :message="generalForm.errors.support_whatsapp" />
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <InputLabel for="support_email" value="Support Email" />
                                        <TextInput
                                            id="support_email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            v-model="generalForm.support_email"
                                            required
                                        />
                                        <InputError class="mt-2" :message="generalForm.errors.support_email" />
                                    </div>

                                    <!-- YouTube -->
                                    <div>
                                        <InputLabel for="support_youtube" value="YouTube URL" />
                                        <TextInput
                                            id="support_youtube"
                                            type="url"
                                            class="mt-1 block w-full"
                                            v-model="generalForm.support_youtube"
                                            required
                                        />
                                        <InputError class="mt-2" :message="generalForm.errors.support_youtube" />
                                    </div>

                                    <!-- Facebook -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="support_facebook" value="Facebook URL" />
                                        <TextInput
                                            id="support_facebook"
                                            type="url"
                                            class="mt-1 block w-full"
                                            v-model="generalForm.support_facebook"
                                            required
                                        />
                                        <InputError class="mt-2" :message="generalForm.errors.support_facebook" />
                                    </div>
                                </div>

                                <div class="flex justify-end mt-6">
                                    <PrimaryButton :class="{ 'opacity-25': generalForm.processing }" :disabled="generalForm.processing">
                                        Save General Settings
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <!-- Appearance Tab -->
                        <div v-if="activeTab === 'appearance'" class="space-y-6">
                            <form @submit.prevent="submitAppearanceForm">
                                <!-- Color Management Component -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                        Color Management
                                    </h3>
                                    <ColorManagement 
                                        :initialColors="appearanceSettings"
                                        @colorUpdated="handleColorUpdated"
                                        @applyColors="handleApplyColors"
                                    />
                                </div>

                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                                    Logo Management
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Header Logo -->
                                    <div class="space-y-4">
                                        <InputLabel for="logo_header" value="Header Logo" />
                                        <div class="flex items-center space-x-4">
                                            <div class="w-40 h-20 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                                <img v-if="logoHeaderPreview" :src="logoHeaderPreview" class="max-w-full max-h-full object-contain" />
                                                <span v-else class="text-gray-400 dark:text-gray-500">No Logo</span>
                                            </div>
                                            <div class="flex flex-col space-y-2">
                                                <input
                                                    id="logo_header"
                                                    type="file"
                                                    class="hidden"
                                                    ref="logoHeaderInput"
                                                    @change="(e) => {
                                                        appearanceForm.logo_header = e.target.files[0];
                                                        previewLogo(e, 'header');
                                                    }"
                                                    accept="image/*"
                                                />
                                                <button
                                                    type="button"
                                                    @click="$refs.logoHeaderInput.click()"
                                                    class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600"
                                                >
                                                    Choose File
                                                </button>
                                                <button
                                                    v-if="logoHeaderPreview"
                                                    type="button"
                                                    @click="deleteLogo('logo_header')"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                        <InputError class="mt-2" :message="appearanceForm.errors.logo_header" />
                                    </div>

                                    <!-- Footer Logo -->
                                    <div class="space-y-4">
                                        <InputLabel for="logo_footer" value="Footer Logo" />
                                        <div class="flex items-center space-x-4">
                                            <div class="w-40 h-20 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                                <img v-if="logoFooterPreview" :src="logoFooterPreview" class="max-w-full max-h-full object-contain" />
                                                <span v-else class="text-gray-400 dark:text-gray-500">No Logo</span>
                                            </div>
                                            <div class="flex flex-col space-y-2">
                                                <input
                                                    id="logo_footer"
                                                    type="file"
                                                    class="hidden"
                                                    ref="logoFooterInput"
                                                    @change="(e) => {
                                                        appearanceForm.logo_footer = e.target.files[0];
                                                        previewLogo(e, 'footer');
                                                    }"
                                                    accept="image/*"
                                                />
                                                <button
                                                    type="button"
                                                    @click="$refs.logoFooterInput.click()"
                                                    class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600"
                                                >
                                                    Choose File
                                                </button>
                                                <button
                                                    v-if="logoFooterPreview"
                                                    type="button"
                                                    @click="deleteLogo('logo_footer')"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                        <InputError class="mt-2" :message="appearanceForm.errors.logo_footer" />
                                    </div>

                                    <!-- Favicon -->
                                    <div class="space-y-4">
                                        <InputLabel for="logo_favicon" value="Favicon (32x32)" />
                                        <div class="flex items-center space-x-4">
                                            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                                <img v-if="logoFaviconPreview" :src="logoFaviconPreview" class="max-w-full max-h-full object-contain" />
                                                <span v-else class="text-gray-400 dark:text-gray-500">No Favicon</span>
                                            </div>
                                            <div class="flex flex-col space-y-2">
                                                <input
                                                    id="logo_favicon"
                                                    type="file"
                                                    class="hidden"
                                                    ref="logoFaviconInput"
                                                    @change="(e) => {
                                                        appearanceForm.logo_favicon = e.target.files[0];
                                                        previewLogo(e, 'favicon');
                                                    }"
                                                    accept="image/*"
                                                />
                                                <button
                                                    type="button"
                                                    @click="$refs.logoFaviconInput.click()"
                                                    class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600"
                                                >
                                                    Choose File
                                                </button>
                                                <button
                                                    v-if="logoFaviconPreview"
                                                    type="button"
                                                    @click="deleteLogo('logo_favicon')"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                        <InputError class="mt-2" :message="appearanceForm.errors.logo_favicon" />
                                    </div>
                                </div>

                                <div class="flex justify-end mt-6">
                                    <PrimaryButton :class="{ 'opacity-25': appearanceForm.processing }" :disabled="appearanceForm.processing">
                                        Save Appearance Settings
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <!-- API Connections Tab -->
                        <div v-if="activeTab === 'api'" class="space-y-6">
                            <form @submit.prevent="submitApiForm">
                                <div class="space-y-6">
                                    <div v-for="provider in providers" :key="provider.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                            {{ provider.provider_name }}
                                        </h3>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel :for="`api_username_${provider.id}`" value="API Username" />
                                                <TextInput
                                                    :id="`api_username_${provider.id}`"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    v-model="apiForm.providers[provider.id].api_username"
                                                />
                                            </div>
                                            
                                            <div>
                                                <InputLabel :for="`api_key_${provider.id}`" value="API Key" />
                                                <TextInput
                                                    :id="`api_key_${provider.id}`"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    v-model="apiForm.providers[provider.id].api_key"
                                                />
                                            </div>
                                            
                                            <div>
                                                <InputLabel :for="`api_private_key_${provider.id}`" value="API Private Key" />
                                                <TextInput
                                                    :id="`api_private_key_${provider.id}`"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    v-model="apiForm.providers[provider.id].api_private_key"
                                                />
                                            </div>
                                            
                                            <div>
                                                <InputLabel :for="`status_${provider.id}`" value="Status" />
                                                <select
                                                    :id="`status_${provider.id}`"
                                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                    v-model="apiForm.providers[provider.id].status"
                                                >
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end mt-6">
                                    <PrimaryButton :class="{ 'opacity-25': apiForm.processing }" :disabled="apiForm.processing">
                                        Save API Settings
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

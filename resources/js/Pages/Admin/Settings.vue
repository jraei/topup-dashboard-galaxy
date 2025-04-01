
<script setup>
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const activeTab = ref('general');

const tabs = [
  { id: 'general', name: 'General Settings' },
  { id: 'appearance', name: 'Appearance' },
  { id: 'api', name: 'API Connections' },
  { id: 'security', name: 'Security' },
  { id: 'payment', name: 'Payment Methods' },
];

const setActiveTab = (tabId) => {
  activeTab.value = tabId;
};
</script>

<template>
  <Head title="Settings | Admin" />

  <AdminLayout title="System Settings">
    <div class="bg-gray-800 rounded-lg overflow-hidden">
      <div class="border-b border-gray-700">
        <div class="flex overflow-x-auto">
          <div class="flex-shrink-0">
            <nav class="flex" aria-label="Tabs">
              <button v-for="tab in tabs" :key="tab.id"
                @click="setActiveTab(tab.id)"
                class="whitespace-nowrap py-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200"
                :class="[
                  activeTab === tab.id
                    ? 'border-indigo-500 text-indigo-400'
                    : 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500'
                ]"
                :aria-current="activeTab === tab.id ? 'page' : undefined"
              >
                {{ tab.name }}
              </button>
            </nav>
          </div>
        </div>
      </div>

      <!-- Tab Content -->
      <div class="p-6">
        <!-- General Settings Tab -->
        <div v-if="activeTab === 'general'" class="space-y-6">
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Website Information</h3>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div>
                <InputLabel for="site_name" value="Site Name" />
                <TextInput
                  id="site_name"
                  type="text"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="Game Top-Up Website"
                />
                <InputError class="mt-2" />
              </div>
              
              <div>
                <InputLabel for="site_url" value="Site URL" />
                <TextInput
                  id="site_url"
                  type="text"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="https://yourdomain.com"
                />
                <InputError class="mt-2" />
              </div>
              
              <div>
                <InputLabel for="admin_email" value="Admin Email" />
                <TextInput
                  id="admin_email"
                  type="email"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="admin@yourdomain.com"
                />
                <InputError class="mt-2" />
              </div>
              
              <div>
                <InputLabel for="support_phone" value="Support Phone" />
                <TextInput
                  id="support_phone"
                  type="text"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="+1234567890"
                />
                <InputError class="mt-2" />
              </div>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-white mb-4">SEO Settings</h3>
            <div class="space-y-4">
              <div>
                <InputLabel for="meta_title" value="Meta Title" />
                <TextInput
                  id="meta_title"
                  type="text"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="Game Top-Up | Fast & Secure"
                />
                <InputError class="mt-2" />
              </div>
              
              <div>
                <InputLabel for="meta_description" value="Meta Description" />
                <textarea
                  id="meta_description"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm"
                  rows="3"
                  placeholder="The best place to buy game credits and top-ups at affordable prices."
                ></textarea>
                <InputError class="mt-2" />
              </div>
              
              <div>
                <InputLabel for="meta_keywords" value="Meta Keywords" />
                <TextInput
                  id="meta_keywords"
                  type="text"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="game top-up, mobile legends, free fire, pubg mobile"
                />
                <InputError class="mt-2" />
              </div>
            </div>
          </div>
          
          <div class="pt-5">
            <div class="flex justify-end">
              <PrimaryButton>
                Save Settings
              </PrimaryButton>
            </div>
          </div>
        </div>

        <!-- Appearance Tab -->
        <div v-if="activeTab === 'appearance'" class="space-y-6">
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Theme Settings</h3>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div>
                <InputLabel for="primary_color" value="Primary Color" />
                <div class="mt-1 flex items-center">
                  <input
                    id="primary_color"
                    type="color"
                    class="h-10 w-10 rounded border-gray-600 bg-gray-700"
                    value="#6366F1"
                  />
                  <TextInput
                    type="text"
                    class="ml-2 block w-full bg-gray-700 text-white border-gray-600"
                    placeholder="#6366F1"
                    value="#6366F1"
                  />
                </div>
              </div>
              
              <div>
                <InputLabel for="secondary_color" value="Secondary Color" />
                <div class="mt-1 flex items-center">
                  <input
                    id="secondary_color"
                    type="color"
                    class="h-10 w-10 rounded border-gray-600 bg-gray-700"
                    value="#8B5CF6"
                  />
                  <TextInput
                    type="text"
                    class="ml-2 block w-full bg-gray-700 text-white border-gray-600"
                    placeholder="#8B5CF6"
                    value="#8B5CF6"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Logo Settings</h3>
            <div class="space-y-4">
              <div>
                <InputLabel for="logo_upload" value="Site Logo" />
                <div class="mt-1 flex items-center">
                  <span class="inline-block h-12 w-12 overflow-hidden bg-gray-700 rounded-md">
                    <svg class="h-full w-full text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                  </span>
                  <button
                    type="button"
                    class="ml-4 rounded-md border border-gray-600 bg-gray-700 py-2 px-3 text-sm font-medium text-gray-300 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800"
                  >
                    Change
                  </button>
                </div>
              </div>
              
              <div>
                <InputLabel for="favicon_upload" value="Favicon" />
                <div class="mt-1 flex items-center">
                  <span class="inline-block h-12 w-12 overflow-hidden bg-gray-700 rounded-md">
                    <svg class="h-full w-full text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                  </span>
                  <button
                    type="button"
                    class="ml-4 rounded-md border border-gray-600 bg-gray-700 py-2 px-3 text-sm font-medium text-gray-300 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800"
                  >
                    Change
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="pt-5">
            <div class="flex justify-end">
              <PrimaryButton>
                Save Appearance
              </PrimaryButton>
            </div>
          </div>
        </div>

        <!-- API Connections Tab -->
        <div v-if="activeTab === 'api'" class="space-y-6">
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Digiflazz API</h3>
            <div class="space-y-4">
              <div>
                <InputLabel for="digiflazz_username" value="Username" />
                <TextInput
                  id="digiflazz_username"
                  type="text"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="Enter Digiflazz username"
                />
              </div>
              
              <div>
                <InputLabel for="digiflazz_key" value="API Key" />
                <TextInput
                  id="digiflazz_key"
                  type="password"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="Enter Digiflazz API key"
                />
              </div>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Tripay API</h3>
            <div class="space-y-4">
              <div>
                <InputLabel for="tripay_merchant_code" value="Merchant Code" />
                <TextInput
                  id="tripay_merchant_code"
                  type="text"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="Enter Tripay merchant code"
                />
              </div>
              
              <div>
                <InputLabel for="tripay_api_key" value="API Key" />
                <TextInput
                  id="tripay_api_key"
                  type="password"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="Enter Tripay API key"
                />
              </div>
              
              <div>
                <InputLabel for="tripay_private_key" value="Private Key" />
                <TextInput
                  id="tripay_private_key"
                  type="password"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="Enter Tripay private key"
                />
              </div>
            </div>
          </div>
          
          <div class="pt-5">
            <div class="flex justify-end">
              <PrimaryButton>
                Save API Settings
              </PrimaryButton>
            </div>
          </div>
        </div>

        <!-- Security Tab -->
        <div v-if="activeTab === 'security'" class="space-y-6">
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Security Settings</h3>
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="2fa_enabled"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 bg-gray-700 border-gray-600 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="2fa_enabled" class="font-medium text-white">Enable 2FA for Admin</label>
                  <p class="text-gray-400">Require two-factor authentication for all admin accounts</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="ip_restriction"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 bg-gray-700 border-gray-600 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="ip_restriction" class="font-medium text-white">IP Restriction</label>
                  <p class="text-gray-400">Only allow admin login from specific IP addresses</p>
                </div>
              </div>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Password Policy</h3>
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="enforce_complex_password"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 bg-gray-700 border-gray-600 rounded"
                    checked
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="enforce_complex_password" class="font-medium text-white">Enforce Complex Passwords</label>
                  <p class="text-gray-400">Require numbers, special characters, and mixed case</p>
                </div>
              </div>
              
              <div>
                <InputLabel for="password_expiry_days" value="Password Expiry (days)" />
                <TextInput
                  id="password_expiry_days"
                  type="number"
                  class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                  placeholder="90"
                  value="90"
                />
              </div>
            </div>
          </div>
          
          <div class="pt-5">
            <div class="flex justify-end">
              <PrimaryButton>
                Save Security Settings
              </PrimaryButton>
            </div>
          </div>
        </div>

        <!-- Payment Methods Tab -->
        <div v-if="activeTab === 'payment'" class="space-y-6">
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Payment Gateway Settings</h3>
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="tripay_enabled"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 bg-gray-700 border-gray-600 rounded"
                    checked
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="tripay_enabled" class="font-medium text-white">Enable Tripay Gateway</label>
                  <p class="text-gray-400">Allow payments through Tripay payment gateway</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="midtrans_enabled"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 bg-gray-700 border-gray-600 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="midtrans_enabled" class="font-medium text-white">Enable Midtrans Gateway</label>
                  <p class="text-gray-400">Allow payments through Midtrans payment gateway</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="manual_transfer_enabled"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 bg-gray-700 border-gray-600 rounded"
                    checked
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="manual_transfer_enabled" class="font-medium text-white">Enable Manual Bank Transfer</label>
                  <p class="text-gray-400">Allow payments through manual bank transfer</p>
                </div>
              </div>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium text-white mb-4">Bank Accounts</h3>
            <div class="border border-gray-700 rounded-md p-4 mb-4">
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                  <InputLabel for="bank_name" value="Bank Name" />
                  <TextInput
                    id="bank_name"
                    type="text"
                    class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                    placeholder="Bank BCA"
                  />
                </div>
                
                <div>
                  <InputLabel for="account_number" value="Account Number" />
                  <TextInput
                    id="account_number"
                    type="text"
                    class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                    placeholder="1234567890"
                  />
                </div>
                
                <div>
                  <InputLabel for="account_name" value="Account Name" />
                  <TextInput
                    id="account_name"
                    type="text"
                    class="mt-1 block w-full bg-gray-700 text-white border-gray-600"
                    placeholder="John Doe"
                  />
                </div>
              </div>
              <div class="mt-4 flex justify-end">
                <button
                  type="button"
                  class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800"
                >
                  Add Bank Account
                </button>
              </div>
            </div>
            
            <div class="bg-gray-700 overflow-hidden shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <p class="text-gray-300">No bank accounts added yet</p>
              </div>
            </div>
          </div>
          
          <div class="pt-5">
            <div class="flex justify-end">
              <PrimaryButton>
                Save Payment Settings
              </PrimaryButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

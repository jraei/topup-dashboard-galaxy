
<template>
    <GuestLayout>
        <div class="min-h-screen bg-content_background">
            <!-- Cosmic Background -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-2 h-2 bg-primary rounded-full animate-ping-small opacity-60"></div>
                <div class="absolute top-40 right-20 w-1 h-1 bg-secondary rounded-full animate-pulse-slow opacity-40"></div>
                <div class="absolute bottom-40 left-1/4 w-1.5 h-1.5 bg-primary rounded-full animate-bounce-slow opacity-50"></div>
            </div>

            <div class="relative z-10 container mx-auto px-4 py-12">
                <!-- Header Section -->
                <div class="text-center mb-16">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">
                        <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            API Documentation
                        </span>
                    </h1>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto mb-8">
                        Integrate our powerful top-up services into your application with our RESTful API. 
                        All endpoints require authentication and return JSON responses.
                    </p>
                    
                    <!-- Authentication Info -->
                    <div class="bg-dark-card border border-primary/20 rounded-lg p-6 max-w-2xl mx-auto">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                            <h3 class="text-lg font-semibold text-white">Authentication Required</h3>
                        </div>
                        <p class="text-gray-300 text-sm mb-4">
                            Include your API key in the request header for all endpoints.
                        </p>
                        <div class="bg-dark-lighter rounded-md p-3 font-mono text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-300">X-API-KEY: your_api_key_here</span>
                                <button 
                                    @click="copyToClipboard('X-API-KEY: your_api_key_here')"
                                    class="text-primary hover:text-primary-hover transition-colors"
                                    title="Copy to clipboard"
                                >
                                    <Copy :size="16" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Endpoints -->
                <div class="max-w-6xl mx-auto">
                    <div class="grid gap-8">
                        <div 
                            v-for="endpoint in endpoints" 
                            :key="endpoint.id"
                            class="bg-dark-card border border-primary/20 rounded-lg overflow-hidden"
                        >
                            <!-- Endpoint Header -->
                            <div 
                                @click="toggleEndpoint(endpoint.id)"
                                class="p-6 cursor-pointer hover:bg-dark-lighter transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <span class="px-3 py-1 bg-primary text-white text-sm font-medium rounded-md">
                                            {{ endpoint.method }}
                                        </span>
                                        <div>
                                            <h3 class="text-lg font-semibold text-white">{{ endpoint.route }}</h3>
                                            <p class="text-gray-400 text-sm">{{ endpoint.description }}</p>
                                        </div>
                                    </div>
                                    <div class="transition-transform duration-200" :class="{ 'rotate-180': expandedEndpoints.includes(endpoint.id) }">
                                        <ChevronDown :size="20" class="text-gray-400" />
                                    </div>
                                </div>
                            </div>

                            <!-- Endpoint Details -->
                            <div v-if="expandedEndpoints.includes(endpoint.id)" class="border-t border-primary/20">
                                <div class="p-6 space-y-6">
                                    <!-- Parameters -->
                                    <div v-if="endpoint.parameters && endpoint.parameters.length > 0">
                                        <h4 class="text-lg font-semibold text-white mb-3">Parameters</h4>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="border-b border-gray-600">
                                                        <th class="text-left py-2 text-gray-300">Name</th>
                                                        <th class="text-left py-2 text-gray-300">Type</th>
                                                        <th class="text-left py-2 text-gray-300">Required</th>
                                                        <th class="text-left py-2 text-gray-300">Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="param in endpoint.parameters" :key="param.name" class="border-b border-gray-700">
                                                        <td class="py-2 text-primary font-mono">{{ param.name }}</td>
                                                        <td class="py-2 text-gray-300">{{ param.type }}</td>
                                                        <td class="py-2">
                                                            <span 
                                                                :class="param.required ? 'bg-red-500/20 text-red-400' : 'bg-gray-500/20 text-gray-400'"
                                                                class="px-2 py-1 rounded text-xs"
                                                            >
                                                                {{ param.required ? 'Required' : 'Optional' }}
                                                            </span>
                                                        </td>
                                                        <td class="py-2 text-gray-300">{{ param.description }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Code Examples -->
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <!-- Request Example -->
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-lg font-semibold text-white">Request Example</h4>
                                                <button 
                                                    @click="copyToClipboard(endpoint.requestExample)"
                                                    class="text-primary hover:text-primary-hover transition-colors"
                                                    title="Copy request"
                                                >
                                                    <Copy :size="16" />
                                                </button>
                                            </div>
                                            <div class="bg-dark-lighter rounded-md p-4 overflow-x-auto">
                                                <pre class="text-sm text-gray-300 whitespace-pre-wrap"><code>{{ endpoint.requestExample }}</code></pre>
                                            </div>
                                        </div>

                                        <!-- Response Example -->
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-lg font-semibold text-white">Response Example</h4>
                                                <button 
                                                    @click="copyToClipboard(endpoint.responseExample)"
                                                    class="text-primary hover:text-primary-hover transition-colors"
                                                    title="Copy response"
                                                >
                                                    <Copy :size="16" />
                                                </button>
                                            </div>
                                            <div class="bg-dark-lighter rounded-md p-4 overflow-x-auto">
                                                <pre class="text-sm text-gray-300 whitespace-pre-wrap"><code>{{ endpoint.responseExample }}</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="text-center mt-16">
                    <p class="text-gray-400 text-sm">
                        Need help? Contact our support team or check out the implementation examples above.
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref } from 'vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { ChevronDown, Copy } from 'lucide-vue-next'

const expandedEndpoints = ref(['balance']) // Start with balance expanded

const endpoints = ref([
    {
        id: 'balance',
        method: 'POST',
        route: '/api/balance',
        description: 'Retrieve user\'s current balance and account information',
        parameters: [],
        requestExample: `curl -X POST ${window.location.origin}/api/balance \\
  -H "X-API-KEY: your_api_key_here" \\
  -H "Content-Type: application/json"`,
        responseExample: `{
  "status": true,
  "message": "Saldo berhasil didapatkan",
  "balance": 150000,
  "currency": "IDR"
}`
    },
    {
        id: 'products',
        method: 'POST',
        route: '/api/products',
        description: 'List all available products with optional category filtering',
        parameters: [
            {
                name: 'kategori',
                type: 'string',
                required: false,
                description: 'Filter products by category name'
            }
        ],
        requestExample: `curl -X POST ${window.location.origin}/api/products \\
  -H "X-API-KEY: your_api_key_here" \\
  -H "Content-Type: application/json" \\
  -d '{
    "kategori": "Game Mobile"
  }'`,
        responseExample: `{
  "status": true,
  "message": "Produk berhasil didapatkan",
  "products": [
    {
      "id": 1,
      "nama": "Mobile Legends",
      "kategori": {
        "id": 1,
        "kategori_name": "Game Mobile"
      }
    }
  ]
}`
    },
    {
        id: 'services',
        method: 'POST',
        route: '/api/services',
        description: 'List all available services with optional product filtering',
        parameters: [
            {
                name: 'produk_id',
                type: 'integer',
                required: false,
                description: 'Filter services by product ID'
            }
        ],
        requestExample: `curl -X POST ${window.location.origin}/api/services \\
  -H "X-API-KEY: your_api_key_here" \\
  -H "Content-Type: application/json" \\
  -d '{
    "produk_id": 1
  }'`,
        responseExample: `{
  "status": true,
  "message": "Data layanan berhasil diambil",
  "services": [
    {
      "id": 1,
      "nama": "86 Diamonds",
      "produk": {
        "id": 1,
        "nama": "Mobile Legends"
      },
      "harga": 25000
    }
  ]
}`
    },
    {
        id: 'order',
        method: 'POST',
        route: '/api/order',
        description: 'Create a new order for the specified service',
        parameters: [
            {
                name: 'layanan_id',
                type: 'integer',
                required: true,
                description: 'ID of the service to order'
            },
            {
                name: 'quantity',
                type: 'integer',
                required: true,
                description: 'Quantity of service items (minimum: 1)'
            },
            {
                name: 'target',
                type: 'string',
                required: true,
                description: 'Target account (format: user_id or user_id-server_id)'
            },
            {
                name: 'phone',
                type: 'string',
                required: false,
                description: 'Contact phone number'
            },
            {
                name: 'email',
                type: 'string',
                required: false,
                description: 'Contact email address'
            }
        ],
        requestExample: `curl -X POST ${window.location.origin}/api/order \\
  -H "X-API-KEY: your_api_key_here" \\
  -H "Content-Type: application/json" \\
  -d '{
    "layanan_id": 1,
    "quantity": 1,
    "target": "123456789",
    "phone": "081234567890",
    "email": "user@example.com"
  }'`,
        responseExample: `{
  "status": true,
  "message": "Order diproses",
  "order_id": "NAPI150625001",
  "success_orders": [
    {
      "reference": "NAPI150625001",
      "status": "completed"
    }
  ],
  "failed_orders": []
}`
    },
    {
        id: 'order-status',
        method: 'POST',
        route: '/api/order/status',
        description: 'Check the current status of an existing order',
        parameters: [
            {
                name: 'order_id',
                type: 'string',
                required: true,
                description: 'Unique order ID returned from order creation'
            }
        ],
        requestExample: `curl -X POST ${window.location.origin}/api/order/status \\
  -H "X-API-KEY: your_api_key_here" \\
  -H "Content-Type: application/json" \\
  -d '{
    "order_id": "NAPI150625001"
  }'`,
        responseExample: `{
  "status": "success",
  "message": "Status order sukses diambil",
  "data": {
    "order_id": "NAPI150625001",
    "status": "completed"
  }
}`
    }
])

const toggleEndpoint = (endpointId) => {
    const index = expandedEndpoints.value.indexOf(endpointId)
    if (index > -1) {
        expandedEndpoints.value.splice(index, 1)
    } else {
        expandedEndpoints.value.push(endpointId)
    }
}

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        // Could add a toast notification here
    } catch (err) {
        console.error('Failed to copy text: ', err)
    }
}
</script>

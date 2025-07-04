<script setup>
import { ref } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { ChevronDown, Copy } from "lucide-vue-next";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import { useToast } from "@/Composables/useToast";

const page = usePage();
const user = page.props.auth.user;

const expandedEndpoints = ref(["balance"]); // Start with balance expanded

const toggleEndpoint = (endpointId) => {
    const index = expandedEndpoints.value.indexOf(endpointId);
    if (index > -1) {
        expandedEndpoints.value.splice(index, 1);
    } else {
        expandedEndpoints.value.push(endpointId);
    }
};

const isCopied = ref(false);

const copyToClipboard = (text) => {
    navigator.clipboard
        .writeText(text)
        .then(() => {
            isCopied.value = true;
            toast.success("Teks berhasil disalin");
            setTimeout(() => (isCopied.value = false), 2000);
        })
        .catch((err) => {
            console.error("Gagal menyalin teks: ", err);
        });
};

const { toast } = useToast();

const whitelistIps = ref(user.ip_whitelist || "");

const saveWhitelistIps = async () => {
    try {
        const response = await axios.post(route("api.update-ip-whitelist"), {
            ip_whitelist: whitelistIps.value,
        });
        // check response is true
        if (response.data.status) {
            toast.success("Whitelist IP berhasil disimpan");
        } else {
            toast.error("Whitelist IP gagal disimpan");
        }
    } catch (error) {
        console.error(error);
        toast.error("Request gagal");
    }
};

const endpoints = ref([
    {
        id: "balance",
        method: "POST",
        route: "/api/balance",
        description: "Retrieve user's current balance and account information",
        parameters: [],
        requestExample: `curl -X POST ${window.location.origin}/api/balance \\
  -H "X-API-KEY: your_api_key_here" \\
  -H "Content-Type: application/json"`,
        responseExample: `{
  "status": true,
  "message": "Saldo berhasil didapatkan",
  "balance": 150000,
  "currency": "IDR"
}`,
    },
    {
        id: "products",
        method: "POST",
        route: "/api/products",
        description:
            "List all available products with optional category filtering",
        parameters: [
            {
                name: "kategori",
                type: "string",
                required: false,
                description: "Filter products by category name",
            },
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
}`,
    },
    {
        id: "services",
        method: "POST",
        route: "/api/services",
        description:
            "List all available services with optional product filtering",
        parameters: [
            {
                name: "produk_id",
                type: "integer",
                required: false,
                description: "Filter services by product ID",
            },
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
}`,
    },
    {
        id: "order",
        method: "POST",
        route: "/api/order",
        description: "Create a new order for the specified service",
        parameters: [
            {
                name: "layanan_id",
                type: "integer",
                required: true,
                description: "ID of the service to order",
            },
            {
                name: "quantity",
                type: "integer",
                required: true,
                description: "Quantity of service items (minimum: 1)",
            },
            {
                name: "target",
                type: "string",
                required: true,
                description:
                    "Target account (format: user_id or user_id-server_id)",
            },
            {
                name: "phone",
                type: "string",
                required: false,
                description: "Contact phone number",
            },
            {
                name: "email",
                type: "string",
                required: false,
                description: "Contact email address",
            },
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
}`,
    },
    {
        id: "order-status",
        method: "POST",
        route: "/api/order/status",
        description: "Check the current status of an existing order",
        parameters: [
            {
                name: "order_id",
                type: "string",
                required: true,
                description: "Unique order ID returned from order creation",
            },
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
}`,
    },
]);
</script>

<template>
    <GuestLayout>
        <div class="min-h-screen bg-content_background">
            <!-- Cosmic Background -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute w-2 h-2 rounded-full top-20 left-10 bg-primary animate-ping-small opacity-60"
                ></div>
                <div
                    class="absolute w-1 h-1 rounded-full top-40 right-20 bg-secondary animate-pulse-slow opacity-40"
                ></div>
                <div
                    class="absolute bottom-40 left-1/4 w-1.5 h-1.5 bg-primary rounded-full animate-bounce-slow opacity-50"
                ></div>
            </div>

            <div class="container relative z-10 px-4 py-12 mx-auto">
                <!-- Header Section -->
                <div class="mb-16 text-center">
                    <h1 class="mb-6 text-4xl font-bold text-white md:text-5xl">
                        <span
                            class="text-transparent bg-gradient-to-r from-primary to-secondary bg-clip-text"
                        >
                            API Documentation
                        </span>
                    </h1>
                    <p class="max-w-3xl mx-auto mb-8 text-lg text-gray-300">
                        Integrate our powerful top-up services into your
                        application with our RESTful API. All endpoints require
                        authentication and return JSON responses.
                    </p>

                    <!-- Authentication Info -->
                    <div
                        class="max-w-3xl p-6 mx-auto border rounded-lg bg-dark-card border-primary/20"
                    >
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-2 h-2 rounded-full bg-primary animate-pulse"
                            ></div>
                            <h3 class="text-lg font-semibold text-white">
                                Authentication Required
                            </h3>
                        </div>
                        <p class="mb-4 text-sm text-gray-300">
                            Include your API key in the request header for all
                            endpoints.
                        </p>
                        <div
                            class="p-3 font-mono text-xs rounded-md md:text-sm bg-dark-lighter"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-gray-300"
                                    >X-API-KEY: {{ user.api_key }}</span
                                >
                                <button
                                    @click="
                                        copyToClipboard(
                                            'X-API-KEY: ' + user.api_key
                                        )
                                    "
                                    class="transition-colors text-primary hover:text-primary-hover"
                                    title="Copy to clipboard"
                                >
                                    <Copy :size="16" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Whitelist IP Info -->
                    <div
                        class="max-w-3xl p-6 mx-auto mt-6 border rounded-lg bg-dark-card border-secondary/20"
                    >
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-2 h-2 rounded-full bg-secondary animate-pulse"
                            ></div>
                            <h3 class="text-lg font-semibold text-white">
                                Whitelist IPs
                            </h3>
                        </div>
                        <p class="mb-4 text-sm text-gray-300">
                            Only the IPs listed here can access the endpoint.
                            Add more than 1 IP with a comma separator (,).
                        </p>
                        <input
                            v-model="whitelistIps"
                            type="text"
                            placeholder="Example: 123.123.123.123, 111.222.333.444"
                            class="w-full px-4 py-2 text-sm text-white border rounded-md bg-dark-lighter border-secondary/30 focus:outline-none focus:ring-2 focus:ring-secondary"
                        />
                        <div class="flex justify-end mt-4">
                            <button
                                @click="saveWhitelistIps"
                                class="px-4 py-2 text-sm font-semibold text-white transition rounded-md bg-primary hover:bg-primary-hover"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- API Endpoints -->
                <div class="max-w-6xl mx-auto">
                    <div class="grid gap-8">
                        <div
                            v-for="endpoint in endpoints"
                            :key="endpoint.id"
                            class="overflow-hidden border rounded-lg bg-dark-card border-primary/20"
                        >
                            <!-- Endpoint Header -->
                            <div
                                @click="toggleEndpoint(endpoint.id)"
                                class="p-6 transition-colors cursor-pointer hover:bg-dark-lighter"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <span
                                            class="px-3 py-1 text-sm font-medium text-white rounded-md bg-primary"
                                        >
                                            {{ endpoint.method }}
                                        </span>
                                        <div>
                                            <h3
                                                class="text-lg font-semibold text-white"
                                            >
                                                {{ endpoint.route }}
                                            </h3>
                                            <p class="text-sm text-gray-400">
                                                {{ endpoint.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                expandedEndpoints.includes(
                                                    endpoint.id
                                                ),
                                        }"
                                    >
                                        <ChevronDown
                                            :size="20"
                                            class="text-gray-400"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Endpoint Details -->
                            <div
                                v-if="expandedEndpoints.includes(endpoint.id)"
                                class="border-t border-primary/20"
                            >
                                <div class="p-6 space-y-6">
                                    <!-- Parameters -->
                                    <div
                                        v-if="
                                            endpoint.parameters &&
                                            endpoint.parameters.length > 0
                                        "
                                    >
                                        <h4
                                            class="mb-3 text-lg font-semibold text-white"
                                        >
                                            Parameters
                                        </h4>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr
                                                        class="border-b border-gray-600"
                                                    >
                                                        <th
                                                            class="py-2 text-left text-gray-300"
                                                        >
                                                            Name
                                                        </th>
                                                        <th
                                                            class="py-2 text-left text-gray-300"
                                                        >
                                                            Type
                                                        </th>
                                                        <th
                                                            class="py-2 text-left text-gray-300"
                                                        >
                                                            Required
                                                        </th>
                                                        <th
                                                            class="py-2 text-left text-gray-300"
                                                        >
                                                            Description
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="param in endpoint.parameters"
                                                        :key="param.name"
                                                        class="border-b border-gray-700"
                                                    >
                                                        <td
                                                            class="py-2 font-mono text-primary"
                                                        >
                                                            {{ param.name }}
                                                        </td>
                                                        <td
                                                            class="py-2 text-gray-300"
                                                        >
                                                            {{ param.type }}
                                                        </td>
                                                        <td class="py-2">
                                                            <span
                                                                :class="
                                                                    param.required
                                                                        ? 'bg-red-500/20 text-red-400'
                                                                        : 'bg-gray-500/20 text-gray-400'
                                                                "
                                                                class="px-2 py-1 text-xs rounded"
                                                            >
                                                                {{
                                                                    param.required
                                                                        ? "Required"
                                                                        : "Optional"
                                                                }}
                                                            </span>
                                                        </td>
                                                        <td
                                                            class="py-2 text-gray-300"
                                                        >
                                                            {{
                                                                param.description
                                                            }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Code Examples -->
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <!-- Request Example -->
                                        <div>
                                            <div
                                                class="flex items-center justify-between mb-3"
                                            >
                                                <h4
                                                    class="text-lg font-semibold text-white"
                                                >
                                                    Request Example
                                                </h4>
                                                <button
                                                    @click="
                                                        copyToClipboard(
                                                            endpoint.requestExample
                                                        )
                                                    "
                                                    class="transition-colors text-primary hover:text-primary-hover"
                                                    title="Copy request"
                                                >
                                                    <Copy :size="16" />
                                                </button>
                                            </div>
                                            <div
                                                class="p-4 overflow-x-auto rounded-md bg-dark-lighter"
                                            >
                                                <pre
                                                    class="text-sm text-gray-300 whitespace-pre-wrap"
                                                ><code>{{ endpoint.requestExample }}</code></pre>
                                            </div>
                                        </div>

                                        <!-- Response Example -->
                                        <div>
                                            <div
                                                class="flex items-center justify-between mb-3"
                                            >
                                                <h4
                                                    class="text-lg font-semibold text-white"
                                                >
                                                    Response Example
                                                </h4>
                                                <button
                                                    @click="
                                                        copyToClipboard(
                                                            endpoint.responseExample
                                                        )
                                                    "
                                                    class="transition-colors text-primary hover:text-primary-hover"
                                                    title="Copy response"
                                                >
                                                    <Copy :size="16" />
                                                </button>
                                            </div>
                                            <div
                                                class="p-4 overflow-x-auto rounded-md bg-dark-lighter"
                                            >
                                                <pre
                                                    class="text-sm text-gray-300 whitespace-pre-wrap"
                                                ><code>{{ endpoint.responseExample }}</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="mt-16 text-center">
                    <p class="text-sm text-gray-400">
                        Need help? Contact our support team or check out the
                        implementation examples above.
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed, getCurrentInstance, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import Modal from "@/Components/Modal.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Pagination from "@/Components/Pagination.vue";
import axios from "axios";

const props = defineProps({
    products: Object,
    kategori_list: Object,
    provider_list: Object,
    errors: Object,
});

const { proxy } = getCurrentInstance();

const products = computed(() => props.products.data || []);

const columns = [
    { key: "id", label: "ID" },
    { key: "nama", label: "Produk" },
    {
        key: "kategori_id",
        label: "Kategori",
        format: (value) => {
            const kategori = props.kategori_list.find(
                (item) => Number(item.id) === Number(value)
            );
            return kategori ? kategori.kategori_name : "Tidak Diketahui";
        },
    },
    { key: "brand", label: "Brand" },
    {
        key: "provider_id",
        label: "Provider",
        format: (value) => {
            const provider = props.provider_list.find(
                (item) => Number(item.id) === Number(value)
            );

            return provider ? provider.provider_name : "Tidak Diketahui";
        },
    },
    { key: "sistem_id", label: "Sistem ID" },
    { key: "validasi_id", label: "Validasi ID" },
    {
        key: "status",
        label: "Status",
        format: (value) => {
            const statusClasses =
                value === "active"
                    ? "bg-green-500/20 text-green-400"
                    : "bg-red-500/20 text-red-400";

            return `<span class="${statusClasses} px-2 py-1 rounded-full text-xs">${value}</span>`;
        },
    },
];

const showViewModal = ref(false);
const selectedProduct = ref(null);
const isLoading = ref(false);

const handleView = async (item) => {
    isLoading.value = true;
    selectedProduct.value = { ...item, loading: true };
    showViewModal.value = true;

    try {
        const response = await axios.get(route("products.show", item.id));
        selectedProduct.value = response.data.product;
    } catch (error) {
        console.error("Error fetching product details:", error);
        proxy.$showSwalConfirm({
            title: "Error",
            text: "Failed to load product details",
            icon: "error",
        });
    } finally {
        isLoading.value = false;
    }
};

const handleEdit = (item) => {
    openEditForm(item);
};

const handleDelete = (item) => {
    proxy.$showSwalConfirm({
        onConfirm: () => {
            router.delete(route("products.destroy", item.id), {
                preserveScroll: true,
            });
        },
    });
};

const showForm = ref(false);
const formMode = ref("add");
const currentProduct = ref({
    nama: "",
    developer: "",
    brand: "",
    kategori_id: "",
    slug: "",
    provider_id: "",
    sistem_id: "",
    validasi_id: "",
    deskripsi_game: "",
    petunjuk_field: "",
    thumbnail: "",
    status: "active",
});

const openAddForm = () => {
    formMode.value = "add";
    currentProduct.value = {
        nama: "",
        status: "active",
    };
    showForm.value = true;
};

const openEditForm = (data) => {
    if (showViewModal.value) {
        showViewModal.value = false;
    }
    formMode.value = "edit";
    currentProduct.value = { ...data };
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
};

const closeViewModal = () => {
    showViewModal.value = false;
    selectedProduct.value = null;
};

const saveDataForm = () => {
    if (formMode.value === "add") {
        router.post(route("products.store"), currentProduct.value, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                currentProduct.value.category_name = "";
            },
        });
    } else {
        router.put(
            route("products.update", currentProduct.value.id),
            currentProduct.value,
            {
                preserveScroll: true,
            }
        );
    }

    closeForm();
};

watch(
    () => currentProduct.value.nama,
    (newVal) => {
        if (newVal) {
            currentProduct.value.slug = newVal
                .toLowerCase()
                .replace(/\s+/g, "-")
                .replace(/[^\w-]+/g, "")
                .replace(/--+/g, "-")
                .trim();
        } else {
            currentProduct.value.slug = "";
        }
    }
);

const imagePreviews = ref({
    petunjuk_field: null,
    thumbnail: null,
});

const getImagePreview = (field) => {
    return computed(() => {
        if (
            typeof currentProduct.value[field] === "string" &&
            currentProduct.value[field]
        ) {
            return `/storage/${currentProduct.value[field]}`;
        } else if (imagePreviews.value[field]) {
            return imagePreviews.value[field];
        }
        return null;
    });
};

const handleFileUpload = (event, field) => {
    const file = event.target.files[0];
    if (file) {
        currentProduct.value[field] = file;
        imagePreviews.value[field] = URL.createObjectURL(file);
    }
};
</script>

<template>
    <Head title="Products" />

    <AdminLayout title="Products">
        <div
            v-if="Object.keys(errors).length > 0"
            class="px-8 py-3 mb-4 text-sm text-white bg-red-500/80"
        >
            <ul v-for="error in errors">
                <li>{{ error }}</li>
            </ul>
        </div>
        <div
            class="w-full overflow-hidden border rounded-lg shadow-lg border-primary/40 bg-dark-card"
        >
            <DataTable :data="products" :columns="columns" class="w-full">
                <template #title> List Products </template>

                <template #addButton>
                    <button
                        @click="openAddForm"
                        class="flex items-center px-4 py-2 space-x-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                        <span>Add Products</span>
                    </button>
                </template>

                <template #actions="{ item }">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                class="inline-flex items-center justify-center p-2 text-gray-400 transition-colors rounded-full hover:text-white hover:bg-dark-lighter"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                    />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <div
                                class="py-1 border border-gray-700 rounded-lg shadow-lg bg-dark-card"
                            >
                                <button
                                    @click="handleView(item)"
                                    class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-secondary"
                                >
                                    View
                                </button>
                                <button
                                    @click="handleEdit(item)"
                                    class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-primary"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="handleDelete(item)"
                                    class="block w-full px-4 py-2 text-sm text-left text-gray-300 hover:bg-dark-lighter hover:text-red-400"
                                >
                                    Delete
                                </button>
                            </div>
                        </template>
                    </Dropdown>
                </template>

                <template #cell(icon)="{ item }">
                    <div
                        class="flex items-center justify-center w-8 h-8 text-white rounded-full bg-primary/20"
                    >
                        {{ item.icon }}
                    </div>
                </template>
            </DataTable>
            <Pagination :links="props.products.links" />
        </div>

        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black/50"
            @click.self="closeForm"
        >
            <div
                class="relative w-full max-w-full mx-4 p-4 border border-gray-700 rounded-lg shadow-lg sm:p-6 sm:max-w-lg md:max-w-xl lg:max-w-2xl bg-dark-card max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">
                        {{
                            formMode === "add"
                                ? "Add New Products"
                                : "Edit Products"
                        }}
                    </h3>
                    <button
                        @click="closeForm"
                        class="text-gray-400 hover:text-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveDataForm" class="overflow-visible">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    for="nama"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Product</label
                                >
                                <input
                                    id="nama"
                                    v-model="currentProduct.nama"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Product Name"
                                    name="nama"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    for="developer"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Developer</label
                                >
                                <input
                                    id="developer"
                                    v-model="currentProduct.developer"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Developer Name"
                                    name="developer"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    for="brand"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Brand</label
                                >
                                <input
                                    id="brand"
                                    v-model="currentProduct.brand"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Brand Name"
                                    name="brand"
                                    required
                                />
                            </div>
                            <div v-if="kategori_list">
                                <label
                                    for="kategori_id"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Kategori</label
                                >
                                <select
                                    name="kategori_id"
                                    id="kategori_id"
                                    v-model="currentProduct.kategori_id"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                >
                                    <option
                                        v-for="(item, index) in kategori_list"
                                        :value="item.id"
                                        :key="index"
                                    >
                                        {{ item.kategori_name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    for="slug"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Slug</label
                                >
                                <input
                                    id="slug"
                                    v-model="currentProduct.slug"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Slug Name"
                                    name="slug"
                                    :disabled="formMode == 'add'"
                                    required
                                />
                            </div>

                            <div>
                                <label
                                    for="provider_id"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Provider</label
                                >
                                <select
                                    name="provider_id"
                                    id="provider_id"
                                    v-model="currentProduct.provider_id"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                >
                                    <option
                                        v-for="(item, index) in provider_list"
                                        :value="item.id"
                                        :key="index"
                                    >
                                        {{ item.provider_name.toUpperCase() }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    for="sistem_id"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Sistem ID</label
                                >
                                <input
                                    id="sistem_id"
                                    v-model="currentProduct.sistem_id"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Sistem ID"
                                    name="sistem_id"
                                    required
                                />
                            </div>

                            <div>
                                <label
                                    for="validasi_id"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Validasi ID</label
                                >
                                <input
                                    id="validasi_id"
                                    v-model="currentProduct.validasi_id"
                                    type="text"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Validasi ID"
                                    name="validasi_id"
                                    required
                                />
                            </div>

                            <div>
                                <label
                                    for="status"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Status</label
                                >
                                <select
                                    id="status"
                                    name="status"
                                    v-model="currentProduct.status"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-span-1 sm:col-span-2">
                                <div
                                    class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                                >
                                    <div>
                                        <label
                                            for="petunjuk_field"
                                            class="block mb-1 text-sm font-medium text-gray-300"
                                            >Petunjuk</label
                                        >
                                        <div
                                            v-if="
                                                getImagePreview(
                                                    'petunjuk_field'
                                                ).value
                                            "
                                            class="mb-2"
                                        >
                                            <img
                                                :src="
                                                    getImagePreview(
                                                        'petunjuk_field'
                                                    ).value
                                                "
                                                alt="Preview Petunjuk"
                                                class="object-cover w-32 h-32 border rounded-lg shadow-md border-primary/60"
                                            />
                                        </div>

                                        <input
                                            id="petunjuk_field"
                                            @change="
                                                handleFileUpload(
                                                    $event,
                                                    'petunjuk_field'
                                                )
                                            "
                                            type="file"
                                            class="w-full px-2 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                            placeholder="Petunjuk"
                                            name="petunjuk_field"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            for="thumbnail"
                                            class="block mb-1 text-sm font-medium text-gray-300"
                                            >Thumbnail</label
                                        >
                                        <div
                                            v-if="
                                                getImagePreview('thumbnail')
                                                    .value
                                            "
                                            class="mb-2"
                                        >
                                            <img
                                                :src="
                                                    getImagePreview('thumbnail')
                                                        .value
                                                "
                                                alt="Thumbnail"
                                                class="object-cover w-32 h-32 border rounded-lg shadow-md border-primary/60"
                                            />
                                        </div>

                                        <input
                                            id="thumbnail"
                                            @change="
                                                handleFileUpload(
                                                    $event,
                                                    'thumbnail'
                                                )
                                            "
                                            type="file"
                                            class="w-full px-2 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                            placeholder="Thumbnail"
                                            name="thumbnail"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-1 sm:col-span-2">
                                <label
                                    for="deskripsi_game"
                                    class="block mb-1 text-sm font-medium text-gray-300"
                                    >Deskripsi Game</label
                                >
                                <textarea
                                    id="deskripsi_game"
                                    v-model="currentProduct.deskripsi_game"
                                    class="w-full px-3 py-2 text-white border border-gray-700 rounded-lg bg-dark-sidebar focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Deskripsi Game"
                                    name="deskripsi_game"
                                    required
                                    rows="2"
                                />
                            </div>
                        </div>
                        <div
                            class="flex flex-col justify-end pt-4 space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3"
                        >
                            <button
                                type="button"
                                @click="closeForm"
                                class="w-full px-4 py-2 text-gray-300 rounded-lg bg-dark-lighter hover:text-white sm:w-auto"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="w-full px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg bg-primary hover:bg-primary-hover hover:shadow-glow-primary sm:w-auto"
                            >
                                {{
                                    formMode === "add"
                                        ? "Create Product"
                                        : "Update Product"
                                }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <Modal :show="showViewModal" @close="closeViewModal" max-width="xl">
            <div
                class="p-4 border border-gray-700 rounded-lg bg-dark-card sm:p-6 max-h-[80vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">
                        Product Details
                    </h3>
                    <button
                        @click="closeViewModal"
                        class="text-gray-400 hover:text-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div v-if="isLoading" class="flex justify-center py-8">
                    <div
                        class="w-10 h-10 border-4 rounded-full animate-spin border-primary border-t-transparent"
                    ></div>
                </div>

                <div v-else-if="selectedProduct" class="space-y-4">
                    <div
                        class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                    >
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Produk ID</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.id }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Product</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.nama }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Kategori</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.kategori.kategori_name }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Provider</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.provider.provider_name }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Developer</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.developer }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Brand</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.brand }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Slug</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.slug }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Sistem ID</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.sistem_id }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Validasi ID</p>
                            <p class="font-medium text-white truncate">
                                {{ selectedProduct.validasi_id }}
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-dark-lighter">
                            <p class="text-sm text-gray-400">Status</p>
                            <p>
                                <span
                                    :class="
                                        selectedProduct.status === 'active'
                                            ? 'bg-green-500/20 text-green-400'
                                            : 'bg-red-500/20 text-red-400'
                                    "
                                    class="px-2 py-1 text-xs rounded-full"
                                >
                                    {{ selectedProduct.status }}
                                </span>
                            </p>
                        </div>

                        <div
                            class="col-span-1 p-3 rounded-lg sm:col-span-2 lg:col-span-4 bg-dark-lighter"
                        >
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div v-if="selectedProduct.petunjuk_field">
                                    <p class="text-sm text-gray-400">
                                        Petunjuk
                                    </p>
                                    <img
                                        :src="
                                            '/storage/' +
                                            selectedProduct.petunjuk_field
                                        "
                                        alt="Preview Petunjuk"
                                        class="object-cover w-32 mt-2 border rounded-lg shadow-md border-primary/60"
                                    />
                                </div>
                                <div v-if="selectedProduct.thumbnail">
                                    <p class="text-sm text-gray-400">
                                        Thumbnail
                                    </p>
                                    <img
                                        :src="
                                            '/storage/' +
                                            selectedProduct.thumbnail
                                        "
                                        alt="Preview Thumbnail"
                                        class="object-cover w-32 mt-2 border rounded-lg shadow-md border-primary/60"
                                    />
                                </div>
                            </div>
                        </div>

                        <div
                            class="col-span-1 p-3 rounded-lg sm:col-span-2 lg:col-span-4 bg-dark-lighter"
                        >
                            <p class="text-sm text-gray-400">Deskripsi Game</p>
                            <p class="font-medium text-white break-words">
                                {{ selectedProduct.deskripsi_game }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-col justify-end pt-4 space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3"
                    >
                        <button
                            @click="openEditForm(selectedProduct)"
                            class="w-full px-4 py-2 text-white transition-all duration-200 rounded-lg shadow-lg sm:w-auto bg-primary hover:bg-primary-hover hover:shadow-glow-primary"
                        >
                            Edit Product
                        </button>
                        <button
                            @click="closeViewModal"
                            class="w-full px-4 py-2 text-gray-300 rounded-lg sm:w-auto bg-dark-lighter hover:text-white"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<style>
body {
    @apply bg-dark overflow-x-hidden min-h-screen;
    width: 100vw;
    overscroll-behavior: none;
}

.fixed.inset-0 {
    overflow-y: auto;
    height: 100%;
    max-height: 100vh;
}

.truncate {
    @apply overflow-hidden text-ellipsis whitespace-nowrap;
}

@media (max-width: 640px) {
    .p-6 {
        padding: 1rem;
    }

    .space-y-4 > * + * {
        margin-top: 0.75rem;
    }
}
</style>

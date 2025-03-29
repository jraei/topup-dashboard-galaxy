
<script setup>
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
</script>

<template>
  <Head title="Admin Dashboard" />

  <AdminLayout title="Admin Dashboard">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
      <!-- Stats Cards -->
      <div
        class="overflow-hidden bg-indigo-800 rounded-lg shadow-lg hover:shadow-xl transition duration-300"
      >
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div
              class="flex-shrink-0 p-3 bg-indigo-600 rounded-md"
            >
              <svg
                class="w-6 h-6 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-indigo-200 truncate">
                  Total Users
                </dt>
                <dd>
                  <div class="text-lg font-medium text-white">0</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div
        class="overflow-hidden bg-purple-800 rounded-lg shadow-lg hover:shadow-xl transition duration-300"
      >
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div
              class="flex-shrink-0 p-3 bg-purple-600 rounded-md"
            >
              <svg
                class="w-6 h-6 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-purple-200 truncate">
                  Total Deposits
                </dt>
                <dd>
                  <div class="text-lg font-medium text-white">0</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div
        class="overflow-hidden bg-pink-800 rounded-lg shadow-lg hover:shadow-xl transition duration-300"
      >
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div
              class="flex-shrink-0 p-3 bg-pink-600 rounded-md"
            >
              <svg
                class="w-6 h-6 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-pink-200 truncate">
                  Total Transactions
                </dt>
                <dd>
                  <div class="text-lg font-medium text-white">0</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div
        class="overflow-hidden bg-blue-800 rounded-lg shadow-lg hover:shadow-xl transition duration-300"
      >
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div
              class="flex-shrink-0 p-3 bg-blue-600 rounded-md"
            >
              <svg
                class="w-6 h-6 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-blue-200 truncate">
                  Active Products
                </dt>
                <dd>
                  <div class="text-lg font-medium text-white">0</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-8">
      <h2 class="text-xl font-semibold text-white mb-4">Recent Activity</h2>
      <div class="overflow-hidden bg-gray-700 rounded-lg shadow">
        <div class="px-4 py-5 sm:p-6">
          <p class="text-gray-300">No recent activity to display.</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
      <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
        <a
          href="#"
          class="flex items-center p-4 bg-gray-700 rounded-lg shadow-md hover:bg-gray-600 transition duration-300"
        >
          <div
            class="flex-shrink-0 p-3 mr-4 bg-indigo-600 rounded-md"
          >
            <svg
              class="w-6 h-6 text-white"
              xmlns="http://www.w3.org/2000/svg"
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
          </div>
          <div>
            <h3 class="text-lg font-medium text-white">Add New Product</h3>
            <p class="text-sm text-gray-300">Create a new product listing</p>
          </div>
        </a>

        <a
          href="#"
          class="flex items-center p-4 bg-gray-700 rounded-lg shadow-md hover:bg-gray-600 transition duration-300"
        >
          <div
            class="flex-shrink-0 p-3 mr-4 bg-pink-600 rounded-md"
          >
            <svg
              class="w-6 h-6 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-medium text-white">Manage Orders</h3>
            <p class="text-sm text-gray-300">View and update order status</p>
          </div>
        </a>

        <a
          href="#"
          class="flex items-center p-4 bg-gray-700 rounded-lg shadow-md hover:bg-gray-600 transition duration-300"
        >
          <div
            class="flex-shrink-0 p-3 mr-4 bg-green-600 rounded-md"
          >
            <svg
              class="w-6 h-6 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-medium text-white">Edit Website Settings</h3>
            <p class="text-sm text-gray-300">Update site configuration</p>
          </div>
        </a>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BusinessForm from './Auth/BusinessForm.vue';
import ApplicationForm from './Auth/ApplicationForm.vue';

const showBusinessModal = ref(false);
const showApplicationModal = ref(false);

const closeBusinessModal = () => (showBusinessModal.value = false);
const closeApplicationModal = () => (showApplicationModal.value = false);

const handleBusinessSubmit = () => {
  console.log("Business submitted");
  closeBusinessModal();
};

const handleApplicationSubmit = () => {
  console.log("Application submitted");
  closeApplicationModal();
};
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- 3 Column Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          
          <!-- Column 1 - Welcome & Actions -->
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
              Welcome Back!
            </h3>
            <p class="text-gray-600 mb-6">
              Use the quick actions below to manage your businesses and apply for licenses.
            </p>
            <div class="space-y-4">
              <button
                @click="showBusinessModal = true"
                class="w-full px-6 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700"
              >
                Create Business Info
              </button>
              <button
                @click="showApplicationModal = true"
                class="w-full px-6 py-3 bg-green-600 text-white rounded-lg shadow hover:bg-green-700"
              >
                Apply for License
              </button>
            </div>
          </div>

          <!-- Column 2 - Applications -->
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
              Your Applications
            </h3>
            <ul class="divide-y divide-gray-200">
              <li class="py-3">Application #1 (Pending)</li>
              <li class="py-3">Application #2 (Approved)</li>
              <li class="py-3">Application #3 (Rejected)</li>
            </ul>
          </div>

          <!-- Column 3 - Businesses -->
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
              Your Businesses
            </h3>
            <ul class="divide-y divide-gray-200">
              <li class="py-3">Business 1 (Dummy)</li>
              <li class="py-3">Business 2 (Dummy)</li>
              <li class="py-3">Business 3 (Dummy)</li>
            </ul>
          </div>

        </div>
      </div>
    </div>

    <!-- Business Modal -->
    <div
      v-if="showBusinessModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
      <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Business Info</h3>
          <button @click="closeBusinessModal" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <BusinessForm
          :submit="handleBusinessSubmit" 
          mode="standalone"
        />
      </div>
    </div>

    <!-- Application Modal -->
    <div
      v-if="showApplicationModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
      <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">License Application</h3>
          <button @click="closeApplicationModal" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <ApplicationForm
          :submit="handleApplicationSubmit"
          :processing="false"
          mode="standalone"
        />
      </div>
    </div>
  </AppLayout>
</template>

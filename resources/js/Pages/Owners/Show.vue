<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    owner: Object
});
</script>

<template>
  <AppLayout :title="`Owner - ${owner.full_name}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Owner Details
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <!-- Header Actions -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
              {{ owner.full_name }}
            </h1>

            <div class="flex space-x-2">
              <Link
                :href="`/owners/${owner.id}/edit`"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
              >
                Edit
              </Link>

              <Link
                href="/owners"
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
              >
                Back to List
              </Link>
            </div>
          </div>

          <!-- Owner Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">
                Personal Information
              </h2>

              <div>
                <label class="block text-sm font-medium text-gray-600">Full Name</label>
                <p class="text-lg text-gray-900">{{ owner.full_name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Job Title</label>
                <p class="text-lg text-gray-900">{{ owner.job_title || 'Not specified' }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Email</label>
                <p class="text-lg text-gray-900">
                  <a :href="`mailto:${owner.email}`" class="text-blue-600 hover:text-blue-800">
                    {{ owner.email }}
                  </a>
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Phone</label>
                <p class="text-lg text-gray-900">
                  <a :href="`tel:${owner.phone}`" class="text-blue-600 hover:text-blue-800">
                    {{ owner.phone }}
                  </a>
                </p>
              </div>
            </div>

            <!-- Identification & Address -->
            <div class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">
                Identification & Address
              </h2>

              <div>
                <label class="block text-sm font-medium text-gray-600">ID Type</label>
                <p class="text-lg text-gray-900 capitalize">
                  {{ owner.id_type?.replace('_', ' ') }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">ID Number</label>
                <p class="text-lg text-gray-900 font-mono">{{ owner.id_number }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Address</label>
                <p class="text-lg text-gray-900">{{ owner.address }}</p>
              </div>
            </div>
          </div>

          <!-- Timestamps -->
          <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
              <div>
                <span class="font-medium">Created:</span>
                {{ new Date(owner.created_at).toLocaleDateString() }}
              </div>
              <div>
                <span class="font-medium">Last Updated:</span>
                {{ new Date(owner.updated_at).toLocaleDateString() }}
              </div>
            </div>
          </div>
        </div>

        <!-- Future related data -->
        <div class="mt-6 bg-white shadow-xl sm:rounded-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Related Information</h2>
          <p class="text-gray-600">
            Businesses and applications related to this owner will be displayed here.
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

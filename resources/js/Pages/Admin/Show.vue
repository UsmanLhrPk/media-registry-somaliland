<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  admin: Object
});
</script>

<template>
  <AppLayout :title="`Admin - ${admin.name}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Admin Details
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <!-- Header Actions -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
              {{ admin.name }}
            </h1>

            <div class="flex space-x-2">
              <Link
                :href="`/admins/${admin.id}/edit`"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
              >
                Edit
              </Link>

              <Link
                href="/dashboard"
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
              >
                Back to List
              </Link>
            </div>
          </div>

          <!-- Admin Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">
                Personal Information
              </h2>

              <div>
                <label class="block text-sm font-medium text-gray-600">Full Name</label>
                <p class="text-lg text-gray-900">{{ admin.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Email</label>
                <p class="text-lg text-gray-900">
                  <a :href="`mailto:${admin.email}`" class="text-blue-600 hover:text-blue-800">
                    {{ admin.email }}
                  </a>
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Phone</label>
                <p class="text-lg text-gray-900">
                  <span v-if="admin.phone">
                    <a :href="`tel:${admin.phone}`" class="text-blue-600 hover:text-blue-800">
                      {{ admin.phone }}
                    </a>
                  </span>
                  <span v-else class="text-gray-500 italic">Not provided</span>
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Role</label>
                <span
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                >
                  {{ admin.role.charAt(0).toUpperCase() + admin.role.slice(1) }}
                </span>
              </div>
            </div>

            <!-- Professional Information -->
            <div class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">
                Professional Information
              </h2>

              <div>
                <label class="block text-sm font-medium text-gray-600">Department</label>
                <p class="text-lg text-gray-900">
                  {{ admin.department || 'Not specified' }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Designation</label>
                <p class="text-lg text-gray-900">
                  {{ admin.designation || 'Not specified' }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-600">Account Status</label>
                <span
                  v-if="admin.email_verified_at"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
                >
                  ✔ Verified
                </span>
                <span
                  v-else
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"
                >
                  ⏳ Pending Verification
                </span>
              </div>
            </div>
          </div>

          <!-- Timestamps -->
          <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
              <div>
                <span class="font-medium">Created:</span>
                {{ new Date(admin.created_at).toLocaleDateString() }}
              </div>
              <div>
                <span class="font-medium">Last Updated:</span>
                {{ new Date(admin.updated_at).toLocaleDateString() }}
              </div>
            </div>
          </div>
        </div>

        <!-- Related Section -->
        <div class="mt-6 bg-white shadow-xl sm:rounded-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Related Information</h2>
          <p class="text-gray-600">
            Activities and system access related to this admin will be displayed here in future.
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

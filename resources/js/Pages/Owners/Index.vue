<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  owners: Array
});

// Delete function
function deleteOwner(ownerId) {
  if (confirm('Are you sure you want to delete this owner?')) {
    const form = useForm({});
    form.delete(`/owners/${ownerId}`);
  }
}
</script>

<template>
  <AppLayout title="Owners">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Owners Management
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Owners List</h3>
            <Link
              href="/owners/create"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              Create Owner
            </Link>
          </div>

          <!-- Owners Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Full Name</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="owner in owners" :key="owner.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-800">{{ owner.id }}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">{{ owner.full_name }}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">{{ owner.email }}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">{{ owner.phone }}</td>
                  <td class="px-6 py-4 text-right text-sm space-x-3">
                    <!-- View -->
                    <Link
                      :href="`/owners/${owner.id}`"
                      class="text-green-600 hover:text-green-800 font-medium"
                    >
                      View
                    </Link>
                    <!-- Edit -->
                    <Link
                      :href="`/owners/${owner.id}/edit`"
                      class="text-blue-600 hover:text-blue-800 font-medium"
                    >
                      Edit
                    </Link>
                    <!-- Delete -->
                    <button
                      @click="deleteOwner(owner.id)"
                      class="text-red-600 hover:text-red-800 font-medium"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div
            v-if="owners.length === 0"
            class="text-center py-8 text-gray-500"
          >
            No owners found.
            <Link href="/owners/create" class="text-blue-600 hover:text-blue-800">
              Create the first one!
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

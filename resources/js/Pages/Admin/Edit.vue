<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    admin: Object
});

const form = useForm({
    name: props.admin.name,
    email: props.admin.email,
    password: '',
    password_confirmation: '',
    phone: props.admin.phone || '',
    department: props.admin.department || '',
    designation: props.admin.designation || '',
});

function submit() {
    form.put(`/admins/${props.admin.id}`);
}
</script>

<template>
  <AppLayout :title="`Edit Admin - ${admin.name}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Admin
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <!-- Header Actions -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
              Editing: {{ admin.name }}
            </h1>

            <Link
              href="/dashboard"
              class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
            >
              Back to List
            </Link>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Info -->
            <div class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">
                Personal Information
              </h2>

              <div>
                <label class="block text-sm font-medium mb-1">Full Name *</label>
                <input v-model="form.name" type="text" required
                  class="w-full border rounded px-3 py-2" />
                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Email *</label>
                <input v-model="form.email" type="email" required
                  class="w-full border rounded px-3 py-2" />
                <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input v-model="form.phone" type="text"
                  class="w-full border rounded px-3 py-2" />
                <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}</div>
              </div>
            </div>

            <!-- Work Info -->
            <div class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">
                Work Information
              </h2>

              <div>
                <label class="block text-sm font-medium mb-1">Department</label>
                <input v-model="form.department" type="text"
                  placeholder="e.g., IT, HR, Finance"
                  class="w-full border rounded px-3 py-2" />
                <div v-if="form.errors.department" class="text-red-600 text-sm mt-1">{{ form.errors.department }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Designation</label>
                <input v-model="form.designation" type="text"
                  placeholder="e.g., Admin Officer, System Administrator"
                  class="w-full border rounded px-3 py-2" />
                <div v-if="form.errors.designation" class="text-red-600 text-sm mt-1">{{ form.errors.designation }}</div>
              </div>
            </div>

            <!-- Password Change -->
            <div class="col-span-2 space-y-4">
              <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">
                Change Password (Optional)
              </h2>
              <p class="text-sm text-gray-600">Leave blank if you don’t want to change the password.</p>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1">New Password</label>
                  <input v-model="form.password" type="password"
                    class="w-full border rounded px-3 py-2" />
                  <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium mb-1">Confirm Password</label>
                  <input v-model="form.password_confirmation" type="password"
                    class="w-full border rounded px-3 py-2" />
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="col-span-2 flex justify-end space-x-4 pt-6 border-t">
              <button type="submit" :disabled="form.processing"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
                {{ form.processing ? 'Updating...' : 'Update Admin' }}
              </button>

              <Link href="/dashboard" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

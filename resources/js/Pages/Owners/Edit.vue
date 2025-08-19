<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    owner: Object
});

const form = useForm({
    full_name: props.owner.full_name,
    job_title: props.owner.job_title,
    id_number: props.owner.id_number,
    id_type: props.owner.id_type,
    address: props.owner.address,
    phone: props.owner.phone,
    email: props.owner.email,
});

function submit() {
    form.put(`/owners/${props.owner.id}`);
}
</script>

<template>
  <AppLayout :title="`Edit Owner - ${owner.full_name}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Owner
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <!-- Header Actions -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
              Editing: {{ owner.full_name }}
            </h1>

            <Link
              href="/owners"
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
                <label class="block text-sm font-medium mb-1">Full Name</label>
                <input v-model="form.full_name" type="text" required
                  class="w-full border rounded px-3 py-2" />
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Job Title</label>
                <input v-model="form.job_title" type="text"
                  class="w-full border rounded px-3 py-2" />
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input v-model="form.email" type="email" required
                  class="w-full border rounded px-3 py-2" />
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input v-model="form.phone" type="text" required
                  class="w-full border rounded px-3 py-2" />
              </div>
            </div>

            <!-- Identification & Address -->
            <div class="space-y-4">
              <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">
                Identification & Address
              </h2>

              <div>
                <label class="block text-sm font-medium mb-1">ID Type</label>
                <select v-model="form.id_type" required class="w-full border rounded px-3 py-2">
                  <option value="id_card">ID Card</option>
                  <option value="passport">Passport</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">ID Number</label>
                <input v-model="form.id_number" type="text" required
                  class="w-full border rounded px-3 py-2 font-mono" />
              </div>

              <div>
                <label class="block text-sm font-medium mb-1">Address</label>
                <textarea v-model="form.address" required
                  class="w-full border rounded px-3 py-2 h-20"></textarea>
              </div>
            </div>

            <!-- Actions -->
            <div class="col-span-2 flex justify-end space-x-4 pt-6 border-t">
              <button type="submit" :disabled="form.processing"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
                {{ form.processing ? 'Updating...' : 'Update Owner' }}
              </button>

              <Link href="/owners" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

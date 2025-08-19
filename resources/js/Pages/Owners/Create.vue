<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({
    full_name: '',
    job_title: '',
    id_number: '',
    id_type: 'id_card',
    address: '',
    phone: '',
    email: '',
});

function submit() {
    form.post('/owners');
}
</script>

<template>
      <AppLayout title="Create Owner">
         <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
       Create Owner
      </h2>
    </template>

    <div class="p-6 max-w-4xl mx-auto">
        <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6 space-y-8">
            <!-- Personal Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                    Personal Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                        <input v-model="form.full_name" type="text" required
                            class="w-full border rounded px-3 py-2" placeholder="Full Name" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Job Title</label>
                        <input v-model="form.job_title" type="text"
                            class="w-full border rounded px-3 py-2" placeholder="Job Title" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                        <input v-model="form.email" type="email" required
                            class="w-full border rounded px-3 py-2" placeholder="Email" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                        <input v-model="form.phone" type="text" required
                            class="w-full border rounded px-3 py-2" placeholder="Phone" />
                    </div>
                </div>
            </div>

            <!-- Identification & Address -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                    Identification & Address
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ID Number</label>
                        <input v-model="form.id_number" type="text" required
                            class="w-full border rounded px-3 py-2" placeholder="ID Number" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ID Type</label>
                        <select v-model="form.id_type" required
                            class="w-full border rounded px-3 py-2">
                            <option value="id_card">ID Card</option>
                            <option value="passport">Passport</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                        <textarea v-model="form.address" required
                            class="w-full border rounded px-3 py-2 h-24"
                            placeholder="Address"></textarea>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit" :disabled="form.processing"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Owner' }}
                </button>

                <Link href="/owners"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancel
                </Link>
            </div>
        </form>
    </div>
    </AppLayout>
</template>

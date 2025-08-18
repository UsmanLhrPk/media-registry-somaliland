<script setup>
import { useForm } from '@inertiajs/vue3';

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
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Owner</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Full Name</label>
                <input v-model="form.full_name" type="text" placeholder="Full Name" required
                    class="w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Job Title</label>
                <input v-model="form.job_title" type="text" placeholder="Job Title"
                    class="w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">ID Number</label>
                <input v-model="form.id_number" type="text" placeholder="ID Number" required
                    class="w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">ID Type</label>
                <select v-model="form.id_type" required class="w-full border rounded px-3 py-2">
                    <option value="id_card">ID Card</option>
                    <option value="passport">Passport</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Address</label>
                <textarea v-model="form.address" placeholder="Address" required
                    class="w-full border rounded px-3 py-2 h-20"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input v-model="form.phone" type="text" placeholder="Phone" required
                    class="w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input v-model="form.email" type="email" placeholder="Email" required
                    class="w-full border rounded px-3 py-2" />
            </div>

            <div class="flex space-x-4">
                <button type="submit" :disabled="form.processing"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50">
                    {{ form.processing ? 'Updating...' : 'Update Owner' }}
                </button>

                <a href="/owners" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</template>
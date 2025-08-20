<script setup>
import { Head, useForm } from '@inertiajs/vue3';

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
    <div class="p-6">

        <Head :title="`Edit Admin - ${admin.name}`" />

        <h1 class="text-2xl font-bold mb-6">Edit Admin - {{ admin.name }}</h1>

        <div class="max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Personal Information -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800">Personal Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Full Name *</label>
                            <input v-model="form.name" type="text" placeholder="Enter full name" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Email *</label>
                            <input v-model="form.email" type="email" placeholder="Enter email address" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Phone</label>
                            <input v-model="form.phone" type="text" placeholder="Enter phone number"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">{{ form.errors.phone }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Department</label>
                            <input v-model="form.department" type="text" placeholder="e.g., IT, HR, Finance"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <div v-if="form.errors.department" class="text-red-600 text-sm mt-1">{{
                                form.errors.department }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1 text-gray-700">Designation</label>
                            <input v-model="form.designation" type="text"
                                placeholder="e.g., Admin Officer, System Administrator"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <div v-if="form.errors.designation" class="text-red-600 text-sm mt-1">{{
                                form.errors.designation }}</div>
                        </div>
                    </div>
                </div>

                <!-- Password Change (Optional) -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800">Change Password (Optional)</h2>
                    <p class="text-sm text-gray-600 mb-4">Leave blank if you don't want to change the password</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">New Password</label>
                            <input v-model="form.password" type="password"
                                placeholder="Enter new password (min 8 characters)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password
                                }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Confirm New Password</label>
                            <input v-model="form.password_confirmation" type="password"
                                placeholder="Confirm new password"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" :disabled="form.processing"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                        {{ form.processing ? 'Updating...' : 'Update Admin' }}
                    </button>

                    <a href="/admins" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 font-medium">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>
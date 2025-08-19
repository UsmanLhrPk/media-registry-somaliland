<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    admin: Object
});
</script>

<template>
    <div class="p-6">

        <Head :title="`Admin Details - ${admin.name}`" />

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Admin Details</h1>

            <div class="flex space-x-2">
                <Link :href="`/admins/${admin.id}/edit`"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 font-medium">
                Edit Admin
                </Link>

                <Link href="/admins" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 font-medium">
                Back to List
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">
                    Personal Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                        <p class="text-lg text-gray-900 font-medium">{{ admin.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
                        <p class="text-lg text-gray-900">
                            <a :href="`mailto:${admin.email}`"
                                class="text-blue-600 hover:text-blue-800 hover:underline">
                                {{ admin.email }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Phone Number</label>
                        <p class="text-lg text-gray-900">
                            <span v-if="admin.phone">
                                <a :href="`tel:${admin.phone}`"
                                    class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ admin.phone }}
                                </a>
                            </span>
                            <span v-else class="text-gray-500 italic">Not provided</span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ admin.role.charAt(0).toUpperCase() + admin.role.slice(1) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">
                    Professional Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Department</label>
                        <p class="text-lg text-gray-900">
                            {{ admin.department || 'Not specified' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Designation</label>
                        <p class="text-lg text-gray-900">
                            {{ admin.designation || 'Not specified' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Account Status</label>
                        <div class="flex items-center space-x-2">
                            <span v-if="admin.email_verified_at"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Verified
                            </span>
                            <span v-else
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Pending Verification
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="mt-6 bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">
                Account Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Account Created</label>
                    <p class="text-lg text-gray-900">
                        {{ new Date(admin.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        }) }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ new Date(admin.created_at).toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Last Updated</label>
                    <p class="text-lg text-gray-900">
                        {{ new Date(admin.updated_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        }) }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ new Date(admin.updated_at).toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">User ID</label>
                    <p class="text-lg text-gray-900 font-mono">
                        #{{ admin.id }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Activity & Statistics (Future Enhancement) -->
        <div class="mt-6 bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">
                Activity Overview
            </h2>
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <p class="text-gray-600">
                    Activity tracking and statistics will be displayed here in future updates.
                </p>
            </div>
        </div>
    </div>
</template>
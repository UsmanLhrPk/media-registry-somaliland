<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    admins: Array,
});

const deleteAdmin = (adminId, adminName) => {
    if (confirm(`Are you sure you want to delete ${adminName}?`)) {
        router.delete(route('admins.destroy', adminId));
    }
};
</script>

<template>
    <div>

        <Head title="Manage Admins" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Manage Admins</h1>
                            <Link :href="route('admins.create')"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                            Create New Admin
                            </Link>
                        </div>

                        <!-- Admins Table -->
                        <div v-if="props.admins.length > 0" class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th
                                            class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th
                                            class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th
                                            class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th
                                            class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-300">
                                    <tr v-for="admin in props.admins" :key="admin.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ admin.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ admin.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ admin.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                                Admin
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <Link :href="route('admins.show', admin.id)"
                                                class="text-blue-600 hover:text-blue-900 transition duration-200">
                                            View
                                            </Link>
                                            <Link :href="route('admins.edit', admin.id)"
                                                class="text-green-600 hover:text-green-900 transition duration-200">
                                            Edit
                                            </Link>
                                            <button @click="deleteAdmin(admin.id, admin.name)"
                                                class="text-red-600 hover:text-red-900 transition duration-200">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- No Admins Message -->
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500 mb-4">No admins found.</p>
                            <Link :href="route('admins.create')"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                            Create First Admin
                            </Link>
                        </div>

                        <!-- Back to Dashboard -->
                        <div class="mt-6">
                            <Link :href="route('dashboard')"
                                class="text-gray-600 hover:text-gray-900 transition duration-200">
                            ← Back to Dashboard
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
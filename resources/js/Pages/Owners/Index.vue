<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

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
    <div class="p-6">

        <Head title="Owners" />

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Owners List</h1>

            <Link href="/owners/create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Create Owner
            </Link>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Full Name</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Phone</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="owner in owners" :key="owner.id" class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2">{{ owner.id }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ owner.full_name }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ owner.email }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ owner.phone }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <div class="flex space-x-2">
                                <!-- View/Show -->
                                <Link :href="`/owners/${owner.id}`"
                                    class="text-green-600 hover:text-green-800 font-medium">
                                View
                                </Link>

                                <!-- Edit -->
                                <Link :href="`/owners/${owner.id}/edit`"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                Edit
                                </Link>

                                <!-- Delete -->
                                <button @click="deleteOwner(owner.id)"
                                    class="text-red-600 hover:text-red-800 font-medium">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty state -->
            <div v-if="owners.length === 0" class="text-center py-8 text-gray-500">
                No owners found.
                <Link href="/owners/create" class="text-blue-600 hover:text-blue-800">
                Create the first one!
                </Link>
            </div>
        </div>
    </div>
</template>
<script setup>
import { Head, router } from '@inertiajs/vue3';


const props = defineProps({
  auth: Object,
});

// logout function
const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <div>

    <Head title="Dashboard" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

            <!-- User Info Card -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <h2 class="text-lg font-semibold mb-2">Welcome, {{ props.auth?.user?.name }}!</h2>
              <p class="text-gray-600">Email: {{ props.auth?.user?.email }}</p>

              <!-- Logout Button -->
              <button @click="logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow">
                Logout
              </button>

              <!-- Role Display -->
              <div class="mt-4">
                <div v-if="props.auth?.user?.role === 'owner'"
                  class="bg-green-100 text-green-800 px-3 py-1 rounded-full inline-block">
                  <strong> Owner</strong>
                </div>

                <div v-else-if="props.auth?.user?.role === 'admin'"
                  class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full inline-block">
                  <strong> Admin</strong>
                </div>

                <div v-else class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full inline-block">
                  <strong> User</strong>
                </div>
              </div>

              <!-- Debug Info -->
              <div class="mt-4 p-2 bg-yellow-50 rounded text-xs">
                <p><strong>Debug Info:</strong></p>
                <p>Role: {{ props.auth?.user?.role }}</p>
                <p>All Roles: {{ props.auth?.user?.roles?.join(', ') }}</p>
              </div>
            </div>

            <!-- Owner-specific content -->
            <div v-if="props.auth?.user?.role === 'owner'" class="bg-blue-50 p-4 rounded-lg">
              <h3 class="text-lg font-semibold mb-2">Owner Dashboard</h3>
              <p>Welcome to your owner panel! You can manage your business from here.</p>

              <!-- Owner details if available -->
              <div v-if="props.auth?.user?.owner" class="mt-3">
                <p><strong>Job Title:</strong> {{ props.auth.user.owner.job_title }}</p>
                <p><strong>Phone:</strong> {{ props.auth.user.owner.phone }}</p>
              </div>
            </div>

            <!-- Admin-specific content -->
            <div v-else-if="props.auth?.user?.role === 'admin'" class="bg-purple-50 p-4 rounded-lg">
              <h3 class="text-lg font-semibold mb-2">Admin Dashboard</h3>
              <p>Welcome to the admin panel! You have full system access.</p>
            </div>

            <!-- Regular user content -->
            <div v-else class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-semibold mb-2">User Dashboard</h3>
              <p>Welcome! You have basic user access.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
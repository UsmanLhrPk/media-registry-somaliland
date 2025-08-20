<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import AdminsIndex from '@/Pages/Admin/Index.vue'
import OwnersIndex from '@/Pages/Owners/Index.vue'


const props = defineProps({
  auth: Object,
  admins: Array,
  owners: Array,
});

// logout function
const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <AppLayout :auth="props.auth">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <Head title="Dashboard" />

    <div class="max-w-5xl mx-auto px-6 py-8"> 
      <div class="bg-white rounded-2xl shadow-lg p-8 space-y-8">

      <div class="bg-gray-50 p-6 rounded-xl shadow-inner">
  <!-- Top Row: Name + Logout -->
  <div class="flex justify-between items-center mb-2">
    <h2 class="text-xl font-semibold">
      Welcome, {{ props.auth?.user?.name }}!
    </h2>
    <button
      @click="logout"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
    >
      Logout
    </button>
  </div>

  <!-- Email under the row -->
  <p class="text-gray-600">Email: {{ props.auth?.user?.email }}</p>
  <div class="mt-5">
            <span
              v-if="props.auth?.user?.role === 'superadmin'"
              class="bg-red-100 text-red-800 px-4 py-1.5 rounded-full text-sm font-medium"
            >
              Super Admin
            </span>

            <span
              v-else-if="props.auth?.user?.role === 'admin'"
              class="bg-blue-100 text-blue-800 px-4 py-1.5 rounded-full text-sm font-medium"
            >
              Admin
            </span>

            <span
              v-else-if="props.auth?.user?.role === 'owner'"
              class="bg-green-100 text-green-800 px-4 py-1.5 rounded-full text-sm font-medium"
            >
              Owner
            </span>

            <span
              v-else
              class="bg-gray-100 text-gray-800 px-4 py-1.5 rounded-full text-sm font-medium"
            >
              User
            </span>
          </div>
</div>

      <!-- Superadmin-specific content -->
        <div
          v-if="props.auth?.user?.role === 'superadmin'"
        >
          <h3 class="text-lg font-semibold mb-2">Super Admin Dashboard</h3>
          <p>You can manage system-wide settings, users, and configurations.</p>
          <AdminsIndex :admins="props.admins" />
          <OwnersIndex :owners="props.owners" />
        </div>

        <!-- Admin-specific content -->
        <div
          v-else-if="props.auth?.user?.role === 'admin'"
          class="bg-purple-50 p-4 rounded-lg"
        >
          <h3 class="text-lg font-semibold mb-2">Admin Dashboard</h3>
          <p>Welcome to the admin panel! You have full system access (except superadmin privileges).</p>
          <OwnersIndex :owners="props.owners" />
        </div>

        <!-- Owner-specific content -->
        <div
          v-else-if="props.auth?.user?.role === 'owner'"
          class="bg-blue-50 p-4 rounded-lg"
        >
          <h3 class="text-lg font-semibold mb-2">Owner Dashboard</h3>
          <p>Welcome to your owner panel! You can manage your business here.</p>

          <div v-if="props.auth?.user?.owner" class="mt-3 space-y-1">
            <p><strong>Job Title:</strong> {{ props.auth.user.owner.job_title }}</p>
            <p><strong>Phone:</strong> {{ props.auth.user.owner.phone }}</p>
          </div>
        </div>

        <!-- Regular user content -->
        <div v-else class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-lg font-semibold mb-2">User Dashboard</h3>
          <p>Welcome! You have basic user access.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
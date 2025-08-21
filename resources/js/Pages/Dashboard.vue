<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import BusinessForm from './Auth/BusinessForm.vue';
import ApplicationForm from './Auth/ApplicationForm.vue';
import AdminsIndex from '@/Pages/Admin/Index.vue'
import OwnersIndex from '@/Pages/Owners/Index.vue'


const props = defineProps({
  auth: Object,
  admins: Array,
  owners: Array,
});

// Owner-specific modal states
const showBusinessModal = ref(false);
const showApplicationModal = ref(false);
const ownerBusinesses = ref([]);
const loadingBusinesses = ref(false);

// Admin-specific states
const showApplicationsModal = ref(false);
const applications = ref([
  {
    id: 1,
    business: "Tech News Daily",
    owner: "John Doe",
    type: "Online News",
    reason: "New License",
    status: "Pending",
    comment: "",
  },
  {
    id: 2,
    business: "Radio Wave FM",
    owner: "Jane Smith",
    type: "Radio",
    reason: "Renewal",
    status: "Pending",
    comment: "",
  },
  {
    id: 3,
    business: "City Times",
    owner: "Ali Khan",
    type: "Newspaper",
    reason: "New License",
    status: "Approved",
    comment: "",
  },
]);

const selectedApp = ref(null);
const adminComment = ref("");

const closeBusinessModal = () => (showBusinessModal.value = false);
const closeApplicationModal = () => (showApplicationModal.value = false);

const handleBusinessSubmit = async (formData) => {
  try {
    await router.post(route('media-entities.store'), formData, {
      onSuccess: () => {
        console.log("Business submitted successfully");
        closeBusinessModal();
        // Reload businesses after successful submission
        loadOwnerBusinesses();
      },
      onError: (errors) => {
        console.error("Business submission failed:", errors);
      }
    });
  } catch (error) {
    console.error("Error submitting business:", error);
  }
};

const handleApplicationSubmit = () => {
  console.log("Application submitted");
  closeApplicationModal();
};

// Admin functions
const openApplicationsModal = () => (showApplicationsModal.value = true);
const closeApplicationsModal = () => {
  showApplicationsModal.value = false;
  selectedApp.value = null;
  adminComment.value = "";
};

const openApplication = (app) => {
  selectedApp.value = { ...app };
  adminComment.value = app.comment || "";
};

const closeApplication = () => {
  selectedApp.value = null;
  adminComment.value = "";
};

const approve = () => {
  const index = applications.value.findIndex((a) => a.id === selectedApp.value.id);
  applications.value[index].status = "Approved";
  applications.value[index].comment = adminComment.value;
  closeApplication();
};

const dismiss = () => {
  const index = applications.value.findIndex((a) => a.id === selectedApp.value.id);
  applications.value[index].status = "Dismissed";
  applications.value[index].comment = adminComment.value;
  closeApplication();
};

// logout function
const logout = () => {
  router.post(route('logout'));
};

// Load owner's businesses
const loadOwnerBusinesses = async () => {
  if (props.auth?.user?.role !== 'owner') return;

  loadingBusinesses.value = true;
  try {
    const response = await fetch(route('owner.media-entities'), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (response.ok) {
      const data = await response.json();
      ownerBusinesses.value = data;
    }
  } catch (error) {
    console.error('Error loading businesses:', error);
  } finally {
    loadingBusinesses.value = false;
  }
};

// Load businesses when component mounts
onMounted(() => {
  if (props.auth?.user?.role === 'owner') {
    loadOwnerBusinesses();
  }
});
</script>

<template>
  <AppLayout :auth="props.auth">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <Head title="Dashboard" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Common Header for all roles -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

            <!-- User Info Card -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <h2 class="text-lg font-semibold mb-2">Welcome, {{ props.auth?.user?.name }}!</h2>
              <p class="text-gray-600">Email: {{ props.auth?.user?.email }}</p>

              <!-- Logout Button -->
              <button @click="logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow mt-4">
                Logout
              </button>

              <!-- Role Display -->
              <div class="mt-4">
                <div v-if="props.auth?.user?.role === 'owner'"
                  class="bg-green-100 text-green-800 px-3 py-1 rounded-full inline-block">
                  <strong>Owner</strong>
                </div>
                <div v-else-if="props.auth?.user?.role === 'admin'"
                  class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full inline-block">
                  <strong>Admin</strong>
                </div>
                <div v-else-if="props.auth?.user?.role === 'super_admin' || props.auth?.user?.role === 'superadmin'"
                  class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full inline-block">
                  <strong>Super Admin</strong>
                </div>
                <div v-else class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full inline-block">
                  <strong>User</strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Owner Dashboard - Full UI with 3 columns -->
    <div v-if="props.auth?.user?.role === 'owner'" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- 3 Column Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Column 1 - Welcome & Actions -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Welcome Back!
          </h3>
          <p class="text-gray-600 mb-6">
            Use the quick actions below to manage your businesses and apply for licenses.
          </p>
          <div class="space-y-4">
            <button @click="showBusinessModal = true"
              class="w-full px-6 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
              Create Business Info
            </button>
            <button @click="showApplicationModal = true"
              class="w-full px-6 py-3 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
              Apply for License
            </button>
          </div>

          <!-- Owner details if available -->
          <div v-if="props.auth?.user?.owner" class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="font-semibold text-gray-800 mb-2">Your Info</h4>
            <p class="text-sm text-gray-600"><strong>Job Title:</strong> {{ props.auth.user.owner.job_title }}</p>
            <p class="text-sm text-gray-600"><strong>Phone:</strong> {{ props.auth.user.owner.phone }}</p>
          </div>
        </div>

        <!-- Column 2 - Applications -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Your Applications
          </h3>
          <ul class="divide-y divide-gray-200">
            <li class="py-3 flex justify-between items-center">
              <span>Application #1</span>
              <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
            </li>
            <li class="py-3 flex justify-between items-center">
              <span>Application #2</span>
              <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
            </li>
            <li class="py-3 flex justify-between items-center">
              <span>Application #3</span>
              <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Rejected</span>
            </li>
          </ul>
        </div>

        <!-- Column 3 - Businesses -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Your Businesses
          </h3>

          <div v-if="loadingBusinesses" class="text-center py-4">
            <p class="text-gray-500">Loading...</p>
          </div>

          <div v-else-if="ownerBusinesses.length === 0" class="text-center py-4">
            <p class="text-gray-500">No businesses created yet</p>
            <p class="text-sm text-gray-400 mt-2">Click "Create Business Info" to add your first business</p>
          </div>

          <ul v-else class="divide-y divide-gray-200">
            <li v-for="business in ownerBusinesses" :key="business.id" class="py-3">
              <div class="font-medium">{{ business.business_name }}</div>
              <div class="text-sm text-gray-500">{{ business.work_type }}</div>
              <div class="text-xs text-gray-400 mt-1 flex items-center justify-between">
                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="{
                  'bg-yellow-100 text-yellow-700': business.status === 'Pending',
                  'bg-green-100 text-green-700': business.status === 'Active',
                  'bg-gray-100 text-gray-700': business.status === 'Unknown',
                }">
                  {{ business.status }}
                </span>
                <span class="text-xs">{{ business.created_at }}</span>
              </div>
            </li>
          </ul>
        </div>

      </div>
    </div>

    <!-- Admin Dashboard -->
    <div v-else-if="props.auth?.user?.role === 'admin'" class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Admin Header -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Admin Dashboard</h3>
        <p class="text-gray-600">Welcome to the admin panel! Manage all applications from here.</p>
      </div>

      <!-- Applications Management -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">
          All Applications
        </h3>

        <!-- Applications Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Business</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Owner</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Reason</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="app in applications" :key="app.id">
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.id }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.business }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.owner }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.type }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.reason }}</td>
                <td class="px-6 py-4 text-sm">
                  <span class="px-3 py-1 rounded-full text-xs font-medium" :class="{
                    'bg-yellow-100 text-yellow-700': app.status === 'Pending',
                    'bg-green-100 text-green-700': app.status === 'Approved',
                    'bg-red-100 text-red-700': app.status === 'Dismissed',
                  }">
                    {{ app.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right text-sm space-x-2">
                  <button @click="openApplication(app)"
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                    View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Super Admin Dashboard -->
    <div v-else-if="props.auth?.user?.role === 'super_admin' || props.auth?.user?.role === 'superadmin'"
      class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Super Admin Header -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Super Admin Dashboard</h3>
        <p class="text-gray-600">Welcome to the super admin panel! You have full system control and can manage all
          applications.</p>
      </div>

      <!-- Quick Actions for Super Admin -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-red-50 p-4 rounded-lg">
          <h4 class="font-semibold text-red-800 mb-2">User Management</h4>
          <p class="text-sm text-red-600">Manage all system users</p>
          <button class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Manage Users
          </button>
        </div>

        <div class="bg-blue-50 p-4 rounded-lg">
          <h4 class="font-semibold text-blue-800 mb-2">Admin Management</h4>
          <p class="text-sm text-blue-600">Manage admin accounts</p>
          <button class="mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Manage Admins
          </button>
        </div>

        <div class="bg-yellow-50 p-4 rounded-lg">
          <h4 class="font-semibold text-yellow-800 mb-2">System Settings</h4>
          <p class="text-sm text-yellow-600">Configure system parameters</p>
          <button class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
            Settings
          </button>
        </div>

        <div class="bg-indigo-50 p-4 rounded-lg">
          <h4 class="font-semibold text-indigo-800 mb-2">System Logs</h4>
          <p class="text-sm text-indigo-600">View system activity</p>
          <button class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            View Logs
          </button>
        </div>
      </div>

      <!-- Applications Management for Super Admin -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">
          All Applications (Super Admin View)
        </h3>

        <!-- Applications Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Business</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Owner</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Reason</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="app in applications" :key="app.id">
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.id }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.business }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.owner }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.type }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ app.reason }}</td>
                <td class="px-6 py-4 text-sm">
                  <span class="px-3 py-1 rounded-full text-xs font-medium" :class="{
                    'bg-yellow-100 text-yellow-700': app.status === 'Pending',
                    'bg-green-100 text-green-700': app.status === 'Approved',
                    'bg-red-100 text-red-700': app.status === 'Dismissed',
                  }">
                    {{ app.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right text-sm space-x-2">
                  <button @click="openApplication(app)"
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                    View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Regular User Dashboard -->
    <div v-else class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">User Dashboard</h3>
        <p class="text-gray-600">Welcome! You have basic user access.</p>

        <div class="mt-6 bg-gray-50 p-4 rounded-lg">
          <p class="text-sm text-gray-600">Contact your administrator for additional permissions.</p>
        </div>
      </div>
    </div>

    <!-- Application Detail Modal - For Admin and Super Admin -->
    <div
      v-if="selectedApp && (props.auth?.user?.role === 'admin' || props.auth?.user?.role === 'super_admin' || props.auth?.user?.role === 'superadmin')"
      class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
      <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
          Application #{{ selectedApp.id }}
        </h3>
        <p class="mb-2"><strong>Business:</strong> {{ selectedApp.business }}</p>
        <p class="mb-2"><strong>Owner:</strong> {{ selectedApp.owner }}</p>
        <p class="mb-2"><strong>Type:</strong> {{ selectedApp.type }}</p>
        <p class="mb-2"><strong>Reason:</strong> {{ selectedApp.reason }}</p>
        <p class="mb-4"><strong>Status:</strong>
          <span class="px-2 py-1 rounded-full text-xs font-medium ml-2" :class="{
            'bg-yellow-100 text-yellow-700': selectedApp.status === 'Pending',
            'bg-green-100 text-green-700': selectedApp.status === 'Approved',
            'bg-red-100 text-red-700': selectedApp.status === 'Dismissed',
          }">
            {{ selectedApp.status }}
          </span>
        </p>

        <!-- Comment box -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Admin Comment</label>
          <textarea v-model="adminComment"
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" rows="3"
            placeholder="Write a comment here..."></textarea>
        </div>

        <div class="flex justify-end space-x-2">
          <button @click="closeApplication" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Close
          </button>
          <button @click="approve" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Approve
          </button>
          <button @click="dismiss" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Dismiss
          </button>
        </div>
      </div>
    </div>

    <!-- Business Modal - Only for Owner -->
    <div v-if="showBusinessModal && props.auth?.user?.role === 'owner'"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Business Info</h3>
          <button @click="closeBusinessModal" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <!-- BusinessForm component would go here -->
        <BusinessForm :submit="handleBusinessSubmit" mode="standalone" />
      </div>
    </div>

    <!-- Application Modal - Only for Owner -->
    <div v-if="showApplicationModal && props.auth?.user?.role === 'owner'"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">License Application</h3>
          <button @click="closeApplicationModal" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <!-- ApplicationForm component would go here -->
        <ApplicationForm :submit="handleApplicationSubmit" :processing="false" mode="standalone" />
      </div>
    </div>
  </AppLayout>
</template>
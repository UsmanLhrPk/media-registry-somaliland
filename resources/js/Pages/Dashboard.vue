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

// License data
const ownerLicenses = ref([]);
const loadingLicenses = ref(false);

// Admin-specific states
const adminLicenses = ref([]);
const loadingAdminLicenses = ref(false);
const selectedLicense = ref(null);
const adminComment = ref("");

const closeBusinessModal = () => (showBusinessModal.value = false);
const closeApplicationModal = () => (showApplicationModal.value = false);

const handleBusinessSubmit = async (formData) => {
  try {
    await router.post(route('media-entities.store'), formData, {
      onSuccess: () => {
        console.log("Business submitted successfully");
        closeBusinessModal();
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

const handleApplicationSubmit = async (formData) => {
  try {
    await router.post(route('licenses.store'), formData, {
      onSuccess: () => {
        console.log("License application submitted successfully");
        closeApplicationModal();
        loadOwnerLicenses();
      },
      onError: (errors) => {
        console.error("License application failed:", errors);
      }
    });
  } catch (error) {
    console.error("Error submitting license application:", error);
  }
};

// Admin functions
const openLicense = (license) => {
  selectedLicense.value = { ...license };
  adminComment.value = "";
};

const closeLicense = () => {
  selectedLicense.value = null;
  adminComment.value = "";
};

const approveLicense = async () => {
  if (!selectedLicense.value) return;

  try {
    await router.put(route('licenses.update-status', selectedLicense.value.id), {
      status: 'active',
      admin_comment: adminComment.value,
    }, {
      onSuccess: () => {
        console.log("License approved successfully");
        closeLicense();
        loadAdminLicenses();
      },
      onError: (errors) => {
        console.error("License approval failed:", errors);
      }
    });
  } catch (error) {
    console.error("Error approving license:", error);
  }
};

const rejectLicense = async () => {
  if (!selectedLicense.value) return;

  try {
    await router.put(route('licenses.update-status', selectedLicense.value.id), {
      status: 'revoked',
      admin_comment: adminComment.value,
    }, {
      onSuccess: () => {
        console.log("License rejected successfully");
        closeLicense();
        loadAdminLicenses();
      },
      onError: (errors) => {
        console.error("License rejection failed:", errors);
      }
    });
  } catch (error) {
    console.error("Error rejecting license:", error);
  }
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

// Load owner's licenses
const loadOwnerLicenses = async () => {
  if (props.auth?.user?.role !== 'owner') return;

  loadingLicenses.value = true;
  try {
    const response = await fetch(route('licenses.mine'), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (response.ok) {
      const data = await response.json();
      ownerLicenses.value = data;
    }
  } catch (error) {
    console.error('Error loading licenses:', error);
  } finally {
    loadingLicenses.value = false;
  }
};

// Load all licenses for admin - NOW USING WORKING ROUTE!
const loadAdminLicenses = async () => {
  const userRole = props.auth?.user?.role;
  console.log("user role", userRole)

  if (!['admin', 'super_admin', 'superadmin'].includes(userRole)) return;

  loadingAdminLicenses.value = true;
  try {
    console.log('🚀 Loading admin licenses...');

    // Use the working route!
    const response = await fetch(route('licenses.index'), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (response.ok) {
      const data = await response.json();
      adminLicenses.value = data;
      console.log('✅ Admin licenses loaded:', data.length, 'applications');
    } else {
      console.error('❌ Failed to load admin licenses. Status:', response.status);
    }
  } catch (error) {
    console.error('💥 Error loading admin licenses:', error);
  } finally {
    loadingAdminLicenses.value = false;
  }
};

// Load data when component mounts
onMounted(() => {
  const userRole = props.auth?.user?.role;
  console.log('🚀 Dashboard loading for role:', userRole);

  if (userRole === 'owner') {
    loadOwnerBusinesses();
    loadOwnerLicenses();
  } else if (['admin', 'super_admin', 'superadmin'].includes(userRole)) {
    loadAdminLicenses();
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

    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Common Header for all roles -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

            <!-- User Info Card -->
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
          </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Owner Dashboard -->
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

        <!-- Column 2 - License Applications -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Your License Applications
          </h3>

          <div v-if="loadingLicenses" class="text-center py-4">
            <p class="text-gray-500">Loading...</p>
          </div>

          <div v-else-if="ownerLicenses.length === 0" class="text-center py-4">
            <p class="text-gray-500">No license applications yet</p>
            <p class="text-sm text-gray-400 mt-2">Click "Apply for License" to submit your first application</p>
          </div>

          <ul v-else class="divide-y divide-gray-200">
            <li v-for="license in ownerLicenses" :key="license.id" class="py-3">
              <div class="font-medium">{{ license.business_name }}</div>
              <div class="text-sm text-gray-500">{{ license.license_number }}</div>
              <div class="text-xs text-gray-400 mt-1 flex items-center justify-between">
                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="{
                  'bg-yellow-100 text-yellow-700': license.status === 'pending',
                  'bg-green-100 text-green-700': license.status === 'active',
                  'bg-red-100 text-red-700': license.status === 'revoked' || license.status === 'expired',
                }">
                  {{ license.status }}
                </span>
                <span class="text-xs">{{ license.issue_date }}</span>
              </div>
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
        <p class="text-gray-600">Manage all license applications from here.</p>
         <OwnersIndex :owners="props.owners" />

        <!-- Stats Card -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-blue-600">Total Applications</p>
            <p class="text-2xl font-bold text-blue-800">{{ adminLicenses.length }}</p>
          </div>
          <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-yellow-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-800">{{adminLicenses.filter(l => l.status === 'pending').length}}
            </p>
          </div>
          <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-green-600">Approved</p>
            <p class="text-2xl font-bold text-green-800">{{adminLicenses.filter(l => l.status === 'active').length}}
            </p>
          </div>
        </div>
      </div>

      <!-- License Applications Management -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">
          All License Applications
        </h3>

        <div v-if="loadingAdminLicenses" class="text-center py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="text-gray-500 mt-4">Loading applications...</p>
        </div>

        <div v-else-if="adminLicenses.length === 0" class="text-center py-8">
          <div class="text-gray-400 text-6xl mb-4">📄</div>
          <p class="text-gray-500 text-lg">No license applications found</p>
          <p class="text-gray-400 text-sm mt-2">Applications will appear here when owners submit them</p>
        </div>

        <!-- Applications Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">License #</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Business</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Owner</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">License Type</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Applied Date</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="license in adminLicenses" :key="license.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-mono text-gray-800">{{ license.license_number }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.business_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.owner_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.work_type }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.license_type }}</td>
                <td class="px-6 py-4 text-sm">
                  <span class="px-3 py-1 rounded-full text-xs font-medium" :class="{
                    'bg-yellow-100 text-yellow-700': license.status === 'pending',
                    'bg-green-100 text-green-700': license.status === 'active',
                    'bg-red-100 text-red-700': license.status === 'revoked',
                  }">
                    {{ license.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.created_at }}</td>
                <td class="px-6 py-4 text-right text-sm space-x-2">
                  <button @click="openLicense(license)"
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    Review
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
        <p class="text-gray-600">Full system control and license application management.</p>
         <AdminsIndex :admins="props.admins" />
          <OwnersIndex :owners="props.owners" />

        <!-- Enhanced Stats Card -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-blue-600">Total Applications</p>
            <p class="text-2xl font-bold text-blue-800">{{ adminLicenses.length }}</p>
          </div>
          <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-yellow-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-800">{{adminLicenses.filter(l => l.status === 'pending').length}}
            </p>
          </div>
          <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-green-600">Approved</p>
            <p class="text-2xl font-bold text-green-800">{{adminLicenses.filter(l => l.status === 'active').length}}
            </p>
          </div>
          <div class="bg-red-50 p-4 rounded-lg">
            <p class="text-sm text-red-600">Rejected</p>
            <p class="text-2xl font-bold text-red-800">{{adminLicenses.filter(l => l.status === 'revoked').length}}
            </p>
          </div>
        </div>
      </div> 

      <!-- License Applications Management for Super Admin -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">
          All License Applications (Super Admin View)
        </h3>

        <div v-if="loadingAdminLicenses" class="text-center py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
          <p class="text-gray-500 mt-4">Loading applications...</p>
        </div>

        <div v-else-if="adminLicenses.length === 0" class="text-center py-8">
          <div class="text-gray-400 text-6xl mb-4">📄</div>
          <p class="text-gray-500 text-lg">No license applications found</p>
          <p class="text-gray-400 text-sm mt-2">Applications will appear here when owners submit them</p>
        </div>

        <!-- Applications Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">License #</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Business</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Owner</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">License Type</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Applied Date</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="license in adminLicenses" :key="license.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-mono text-gray-800">{{ license.license_number }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.business_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.owner_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.work_type }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.license_type }}</td>
                <td class="px-6 py-4 text-sm">
                  <span class="px-3 py-1 rounded-full text-xs font-medium" :class="{
                    'bg-yellow-100 text-yellow-700': license.status === 'pending',
                    'bg-green-100 text-green-700': license.status === 'active',
                    'bg-red-100 text-red-700': license.status === 'revoked',
                  }">
                    {{ license.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ license.created_at }}</td>
                <td class="px-6 py-4 text-right text-sm space-x-2">
                  <button @click="openLicense(license)"
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    Review
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

    <!-- License Review Modal - For Admin and Super Admin -->
    <div
      v-if="selectedLicense && (props.auth?.user?.role === 'admin' || props.auth?.user?.role === 'super_admin' || props.auth?.user?.role === 'superadmin')"
      class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
      <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
          License Application Review
        </h3>

        <!-- License Details -->
        <div class="space-y-4 mb-6">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm font-semibold text-gray-700">License Number:</p>
              <p class="text-sm text-gray-800">{{ selectedLicense.license_number }}</p>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700">Status:</p>
              <span class="px-2 py-1 rounded-full text-xs font-medium" :class="{
                'bg-yellow-100 text-yellow-700': selectedLicense.status === 'pending',
                'bg-green-100 text-green-700': selectedLicense.status === 'active',
                'bg-red-100 text-red-700': selectedLicense.status === 'revoked',
              }">
                {{ selectedLicense.status }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm font-semibold text-gray-700">Business Name:</p>
              <p class="text-sm text-gray-800">{{ selectedLicense.business_name }}</p>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700">Work Type:</p>
              <p class="text-sm text-gray-800">{{ selectedLicense.work_type }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm font-semibold text-gray-700">License Type:</p>
              <p class="text-sm text-gray-800">{{ selectedLicense.license_type }}</p>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700">Applied Date:</p>
              <p class="text-sm text-gray-800">{{ selectedLicense.created_at }}</p>
            </div>
          </div>

          <!-- Owner Information -->
          <div class="border-t pt-4">
            <h4 class="font-semibold text-gray-800 mb-2">Owner Information</h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm font-semibold text-gray-700">Owner Name:</p>
                <p class="text-sm text-gray-800">{{ selectedLicense.owner_name }}</p>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-700">Owner Email:</p>
                <p class="text-sm text-gray-800">{{ selectedLicense.owner_email }}</p>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-2">
              <div>
                <p class="text-sm font-semibold text-gray-700">Owner Phone:</p>
                <p class="text-sm text-gray-800">{{ selectedLicense.owner_phone }}</p>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-700">Business Phone:</p>
                <p class="text-sm text-gray-800">{{ selectedLicense.business_phone }}</p>
              </div>
            </div>
          </div>

          <!-- Business Information -->
          <div class="border-t pt-4">
            <h4 class="font-semibold text-gray-800 mb-2">Business Information</h4>
            <div>
              <p class="text-sm font-semibold text-gray-700">Office Location:</p>
              <p class="text-sm text-gray-800">{{ selectedLicense.office_location }}</p>
            </div>
            <div class="mt-2">
              <p class="text-sm font-semibold text-gray-700">Business Email:</p>
              <p class="text-sm text-gray-800">{{ selectedLicense.business_email }}</p>
            </div>
          </div>
        </div>

        <!-- Admin Comment -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Admin Comments</label>
          <textarea v-model="adminComment"
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" rows="3"
            placeholder="Add your review comments here..."></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
          <button @click="closeLicense" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Close
          </button>
          <button v-if="selectedLicense.status === 'pending'" @click="rejectLicense"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Reject
          </button>
          <button v-if="selectedLicense.status === 'pending'" @click="approveLicense"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Approve
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
        <ApplicationForm :submit="handleApplicationSubmit" :processing="false" mode="standalone" />
      </div>
    </div>
  </AppLayout>
</template>
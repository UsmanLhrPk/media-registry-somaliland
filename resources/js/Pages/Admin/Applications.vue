<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

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

// Track currently opened application
const selectedApp = ref(null);
const adminComment = ref("");

// Open details
const openApplication = (app) => {
  selectedApp.value = { ...app }; // clone to avoid mutating table directly
  adminComment.value = app.comment || "";
};

// Close modal
const closeApplication = () => {
  selectedApp.value = null;
  adminComment.value = "";
};

// Approve with optional comment
const approve = () => {
  const index = applications.value.findIndex((a) => a.id === selectedApp.value.id);
  applications.value[index].status = "Approved";
  applications.value[index].comment = adminComment.value;
  closeApplication();
};

// Dismiss with required comment
const dismiss = () => {
  if (!adminComment.value.trim()) {
    alert("A comment is required when dismissing an application.");
    return;
  }
  const index = applications.value.findIndex((a) => a.id === selectedApp.value.id);
  applications.value[index].status = "Dismissed";
  applications.value[index].comment = adminComment.value;
  closeApplication();
};
</script>

<template>
  <AppLayout title="Admin Panel">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Admin Panel – Manage Applications
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
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
                    <span
                      class="px-3 py-1 rounded-full text-xs font-medium"
                      :class="{
                        'bg-yellow-100 text-yellow-700': app.status === 'Pending',
                        'bg-green-100 text-green-700': app.status === 'Approved',
                        'bg-red-100 text-red-700': app.status === 'Dismissed',
                      }"
                    >
                      {{ app.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right text-sm space-x-2">
                    <button
                      @click="openApplication(app)"
                      class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                      View
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Application Detail Modal -->
    <div
      v-if="selectedApp"
      class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50"
    >
      <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
          Application #{{ selectedApp.id }}
        </h3>
        <p><strong>Business:</strong> {{ selectedApp.business }}</p>
        <p><strong>Owner:</strong> {{ selectedApp.owner }}</p>
        <p><strong>Type:</strong> {{ selectedApp.type }}</p>
        <p><strong>Reason:</strong> {{ selectedApp.reason }}</p>
        <p class="mb-4"><strong>Status:</strong> {{ selectedApp.status }}</p>

        <!-- Comment box -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Admin Comment</label>
          <textarea
            v-model="adminComment"
            class="w-full border-gray-300 rounded-md shadow-sm"
            rows="3"
            placeholder="Write a comment here..."
          ></textarea>
        </div>

        <div class="flex justify-end space-x-2">
          <button
            @click="closeApplication"
            class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600"
          >
            Close
          </button>
          <button
            @click="approve"
            class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
          >
            Approve
          </button>
          <button
            @click="dismiss"
            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
          >
            Dismiss
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

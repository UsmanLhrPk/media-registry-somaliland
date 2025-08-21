<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { ref, onMounted, computed, watch } from 'vue';

const props = defineProps({
  prevStep: Function,
  submit: Function,
  processing: {
    type: Boolean,
    default: false,
  },
  mode: {
    type: String,
    default: "wizard", // wizard | standalone
  },
  // For registration wizard
  businessData: {
    type: Object,
    default: () => ({})
  },
  // For passing data back to parent
  modelValue: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['update:modelValue']);

// Form data
const applicationForm = ref({
  license_type: props.modelValue?.license_type || 'new',
  media_entity_id: props.modelValue?.media_entity_id || '', // For standalone mode
});

// Available businesses for the owner (for standalone mode)
const ownerBusinesses = ref([]);
const loadingBusinesses = ref(false);
const errors = ref({});

// For registration wizard mode - check if business data exists
const hasBusinessData = computed(() => {
  return props.businessData && props.businessData.business_name;
});

// Watch for changes and emit to parent (for wizard mode)
watch(applicationForm, (newValue) => {
  if (props.mode === 'wizard') {
    emit('update:modelValue', newValue);
  }
}, { deep: true });

// Load owner's businesses (only for standalone mode)
const loadOwnerBusinesses = async () => {
  if (props.mode !== 'standalone') return;

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

// Form validation
const validateForm = () => {
  errors.value = {};

  // For standalone mode, require business selection
  if (props.mode === 'standalone' && !applicationForm.value.media_entity_id) {
    errors.value.media_entity_id = 'Please select a business';
  }

  // For wizard mode, check if business data exists
  if (props.mode === 'wizard' && !hasBusinessData.value) {
    errors.value.business = 'Business information is required for license application. Please go back and fill Step 2.';
  }

  if (!applicationForm.value.license_type) {
    errors.value.license_type = 'Please select license type';
  }

  return Object.keys(errors.value).length === 0;
};

// Handle form submission
const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  // For wizard mode, just emit data to parent - CreateNewUser will handle license creation
  if (props.mode === 'wizard') {
    emit('update:modelValue', applicationForm.value);
  }

  if (props.submit) {
    await props.submit(applicationForm.value);
  }
};

// Load businesses on component mount for standalone mode
onMounted(() => {
  if (props.mode === 'standalone') {
    loadOwnerBusinesses();
  }
});
</script>

<template>
  <div>
    <h2 class="text-xl font-bold mb-4">License Application</h2>

    <!-- Wizard Mode - Show business info from step 2 -->
    <div v-if="mode === 'wizard'">
      <!-- Show business info if available from step 2 -->
      <div v-if="hasBusinessData" class="mb-4 p-4 bg-green-50 rounded-lg border border-green-200">
        <h4 class="font-semibold text-green-800 mb-2">✓ Business Information Available</h4>
        <p class="text-sm text-green-700">
          <strong>Business Name:</strong> {{ businessData.business_name }}
        </p>
        <p class="text-sm text-green-700">
          <strong>Work Type:</strong>
          <span v-if="businessData.work_type === 'other'">{{ businessData.other_work_type }}</span>
          <span v-else>{{ businessData.work_type }}</span>
        </p>
        <p class="text-sm text-green-700">
          <strong>Ownership Type:</strong> {{ businessData.ownership_type }}
        </p>
        <p class="text-xs text-green-600 mt-2">
          A license application will be created for this business after registration.
        </p>
      </div>

      <!-- Warning if no business data -->
      <div v-else class="mb-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
        <h4 class="font-semibold text-yellow-800 mb-2">⚠️ No Business Information</h4>
        <p class="text-sm text-yellow-700 mb-2">
          You haven't filled business information in Step 2.
        </p>
        <p class="text-sm text-yellow-700">
          <strong>Options:</strong>
        </p>
        <ul class="text-sm text-yellow-700 ml-4 list-disc">
          <li>Go back to Step 2 and fill business information to apply for license</li>
          <li>Skip this step and register as owner only (you can add business & apply for license later)</li>
        </ul>
      </div>
    </div>

    <!-- Standalone Mode - Business Selection -->
    <div v-else class="space-y-4">
      <div>
        <InputLabel value="Select Business *" />
        <div v-if="loadingBusinesses" class="mt-1 block w-full p-2 text-gray-500">
          Loading businesses...
        </div>
        <div v-else-if="ownerBusinesses.length === 0"
          class="mt-1 block w-full p-2 text-gray-500 bg-gray-50 rounded border">
          No businesses found. Please create a business first.
        </div>
        <select v-else v-model="applicationForm.media_entity_id"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          required>
          <option value="">Select a business</option>
          <option v-for="business in ownerBusinesses" :key="business.id" :value="business.id">
            {{ business.business_name }} ({{ business.work_type }})
          </option>
        </select>
        <InputError class="mt-2" :message="errors.media_entity_id" />
      </div>
    </div>

    <!-- License Type (Only show if business data exists for wizard mode, or always for standalone) -->
    <div v-if="mode === 'standalone' || (mode === 'wizard' && hasBusinessData)" class="mt-4">
      <InputLabel value="License Type *" />
      <select v-model="applicationForm.license_type"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        required>
        <option value="new">New License</option>
        <option value="renewal">License Renewal</option>
      </select>
      <InputError class="mt-2" :message="errors.license_type" />
    </div>

    <!-- Document Attachment -->
    <div v-if="mode === 'standalone' || (mode === 'wizard' && hasBusinessData)" class="mt-4">
      <InputLabel value="Document Attachment (Optional)" />
      <TextInput type="file" accept=".pdf,.doc,.docx,.jpg,.png" class="mt-1 block w-full" disabled />
      <p class="text-xs text-gray-500 mt-1">Document upload feature coming soon</p>
    </div>

    <!-- Application Summary for wizard mode -->
    <div v-if="mode === 'wizard' && hasBusinessData" class="mt-4 p-4 bg-blue-50 rounded-lg">
      <h4 class="font-semibold text-blue-800 mb-2">License Application Summary</h4>
      <p class="text-sm text-blue-700">
        <strong>Business:</strong> {{ businessData.business_name }}
      </p>
      <p class="text-sm text-blue-700">
        <strong>License Type:</strong>
        {{ applicationForm.license_type === 'new' ? 'New License' : 'License Renewal' }}
      </p>
      <p class="text-xs text-blue-600 mt-2">
        After registration completion, a license application will be automatically created with status: "Pending"
      </p>
    </div>

    <!-- Application Info for standalone mode -->
    <div v-else-if="mode === 'standalone' && applicationForm.media_entity_id" class="mt-4 p-4 bg-blue-50 rounded-lg">
      <h4 class="font-semibold text-blue-800 mb-2">Application Details</h4>
      <div v-if="applicationForm.media_entity_id">
        <p class="text-sm text-blue-700">
          <strong>Selected Business:</strong>
          {{ownerBusinesses.find(b => b.id == applicationForm.media_entity_id)?.business_name}}
        </p>
        <p class="text-sm text-blue-700">
          <strong>Business Type:</strong>
          {{ownerBusinesses.find(b => b.id == applicationForm.media_entity_id)?.work_type}}
        </p>
        <p class="text-sm text-blue-700">
          <strong>License Type:</strong>
          {{ applicationForm.license_type === 'new' ? 'New License' : 'License Renewal' }}
        </p>
      </div>
    </div>

    <!-- Error Messages -->
    <InputError class="mt-2" :message="errors.business" />
    <InputError class="mt-2" :message="errors.license_type" />

    <!-- Standalone Mode Buttons -->
    <div v-if="mode === 'standalone'" class="flex justify-end mt-6">
      <PrimaryButton type="button" @click="handleSubmit" :class="{ 'opacity-25': processing }"
        :disabled="processing || (ownerBusinesses.length === 0 && mode === 'standalone')">
        Submit Application
      </PrimaryButton>
    </div>
  </div>
</template>
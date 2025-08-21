<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

const emit = defineEmits(["submitted"]);

const props = defineProps({
  prevStep: Function,
  nextStep: Function,
  submit: Function,
  processing: {
    type: Boolean,
    default: false,
  },
  mode: {
    type: String,
    default: "wizard", // wizard | standalone
  },
  // For wizard mode - to pass business data to parent
  modelValue: {
    type: Object,
    default: () => ({})
  }
});

// Form data - reactive ref
const businessForm = ref({
  business_name: props.modelValue?.business_name || '',
  ownership_type: props.modelValue?.ownership_type || '',
  work_type: props.modelValue?.work_type || '',
  other_work_type: props.modelValue?.other_work_type || '',
  reason: props.modelValue?.reason || '',
  office_location: props.modelValue?.office_location || '',
  phone: props.modelValue?.phone || '',
  email: props.modelValue?.email || '',
});

// Form errors
const errors = ref({});

// Validation function
const validateForm = () => {
  errors.value = {};

  if (!businessForm.value.business_name) {
    errors.value.business_name = 'Business name is required';
  }

  if (!businessForm.value.ownership_type) {
    errors.value.ownership_type = 'Ownership type is required';
  }

  if (!businessForm.value.work_type) {
    errors.value.work_type = 'Work type is required';
  }

  if (businessForm.value.work_type === 'other' && !businessForm.value.other_work_type) {
    errors.value.other_work_type = 'Please specify other work type';
  }

  if (!businessForm.value.reason) {
    errors.value.reason = 'Reason is required';
  }

  if (!businessForm.value.office_location) {
    errors.value.office_location = 'Office location is required';
  }

  if (!businessForm.value.phone) {
    errors.value.phone = 'Phone number is required';
  }

  if (!businessForm.value.email) {
    errors.value.email = 'Email is required';
  } else if (!/\S+@\S+\.\S+/.test(businessForm.value.email)) {
    errors.value.email = 'Please enter a valid email';
  }

  return Object.keys(errors.value).length === 0;
};

const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  if (props.submit) {
    await props.submit(businessForm.value);
  }
  emit("submitted");
};

const handleNext = () => {
  if (!validateForm()) {
    return;
  }

  // In wizard mode, pass data to parent before going to next step
  if (props.mode === 'wizard') {
    emit('update:modelValue', businessForm.value);
  }

  if (props.nextStep) {
    props.nextStep();
  }
};
</script>

<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Business Info</h2>
    <div class="space-y-4">
      <div>
        <InputLabel value="Business Name *" />
        <TextInput v-model="businessForm.business_name" type="text" class="mt-1 block w-full" required />
        <InputError class="mt-2" :message="errors.business_name" />
      </div>

      <div>
        <InputLabel value="Type of Ownership *" />
        <select v-model="businessForm.ownership_type"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          required>
          <option value="">Select Ownership Type</option>
          <option value="sole_proprietorship">Sole Proprietorship</option>
          <option value="partnership">Partnership</option>
          <option value="corporation">Corporation</option>
        </select>
        <InputError class="mt-2" :message="errors.ownership_type" />
      </div>

      <div>
        <InputLabel value="Type of Work *" />
        <select v-model="businessForm.work_type"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          required>
          <option value="">Select Work Type</option>
          <option value="tv">TV</option>
          <option value="radio">Radio</option>
          <option value="online_news">Online News</option>
          <option value="newspaper">Newspaper</option>
          <option value="other">Other</option>
        </select>
        <InputError class="mt-2" :message="errors.work_type" />
      </div>

      <!-- Show other work type field when 'other' is selected -->
      <div v-if="businessForm.work_type === 'other'">
        <InputLabel value="Specify Other Work Type *" />
        <TextInput v-model="businessForm.other_work_type" type="text" class="mt-1 block w-full"
          placeholder="Please specify..." required />
        <InputError class="mt-2" :message="errors.other_work_type" />
      </div>

      <div>
        <InputLabel value="Reason for Registration *" />
        <select v-model="businessForm.reason"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          required>
          <option value="">Select Reason</option>
          <option value="new_license">New License</option>
          <option value="renewal">Renewal</option>
        </select>
        <InputError class="mt-2" :message="errors.reason" />
      </div>

      <div>
        <InputLabel value="Office Location *" />
        <textarea v-model="businessForm.office_location"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          rows="3" required></textarea>
        <InputError class="mt-2" :message="errors.office_location" />
      </div>

      <div>
        <InputLabel value="Business Phone Number *" />
        <TextInput v-model="businessForm.phone" type="text" class="mt-1 block w-full" required />
        <InputError class="mt-2" :message="errors.phone" />
      </div>

      <div>
        <InputLabel value="Business Email Address *" />
        <TextInput v-model="businessForm.email" type="email" class="mt-1 block w-full" required />
        <InputError class="mt-2" :message="errors.email" />
      </div>
    </div>

    <!-- Wizard Mode -->
    <div v-if="mode === 'wizard'" class="flex justify-between mt-6">
      <PrimaryButton type="button" @click="props.prevStep">Back</PrimaryButton>
      <PrimaryButton type="button" @click="handleNext">Next</PrimaryButton>
    </div>

    <!-- Standalone Mode -->
    <div v-else class="flex justify-end mt-6">
      <PrimaryButton type="button" @click="handleSubmit" :class="{ 'opacity-25': props.processing }"
        :disabled="props.processing">
        Submit
      </PrimaryButton>
    </div>
  </div>
</template>
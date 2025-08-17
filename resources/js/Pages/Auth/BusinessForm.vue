<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const emit = defineEmits(["submitted"]); // 👈 event to notify parent

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
});

const handleSubmit = async () => {
  if (props.submit) {
    await props.submit();   // run parent submit logic
  }
  emit("submitted");        // 👈 tell parent to close
};
</script>

<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Business Info</h2>
    <div class="space-y-4">
      <div>
        <InputLabel value="Business Name" />
        <TextInput type="text" class="mt-1 block w-full" />
      </div>

      <div>
        <InputLabel value="Type of Ownership" />
        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option>Sole Proprietorship</option>
          <option>Partnership</option>
          <option>Corporation</option>
          <option>Other</option>
        </select>
      </div>

      <div>
        <InputLabel value="Type of Work" />
        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option>TV</option>
          <option>Radio</option>
          <option>Online News</option>
          <option>Newspaper</option>
        </select>
      </div>

      <div>
        <InputLabel value="Reason for Registration" />
        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option>New License</option>
          <option>Renewal</option>
        </select>
      </div>

      <div>
        <InputLabel value="Office Location" />
        <TextInput type="text" class="mt-1 block w-full" />
      </div>

      <div>
        <InputLabel value="Business Phone Number" />
        <TextInput type="text" class="mt-1 block w-full" />
      </div>

      <div>
        <InputLabel value="Business Email Address" />
        <TextInput type="email" class="mt-1 block w-full" />
      </div>
    </div>

    <!-- Wizard Mode -->
    <div v-if="mode === 'wizard'" class="flex justify-between mt-6">
      <PrimaryButton type="button" @click="props.prevStep">Back</PrimaryButton>
      <PrimaryButton type="button" @click="props.nextStep">Next</PrimaryButton>
    </div>

    <!-- Standalone Mode -->
    <div v-else class="flex justify-end mt-6">
      <PrimaryButton 
        type="button" 
        @click="handleSubmit"
        :class="{ 'opacity-25': props.processing }"
        :disabled="props.processing"
      >
        Submit
      </PrimaryButton>
    </div>
  </div>
</template>

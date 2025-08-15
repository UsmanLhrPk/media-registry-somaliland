<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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
});
</script>

<template>
  <div>
    <h2 class="text-xl font-bold mb-4">License Application</h2>
    <div class="space-y-4">
      <div>
        <InputLabel value="Select Business" />
        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option>Business 1</option>
          <option>Business 2</option>
          <option>Business 3</option>
        </select>
      </div>

      <div>
        <InputLabel value="Document Attachment" />
        <TextInput type="text" placeholder="Upload or attach document" class="mt-1 block w-full" />
      </div>
    </div>

    <!-- Wizard Mode -->
    <div v-if="mode === 'wizard'" class="flex justify-between mt-6">
      <PrimaryButton type="button" @click="props.prevStep">Back</PrimaryButton>
      <PrimaryButton 
        type="button" 
        @click="props.submit" 
        :class="{ 'opacity-25': props.processing }" 
        :disabled="props.processing"
      >
        Register
      </PrimaryButton>
    </div>

    <!-- Standalone Mode -->
    <div v-else class="flex justify-end mt-6">
      <PrimaryButton 
        type="button" 
        @click="props.submit" 
        :class="{ 'opacity-25': props.processing }" 
        :disabled="props.processing"
      >
        Submit
      </PrimaryButton>
    </div>
  </div>
</template>

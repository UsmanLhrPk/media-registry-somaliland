<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import BusinessForm from './BusinessForm.vue';
import { ref } from 'vue';

const form = useForm({
    full_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    job_title: '',
    id_number: '',
    id_type: '',
    address: '',
    phone: '',
    terms: false,
});

// Business form data
const businessData = ref({
    business_name: '',
    ownership_type: '',
    work_type: '',
    other_work_type: '',
    reason: '',
    office_location: '',
    phone: '',
    email: '',
});

// Track what steps have been filled
const completedSteps = ref({
    step1: false,
    step2: false,
});

const step = ref(1);

const nextStep = () => {
    if (step.value < 2) step.value++;
};

const prevStep = () => {
    if (step.value > 1) step.value--;
};

// Skip step 2 (Business Info) and register
const skipStep2AndRegister = () => {
    completedSteps.value.step2 = false;
    businessData.value = {
        business_name: '',
        ownership_type: '',
        work_type: '',
        other_work_type: '',
        reason: '',
        office_location: '',
        phone: '',
        email: '',
    };
    submitRegistration();
};

// Handle business data update from BusinessForm
const updateBusinessData = (data) => {
    businessData.value = { ...data };
    completedSteps.value.step2 = true;
    console.log('Business data updated:', businessData.value);
};

// Mark step 1 as completed when moving to next step
const handleStep1Next = () => {
    completedSteps.value.step1 = true;
    nextStep();
};

// Submit registration
const submitRegistration = () => {
    console.log('Submitting registration with completed steps:', completedSteps.value);
    console.log('Business data:', businessData.value);

    const registrationData = {
        // Always include owner data (Step 1)
        full_name: form.full_name,
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation,
        job_title: form.job_title,
        id_number: form.id_number,
        id_type: form.id_type,
        address: form.address,
        phone: form.phone,
        terms: form.terms,

        // Include what steps were completed
        completed_steps: {
            step1: completedSteps.value.step1,
            step2: completedSteps.value.step2,
        }
    };

    // Add business data if step 2 was completed
    if (completedSteps.value.step2) {
        registrationData.business_name = businessData.value.business_name;
        registrationData.ownership_type = businessData.value.ownership_type;
        registrationData.work_type = businessData.value.work_type;
        registrationData.other_work_type = businessData.value.other_work_type;
        registrationData.reason = businessData.value.reason;
        registrationData.office_location = businessData.value.office_location;
        registrationData.business_phone = businessData.value.phone;
        registrationData.business_email = businessData.value.email;
    }

    console.log('Final registration data:', registrationData);

    // Create form and submit
    const completeForm = useForm(registrationData);

    completeForm.post(route('register'), {
        onFinish: () => {
            completeForm.reset('password', 'password_confirmation');
        },
        onSuccess: () => {
            console.log('Registration successful!');
        },
        onError: (errors) => {
            console.error('Registration errors:', errors);
        }
    });
};
</script>

<template>

    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Progress Bar -->
        <div class="flex items-center justify-between mb-8">
            <!-- Step 1 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full border-2"
                    :class="completedSteps.step1 ? 'bg-green-500 text-white border-green-500' : step === 1 ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 text-gray-400'">
                    <span v-if="completedSteps.step1">✓</span>
                    <span v-else>1</span>
                </div>
                <span class="ml-2 font-medium" :class="step >= 1 ? 'text-indigo-600' : 'text-gray-500'">
                    Owner Info
                </span>
            </div>

            <div class="flex-1 border-t-2 mx-2" :class="step >= 2 ? 'border-indigo-600' : 'border-gray-300'"></div>

            <!-- Step 2 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full border-2"
                    :class="completedSteps.step2 ? 'bg-green-500 text-white border-green-500' : step === 2 ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 text-gray-400'">
                    <span v-if="completedSteps.step2">✓</span>
                    <span v-else>2</span>
                </div>
                <span class="ml-2 font-medium" :class="step >= 2 ? 'text-indigo-600' : 'text-gray-500'">
                    Business Info <span class="text-xs">(Optional)</span>
                </span>
            </div>
        </div>

        <!-- Form Container -->
        <div class="wizard-container max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md">
            <form @submit.prevent="step === 1 ? handleStep1Next() : submitRegistration()">

                <!-- Step 1: Owner Info -->
                <div v-if="step === 1">
                    <h3 class="text-lg font-semibold mb-4">Owner Information</h3>

                    <div>
                        <InputLabel for="full_name" value="Full Name *" />
                        <TextInput id="full_name" v-model="form.full_name" type="text" class="mt-1 block w-full"
                            required autofocus />
                        <InputError class="mt-2" :message="form.errors.full_name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" value="Email *" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Password *" />
                        <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full"
                            required />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password_confirmation" value="Confirm Password *" />
                        <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
                            class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="job_title" value="Job Title *" />
                        <TextInput id="job_title" v-model="form.job_title" type="text" class="mt-1 block w-full"
                            required />
                        <InputError class="mt-2" :message="form.errors.job_title" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="id_type" value="ID Type *" />
                        <select id="id_type" v-model="form.id_type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                            <option value="">Select ID Type</option>
                            <option value="cnic">CNIC</option>
                            <option value="passport">Passport</option>
                            <option value="driving_license">Driving License</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.id_type" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="id_number" value="ID Number *" />
                        <TextInput id="id_number" v-model="form.id_number" type="text" class="mt-1 block w-full"
                            required />
                        <InputError class="mt-2" :message="form.errors.id_number" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="phone" value="Phone Number *" />
                        <TextInput id="phone" v-model="form.phone" type="text" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="address" value="Address *" />
                        <textarea id="address" v-model="form.address"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            rows="3" required></textarea>
                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>

                    <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                        <InputLabel for="terms">
                            <div class="flex items-center">
                                <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />
                                <div class="ms-2">
                                    I agree to the
                                    <a target="_blank" :href="route('terms.show')"
                                        class="underline text-sm text-gray-600 hover:text-gray-900">Terms of Service</a>
                                    and
                                    <a target="_blank" :href="route('policy.show')"
                                        class="underline text-sm text-gray-600 hover:text-gray-900">Privacy Policy</a>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.terms" />
                        </InputLabel>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                        Already registered?
                        </Link>
                        <PrimaryButton type="submit" class="ml-4">Next</PrimaryButton>
                    </div>
                </div>

                <!-- Step 2: Business Info -->
                <div v-else-if="step === 2">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Business Information</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            This step is optional. You can skip it and add business information later from your
                            dashboard.
                        </p>
                        <p class="text-sm text-blue-600 mt-1">
                            <strong>Note:</strong> You can apply for licenses later from your dashboard after creating
                            businesses.
                        </p>
                    </div>

                    <BusinessForm mode="wizard" v-model="businessData" @update:modelValue="updateBusinessData" />

                    <!-- Buttons for Step 2 -->
                    <div class="flex justify-between mt-6">
                        <PrimaryButton type="button" @click="prevStep" class="bg-gray-500 hover:bg-gray-600">
                            Back
                        </PrimaryButton>
                        <div class="space-x-2">
                            <PrimaryButton type="button" @click="skipStep2AndRegister"
                                class="bg-yellow-500 hover:bg-yellow-600" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                Skip & Register
                            </PrimaryButton>
                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                Complete Registration
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticationCard>
</template>
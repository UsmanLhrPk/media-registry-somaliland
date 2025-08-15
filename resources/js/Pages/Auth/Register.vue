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
import ApplicationForm from './ApplicationForm.vue';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const step = ref(1);

const nextStep = () => {
    if (step.value < 3) step.value++;
};

const prevStep = () => {
    if (step.value > 1) step.value--;
};

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full border-2"
                    :class="step > 1 ? 'bg-green-500 text-white border-green-500' : step === 1 ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 text-gray-400'"
                >
                    <span v-if="step > 1">✓</span>
                    <span v-else>1</span>
                </div>
                <span class="ml-2 font-medium" :class="step >= 1 ? 'text-indigo-600' : 'text-gray-500'">
                    Owner Info
                </span>
            </div>

            <div class="flex-1 border-t-2 mx-2" :class="step >= 2 ? 'border-indigo-600' : 'border-gray-300'"></div>

            <!-- Step 2 -->
            <div class="flex items-center">
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full border-2"
                    :class="step > 2 ? 'bg-green-500 text-white border-green-500' : step === 2 ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 text-gray-400'"
                >
                    <span v-if="step > 2">✓</span>
                    <span v-else>2</span>
                </div>
                <span class="ml-2 font-medium" :class="step >= 2 ? 'text-indigo-600' : 'text-gray-500'">
                    Business Info
                </span>
            </div>

            <div class="flex-1 border-t-2 mx-2" :class="step >= 3 ? 'border-indigo-600' : 'border-gray-300'"></div>

            <!-- Step 3 -->
            <div class="flex items-center">
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full border-2"
                    :class="step === 3 ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 text-gray-400'"
                >
                    3
                </div>
                <span class="ml-2 font-medium" :class="step >= 3 ? 'text-indigo-600' : 'text-gray-500'">
                    License Application
                </span>
            </div>
        </div>

        <!-- Form Container -->
        <div class="wizard-container max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md">
            <form @submit.prevent="step === 3 ? submit() : nextStep()">
                
                <!-- Step 1: Owner Info -->
                <div v-if="step === 1">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name"/>
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="username"/>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autocomplete="new-password"/>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password"/>
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                        <InputLabel for="terms">
                            <div class="flex items-center">
                                <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />
                                <div class="ms-2">
                                    I agree to the 
                                    <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 hover:text-gray-900">Terms of Service</a> 
                                    and 
                                    <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 hover:text-gray-900">Privacy Policy</a>
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
                    <BusinessForm :prevStep="prevStep" :nextStep="nextStep" mode="wizard"/>
                </div>

                <!-- Step 3: License Application (separate file now) -->
                <div v-else-if="step === 3">
                    <ApplicationForm :prevStep="prevStep" :submit="submit" :processing="form.processing" mode="wizard" />
                </div>
            </form>
        </div>
    </AuthenticationCard>
</template>

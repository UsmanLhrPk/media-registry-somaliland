<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Owner;
use App\Models\Role;
use App\Models\MediaEntity;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Log incoming data for debugging
        Log::info('Registration data received:', [
            'completed_steps' => $input['completed_steps'] ?? 'not set',
            'has_business_name' => isset($input['business_name']),
        ]);

        // Base validation rules for owner data
        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'job_title' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:50', 'unique:owners'],
            'id_type' => ['required', 'string', 'in:cnic,passport,driving_license'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'completed_steps' => ['required', 'array'],
        ];

        // Check what steps were completed
        $completedSteps = $input['completed_steps'] ?? [];
        $step2Completed = $completedSteps['step2'] ?? false;

        Log::info('Steps analysis:', [
            'step2_completed' => $step2Completed,
        ]);

        // Add business validation rules if step 2 was completed
        if ($step2Completed) {
            $rules = array_merge($rules, [
                'business_name' => ['required', 'string', 'max:255'],
                'ownership_type' => ['required', 'in:sole_proprietorship,partnership,corporation'],
                'work_type' => ['required', 'in:tv,radio,online_news,newspaper,other'],
                'other_work_type' => ['nullable', 'string', 'max:255'],
                'reason' => ['required', 'in:new_license,renewal'],
                'office_location' => ['required', 'string'],
                'business_phone' => ['required', 'string', 'max:50'],
                'business_email' => ['required', 'email', 'max:255'],
            ]);
        }

        $validator = Validator::make($input, $rules);
        $validator->validate();

        // Database transaction to rollback if any error occurs
        return DB::transaction(function () use ($input, $step2Completed) {
            
            // Always create User
            $user = User::create([
                'name' => $input['full_name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            Log::info('User created:', ['user_id' => $user->id]);

            // Always create Owner record
            $owner = Owner::create([
                'user_id' => $user->id,
                'full_name' => $input['full_name'],
                'job_title' => $input['job_title'],
                'id_number' => $input['id_number'],
                'id_type' => $input['id_type'],
                'address' => $input['address'],
                'phone' => $input['phone'],
                'email' => $input['email'],
            ]);

            Log::info('Owner created:', ['owner_id' => $owner->id]);

            // Assign owner role
            $ownerRole = Role::firstOrCreate([
                'name' => 'owner',
                'guard_name' => 'web'
            ]);
            
            $user->assignRole($ownerRole);
            Log::info('Role assigned:', ['role' => 'owner']);

            //  Create Media Entity (Business) if step 2 was completed
            if ($step2Completed) {
                $mediaEntity = MediaEntity::create([
                    'owner_id' => $owner->id,
                    'business_name' => $input['business_name'],
                    'ownership_type' => $input['ownership_type'],
                    'work_type' => $input['work_type'],
                    'other_work_type' => $input['other_work_type'] ?? '',
                    'reason' => $input['reason'],
                    'office_location' => $input['office_location'],
                    'phone' => $input['business_phone'],
                    'email' => $input['business_email'],
                ]);

                Log::info('MediaEntity created:', [
                    'media_entity_id' => $mediaEntity->id,
                    'business_name' => $mediaEntity->business_name
                ]);
            } else {
                Log::info('MediaEntity skipped - step 2 not completed');
            }

            Log::info('Registration completed successfully', [
                'user_id' => $user->id,
                'owner_id' => $owner->id,
                'business_created' => $step2Completed
            ]);

            return $user;
        });
    }
}
<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Owner;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;

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
        $validator = Validator::make($input, [
            // Owner validation rules
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'job_title' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:50', 'unique:owners'],
            'id_type' => ['required', 'string', 'in:cnic,passport,driving_license'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);
        
        $validator->validate();

        // Database transaction to rollback if any error occurs
        return DB::transaction(function () use ($input) {
            
            // User Create
            $user = User::create([
                'name' => $input['full_name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            // Owner create
            Owner::create([
                'user_id' => $user->id,
                'full_name' => $input['full_name'],
                'job_title' => $input['job_title'],
                'id_number' => $input['id_number'],
                'id_type' => $input['id_type'],
                'address' => $input['address'],
                'phone' => $input['phone'],
                'email' => $input['email'],
            ]);

            // User role assignment
            $ownerRole = Role::firstOrCreate([
                'name' => 'owner',
                'guard_name' => 'web'
            ]);
            
            $user->assignRole($ownerRole);

            return $user;
        });
    }
}
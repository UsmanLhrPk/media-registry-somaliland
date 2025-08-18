<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner User',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole($adminRole);
        $owner->assignRole($ownerRole);

        $this->command->info('Admin and Owner users created with roles assigned.');
        $this->command->warn('Admin Login: admin@example.com / password');
        $this->command->warn('Owner Login: owner@example.com / password');
    }
}

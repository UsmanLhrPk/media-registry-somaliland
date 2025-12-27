<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Display a listing of admins
     */
    public function index()
    {
        // Get all users with admin role
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->with(['roles'])->get();

        return Inertia::render('Admin/Index', [
            'admins' => $admins
        ]);
    }

    /**
     * Show the form for creating a new admin
     */
    public function create()
    {
        return Inertia::render('Admin/Create');
    }

    /**
     * Store a newly created admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
        ]);

        // Create the user
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign admin role
        $admin->assignRole('admin');

        return redirect()->route('admins.index')
                        ->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified admin
     */
    public function show(User $admin)
    {
        // Load roles relationship
        $admin->load('roles');

        // Verify this user is actually an admin
        if (!$admin->hasRole('admin')) {
            abort(404, 'Admin not found.');
        }

        return Inertia::render('Admin/Show', [
            'admin' => $admin
        ]);
    }

    /**
     * Show the form for editing the specified admin
     */
    public function edit(User $admin)
    {
        // Load roles relationship
        $admin->load('roles');

        // Verify this user is actually an admin
        if (!$admin->hasRole('admin')) {
            abort(404, 'Admin not found.');
        }

        return Inertia::render('Admin/Edit', [
            'admin' => $admin
        ]);
    }

    /**
     * Update the specified admin
     */
    public function update(Request $request, User $admin)
    {
        // Verify this user is actually an admin
        if (!$admin->hasRole('admin')) {
            abort(404, 'Admin not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => ['nullable', Password::defaults()],
        ]);

        // Update admin data
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect()->route('admins.index')
                        ->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified admin
     */
    public function destroy(User $admin)
    {
        // Verify this user is actually an admin
        if (!$admin->hasRole('admin')) {
            abort(404, 'Admin not found.');
        }

        // Don't allow deleting yourself
        if ($admin->id === auth()->id()) {
            return redirect()->route('admins.index')
                            ->with('error', 'You cannot delete yourself.');
        }

        // Delete the admin
        $admin->delete();

        return redirect()->route('admins.index')
                        ->with('success', 'Admin deleted successfully.');
    }
}
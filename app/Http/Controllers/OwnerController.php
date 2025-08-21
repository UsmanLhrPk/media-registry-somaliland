<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::all();

        // Inertia page
        return Inertia::render('Owners/Index', [
            'owners' => $owners
        ]);
    }

        public function show($id)
    {
        $owner = Owner::findOrFail($id);

        return Inertia::render('Owners/Show', [
            'owner' => $owner
        ]);
    }

    public function create()
    {
        return Inertia::render('Owners/Create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'job_title' => 'nullable|string|max:150',
            'id_number' => 'required|string|max:100|unique:owners',
            'id_type' => 'required|in:id_card,passport',
            'address' => 'required|string',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:owners',
        ]);

        Owner::create($validated);

        return redirect()->route('owners.index')->with('success', 'Owner created successfully.');
    }

    public function edit($id)
    {
        $owner = Owner::findOrFail($id);

        return Inertia::render('Owners/Edit', [
            'owner' => $owner
        ]);
    }

    public function update(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'job_title' => 'nullable|string|max:150',
            'id_number' => 'required|string|max:100|unique:owners,id_number,' . $owner->id,
            'id_type' => 'required|in:id_card,passport',
            'address' => 'required|string',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:owners,email,' . $owner->id,
        ]);

        $owner->update($validated);

        return redirect()->route('owners.index')->with('success', 'Owner updated successfully.');
    }

    public function destroy($id)
    {
        $owner = Owner::findOrFail($id);
        $owner->delete();

        return redirect()->route('owners.index')->with('success', 'Owner deleted successfully.');
    }
}

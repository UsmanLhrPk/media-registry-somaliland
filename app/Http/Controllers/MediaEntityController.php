<?php

namespace App\Http\Controllers;

use App\Models\MediaEntity;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MediaEntityController extends Controller
{
    /**
     * Store a newly created media entity in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'ownership_type' => 'required|in:sole_proprietorship,partnership,corporation',
            'work_type' => 'required|in:tv,radio,online_news,newspaper,other',
            'other_work_type' => 'nullable|string|max:255',
            'reason' => 'required|in:new_license,renewal',
            'office_location' => 'required|string',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
        ]);

        $user = Auth::user();
        
        // Get or create the owner record
        $owner = $user->owner;
        
        if (!$owner) {
            // Automatically create owner record if it doesn't exist
            $owner = Owner::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'job_title' => 'Business Owner',
                'id_number' => '',
                'id_type' => 'cnic',
                'address' => $request->office_location, // Use office location as default
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
        }

        // Create the media entity
        $mediaEntity = MediaEntity::create([
            'owner_id' => $owner->id,
            'business_name' => $request->business_name,
            'ownership_type' => $request->ownership_type,
            'work_type' => $request->work_type,
            // Handle null other_work_type - set empty string if null
            'other_work_type' => $request->other_work_type ?? '',
            'reason' => $request->reason,
            'office_location' => $request->office_location,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Business information created successfully!');
    }

    /**
     * Get media entities for the authenticated owner
     */
    public function getOwnerMediaEntities()
    {
        $user = Auth::user();
        $owner = $user->owner;
        
        if (!$owner) {
            return response()->json([]);
        }

        $mediaEntities = $owner->mediaEntities()->latest()->get()->map(function ($entity) {
            return [
                'id' => $entity->id,
                'business_name' => $entity->business_name,
                'work_type' => $entity->formatted_work_type,
                'ownership_type' => $entity->formatted_ownership_type,
                'status' => $entity->status,
                'created_at' => $entity->created_at->format('M d, Y'),
            ];
        });

        return response()->json($mediaEntities);
    }
}
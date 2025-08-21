<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\MediaEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LicenseController extends Controller
{
    /**
     * Store a newly created license application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'media_entity_id' => 'required|exists:media_entities,id',
            'license_type' => 'required|in:new,renewal',
        ]);

        $user = Auth::user();
        $owner = $user->owner;

        if (!$owner) {
            return back()->withErrors(['error' => 'Owner record not found.']);
        }

        // Verify that the media entity belongs to the authenticated owner
        $mediaEntity = MediaEntity::where('id', $request->media_entity_id)
            ->where('owner_id', $owner->id)
            ->first();

        if (!$mediaEntity) {
            return back()->withErrors(['error' => 'Media entity not found or access denied.']);
        }

        // Generate unique license number
        $licenseNumber = $this->generateLicenseNumber();

        // Create license
        $license = License::create([
            'media_entity_id' => $mediaEntity->id,
            'license_number' => $licenseNumber,
            'license_type' => $request->license_type,
            'issue_date' => Carbon::now(),
            'expiry_date' => Carbon::now()->addYear(), // 1 year validity
            'status' => 'pending',
        ]);

        return back()->with('success', 'License application submitted successfully!');
    }

    /**
     * Get all license applications for admin/super admin
     */
    public function index()
    {
        $licenses = License::with(['mediaEntity.owner.user'])
            ->latest()
            ->get()
            ->map(function ($license) {
                return [
                    'id' => $license->id,
                    'license_number' => $license->license_number,
                    'business_name' => $license->mediaEntity->business_name,
                    'owner_name' => $license->mediaEntity->owner->full_name,
                    'work_type' => $license->mediaEntity->formatted_work_type,
                    'license_type' => $license->formatted_license_type,
                    'status' => $license->status,
                    'issue_date' => $license->issue_date->format('M d, Y'),
                    'expiry_date' => $license->expiry_date->format('M d, Y'),
                    'created_at' => $license->created_at->format('M d, Y'),
                    // Additional details for admin view
                    'owner_email' => $license->mediaEntity->owner->email,
                    'owner_phone' => $license->mediaEntity->owner->phone,
                    'business_phone' => $license->mediaEntity->phone,
                    'business_email' => $license->mediaEntity->email,
                    'office_location' => $license->mediaEntity->office_location,
                ];
            });

        return response()->json($licenses);
    }

    /**
     * Update license status (approve/reject)
     */
 public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:active,revoked',
        'admin_comment' => 'nullable|string|max:1000',
    ]);

    $license = License::findOrFail($id);

    $license->update([
        'status' => $request->status,
    ]);

    \Log::info('License status updated', [
        'license_id' => $license->id,
        'old_status' => $license->getOriginal('status'),
        'new_status' => $request->status,
        'admin_id' => Auth::id(),
        'admin_comment' => $request->admin_comment,
    ]);

    // ✅ Instead of JSON, redirect back with flash for Inertia
    return redirect()->back()->with('success', 'License status updated successfully.');
}


    /**
     * Get licenses for the authenticated owner
     */
    public function myLicenses()
    {
        $user = Auth::user();
        $owner = $user->owner;

        if (!$owner) {
            return response()->json([]);
        }

        $licenses = License::whereHas('mediaEntity', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
        ->with('mediaEntity')
        ->latest()
        ->get()
        ->map(function ($license) {
            return [
                'id' => $license->id,
                'license_number' => $license->license_number,
                'business_name' => $license->mediaEntity->business_name,
                'license_type' => $license->formatted_license_type,
                'status' => $license->formatted_status,
                'issue_date' => $license->issue_date->format('M d, Y'),
                'expiry_date' => $license->expiry_date->format('M d, Y'),
            ];
        });

        return response()->json($licenses);
    }

    /**
     * Show specific license
     */
    public function show($id)
    {
        $license = License::with(['mediaEntity.owner.user'])->findOrFail($id);
        
        return response()->json([
            'id' => $license->id,
            'license_number' => $license->license_number,
            'license_type' => $license->formatted_license_type,
            'status' => $license->status,
            'issue_date' => $license->issue_date->format('M d, Y'),
            'expiry_date' => $license->expiry_date->format('M d, Y'),
            'business' => [
                'name' => $license->mediaEntity->business_name,
                'work_type' => $license->mediaEntity->formatted_work_type,
                'ownership_type' => $license->mediaEntity->formatted_ownership_type,
                'phone' => $license->mediaEntity->phone,
                'email' => $license->mediaEntity->email,
                'office_location' => $license->mediaEntity->office_location,
            ],
            'owner' => [
                'name' => $license->mediaEntity->owner->full_name,
                'email' => $license->mediaEntity->owner->email,
                'phone' => $license->mediaEntity->owner->phone,
                'job_title' => $license->mediaEntity->owner->job_title,
            ]
        ]);
    }

    /**
     * Generate unique license number
     */
    private function generateLicenseNumber()
    {
        $prefix = 'LIC';
        $year = date('Y');
        
        // Get the last license number for this year
        $lastLicense = License::where('license_number', 'like', $prefix . $year . '%')
            ->orderBy('license_number', 'desc')
            ->first();

        if ($lastLicense) {
            $lastNumber = (int) substr($lastLicense->license_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
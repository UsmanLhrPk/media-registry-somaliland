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

    /**
     * Show all licenses (Admin only)
     */
    public function index()
    {
        $licenses = License::with('mediaEntity.owner')->latest()->get();
        return response()->json($licenses);
    }

    /**
     * Show specific license
     */
    public function show($id)
    {
        $license = License::with('mediaEntity.owner')->findOrFail($id);
        return response()->json($license);
    }
}
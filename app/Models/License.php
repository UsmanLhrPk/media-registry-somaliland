<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class License extends Model
{
    protected $fillable = [
        'media_entity_id',
        'license_number',
        'license_type',
        'issue_date',
        'expiry_date',
        'renewed_date',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewed_date' => 'date',
    ];

    /**
     * Get the media entity that owns the license.
     */
    public function mediaEntity(): BelongsTo
    {
        return $this->belongsTo(MediaEntity::class);
    }

    /**
     * Get formatted license type
     */
    public function getFormattedLicenseTypeAttribute()
    {
        return match($this->license_type) {
            'new' => 'New License',
            'renewal' => 'License Renewal',
            default => $this->license_type
        };
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'expired' => 'Expired',
            'revoked' => 'Revoked',
            'pending' => 'Pending',
            default => $this->status
        };
    }
}
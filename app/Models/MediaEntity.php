<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaEntity extends Model
{
    protected $fillable = [
        'owner_id',
        'business_name',
        'ownership_type',
        'work_type',
        'other_work_type',
        'reason',
        'office_location',
        'phone',
        'email',
    ];

    /**
     * Get the owner that owns the media entity.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the licenses for the media entity.
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    /**
     * Get formatted ownership type
     */
    public function getFormattedOwnershipTypeAttribute()
    {
        return match($this->ownership_type) {
            'sole_proprietorship' => 'Sole Proprietorship',
            'partnership' => 'Partnership',
            'corporation' => 'Corporation',
            default => $this->ownership_type
        };
    }

    /**
     * Get formatted work type
     */
    public function getFormattedWorkTypeAttribute()
    {
        return match($this->work_type) {
            'tv' => 'TV',
            'radio' => 'Radio',
            'online_news' => 'Online News',
            'newspaper' => 'Newspaper',
            'other' => $this->other_work_type ?? 'Other',
            default => $this->work_type
        };
    }

    /**
     * Get status based on reason
     */
    public function getStatusAttribute()
    {
        return match($this->reason) {
            'new_license' => 'Pending',
            'renewal' => 'Active',
            default => 'Unknown'
        };
    }
}
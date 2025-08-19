<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Owner extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'job_title',
        'id_number',
        'id_type',
        'address',
        'phone',
        'email',
    ];

    /**
     * Get the user that owns the owner record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
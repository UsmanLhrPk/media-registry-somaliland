<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = [
        'full_name',
        'job_title',
        'id_number',
        'id_type',
        'address',
        'phone',
        'email',
    ];
}

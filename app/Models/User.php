<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the owner record associated with the user.
     */
    public function owner(): HasOne
    {
        return $this->hasOne(Owner::class);
    }

    /**
     * Get the roles for the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
                    ->where('model_type', static::class);
    }

    /**
     * Assign a role to the user
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            // Check if role already assigned
            $existingRole = DB::table('model_has_roles')
                ->where('model_id', $this->id)
                ->where('role_id', $role->id)
                ->where('model_type', static::class)
                ->first();

            if (!$existingRole) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $role->id,
                    'model_type' => static::class,
                    'model_id' => $this->id,
                ]);
            }
        }

        return $this;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Get user's first role name
     */
    public function getRoleAttribute()
    {
        return $this->roles()->first()?->name ?? 'user';
    }

    /**
     * Get all role names for the user
     */
    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name')->toArray();
    }
}
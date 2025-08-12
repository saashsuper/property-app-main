<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserType;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'user_role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's type
     */
    public function userType()
    {
        return $this->belongsTo('App\Models\UserType', 'user_role_id');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->userType && in_array($this->userType->name, ['Admin', 'Super Admin']);
    }

    /**
     * Check if user has a specific type
     */
    public function hasType($typeName)
    {
        return $this->userType && $this->userType->name === $typeName;
    }

    /**
     * Scope a query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}

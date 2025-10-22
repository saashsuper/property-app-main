<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserType;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

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
        'user_type_id',
        'created_by',
        'updated_by',
        'deleted_by',
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
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the user's type
     */
    public function userType()
    {
        return $this->belongsTo('App\Models\UserType', 'user_type_id');
    }

    /**
     * Get the user who created this user
     */
    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    /**
     * Get users created by this user
     */
    public function createdUsers()
    {
        return $this->hasMany('App\Models\User', 'created_by');
    }

    /**
     * Get the user who last updated this user
     */
    public function updater()
    {
        return $this->belongsTo('App\Models\User', 'updated_by');
    }

    /**
     * Get users updated by this user
     */
    public function updatedUsers()
    {
        return $this->hasMany('App\Models\User', 'updated_by');
    }

    /**
     * Get the user who deleted this user
     */
    public function deleter()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by');
    }

    /**
     * Get users deleted by this user
     */
    public function deletedUsers()
    {
        return $this->hasMany('App\Models\User', 'deleted_by');
    }

    /**
     * Get the inspection teams for this user.
     */
    public function inspectionTeams()
    {
        return $this->hasMany(BlockInspectionTeam::class);
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

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserType;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, HasPushSubscriptions;

    // Archive status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

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
        'phone',
        'address',
        'is_active',
        'user_type_id',
        'contract_company_id',
        'fcm_token',
        'archive_status',
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
     * Get the contract company associated with this user
     */
    public function contractCompany()
    {
        return $this->belongsTo('App\Models\ContractCompany', 'contract_company_id');
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
     * Get blocks owned by this user
     */
    public function ownedBlocks()
    {
        return $this->hasMany(Block::class, 'user_id');
    }

    /**
     * Get blocks managed by this user
     */
    public function managedBlocks()
    {
        return $this->hasMany(Block::class, 'block_manager_id');
    }

    /**
     * Get block issues created by this user
     */
    public function createdBlockIssues()
    {
        return $this->hasMany(BlockIssue::class, 'created_by');
    }

    /**
     * Get block issues assigned to this user
     */
    public function assignedBlockIssues()
    {
        return $this->hasMany(BlockIssue::class, 'assigned_to');
    }

    /**
     * Get block issues reported by this user
     */
    public function reportedBlockIssues()
    {
        return $this->hasMany(BlockIssue::class, 'reported_by');
    }

    /**
     * Get block issues issued by this user
     */
    public function issuedBlockIssues()
    {
        return $this->hasMany(BlockIssue::class, 'issued_by');
    }

    /**
     * Get block work orders created by this user
     */
    public function createdBlockWorkOrders()
    {
        return $this->hasMany(BlockWorkOrder::class, 'created_by');
    }

    /**
     * Get block work orders issued by this user
     */
    public function issuedBlockWorkOrders()
    {
        return $this->hasMany(BlockWorkOrder::class, 'issued_by');
    }

    /**
     * Get block work order teams for this user
     */
    public function blockWorkOrderTeams()
    {
        return $this->hasMany(BlockWorkOrderTeam::class, 'user_id');
    }

    /**
     * Get block work order logs for this user
     */
    public function blockWorkOrderLogs()
    {
        return $this->hasMany(BlockWorkOrderLog::class, 'user_id');
    }

    /**
     * Get issue logs for this user
     */
    public function issueLogs()
    {
        return $this->hasMany(IssueLog::class, 'user_id');
    }

    /**
     * Get block issue actions performed by this user
     */
    public function blockIssueActions()
    {
        return $this->hasMany(BlockIssueAction::class, 'performed_by');
    }

    /**
     * Check if user is admin (by user type or Spatie role). Used for @admin directive and completed work order notes/photos.
     */
    public function isAdmin()
    {
        if ($this->userType && in_array($this->userType->name, ['Admin', 'Super Admin'])) {
            return true;
        }
        return $this->hasAnyRole(['Admin', 'Super Admin']);
    }

    /**
     * Check if user has a specific type
     */
    public function hasType($typeName)
    {
        return $this->userType && $this->userType->name === $typeName;
    }

    /**
     * Scope a query to only include active users (not deleted and archive_status is active or null).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
                    ->where(function($q) {
                        $q->where('archive_status', self::STATUS_ACTIVE)
                          ->orWhereNull('archive_status'); // Handle existing records without archive_status
                    });
    }

    /**
     * Scope a query to only include archived users.
     */
    public function scopeArchived($query)
    {
        return $query->where('archive_status', self::STATUS_ARCHIVED);
    }

    /**
     * Check if user has any related entities.
     * Returns true if user has blocks, issues, work orders, or other related data.
     */
    public function hasRelatedEntities(): bool
    {
        return $this->ownedBlocks()->count() > 0
            || $this->managedBlocks()->count() > 0
            || $this->createdBlockIssues()->count() > 0
            || $this->assignedBlockIssues()->count() > 0
            || $this->reportedBlockIssues()->count() > 0
            || $this->issuedBlockIssues()->count() > 0
            || $this->createdBlockWorkOrders()->count() > 0
            || $this->issuedBlockWorkOrders()->count() > 0
            || $this->blockWorkOrderTeams()->count() > 0
            || $this->blockWorkOrderLogs()->count() > 0
            || $this->issueLogs()->count() > 0
            || $this->blockIssueActions()->count() > 0
            || $this->inspectionTeams()->count() > 0
            || $this->createdUsers()->count() > 0
            || $this->updatedUsers()->count() > 0
            || $this->deletedUsers()->count() > 0;
    }

    /**
     * Check if user is archived.
     */
    public function isArchived(): bool
    {
        return $this->archive_status === self::STATUS_ARCHIVED;
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return ($this->archive_status === self::STATUS_ACTIVE || is_null($this->archive_status)) && is_null($this->deleted_at);
    }

    /**
     * Archive the user (soft delete with archived status).
     */
    public function archive(): bool
    {
        $this->archive_status = self::STATUS_ARCHIVED;
        $this->deleted_by = auth()->id();
        $this->save();
        return $this->delete(); // Soft delete
    }
}

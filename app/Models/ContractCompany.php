<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractCompany extends Model
{
    use HasFactory, SoftDeletes;

    // Archive status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    protected $table = 'contract_companies';

    protected $fillable = [
        'company_name',
        'address',
        'phone_number',
        'website',
        'archive_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created this company.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this company.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted this company.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get users associated with this contract company.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'contract_company_id');
    }

    /**
     * Scope a query to only include active companies (not deleted and archive_status is active or null).
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
     * Scope a query to only include archived companies.
     */
    public function scopeArchived($query)
    {
        return $query->where('archive_status', self::STATUS_ARCHIVED);
    }

    /**
     * Check if company has any related entities.
     * Returns true if company has users associated with it.
     */
    public function hasRelatedEntities(): bool
    {
        return $this->users()->count() > 0;
    }

    /**
     * Check if company is archived.
     */
    public function isArchived(): bool
    {
        return $this->archive_status === self::STATUS_ARCHIVED;
    }

    /**
     * Check if company is active.
     */
    public function isActive(): bool
    {
        return ($this->archive_status === self::STATUS_ACTIVE || is_null($this->archive_status)) && is_null($this->deleted_at);
    }

    /**
     * Archive the company (soft delete with archived status).
     */
    public function archive(): bool
    {
        $this->archive_status = self::STATUS_ARCHIVED;
        $this->deleted_by = auth()->id();
        $this->save();
        return $this->delete(); // Soft delete
    }
}

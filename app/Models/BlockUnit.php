<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockUnit extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'block_building_id',
        'block_unit_type_id',
        'unit_code',
        'unit_name',
        'owners_name',
        'salutation',
        'email',
        'resident',
        'address1',
        'address2',
        'address3',
        'country_id',
        'state_id',
        'zip',
        'mobile_no',
        'phone_number',
        'letting_agent',
        'misc_info',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'resident' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the block that owns the unit.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the building that owns the unit.
     */
    public function building()
    {
        return $this->belongsTo(BlockBuilding::class, 'block_building_id');
    }

    /**
     * Get the unit type that owns the unit.
     */
    public function unitType()
    {
        return $this->belongsTo(BlockUnitType::class, 'block_unit_type_id');
    }

    /**
     * Get the country that owns the unit.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that owns the unit.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the issues for this unit.
     */
    public function issues()
    {
        return $this->hasMany(BlockIssue::class, 'block_unit_id');
    }

    /**
     * Get the work orders for this unit.
     */
    public function workOrders()
    {
        return $this->hasMany(BlockWorkOrder::class, 'block_unit_id');
    }

    /**
     * Get the site visits for this unit.
     */
    public function siteVisits()
    {
        return $this->hasMany(BlockVisit::class, 'block_unit_id');
    }

    /**
     * Scope a query to only include active units (not deleted and status is active).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
                    ->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include archived units.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    /**
     * Check if unit has any related issues.
     * Returns true if unit has at least one issue.
     */
    public function hasIssues(): bool
    {
        return $this->issues()->count() > 0;
    }

    /**
     * Check if unit is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Check if unit is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && is_null($this->deleted_at);
    }

    /**
     * Archive the unit (soft delete with archived status).
     */
    public function archive(): bool
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->deleted_by = auth()->id();
        $this->save();
        return $this->delete(); // Soft delete
    }

    /**
     * Boot the model and register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Update block unit count when unit is created
        static::created(function ($unit) {
            $unit->updateBlockUnitCount();
        });

        // Update block unit count when unit is updated
        static::updated(function ($unit) {
            $unit->updateBlockUnitCount();
        });

        // Update block unit count when unit is deleted
        static::deleted(function ($unit) {
            $unit->updateBlockUnitCount();
        });

        // Update block unit count when unit is restored
        static::restored(function ($unit) {
            $unit->updateBlockUnitCount();
        });
    }

    /**
     * Update the block's unit count.
     */
    protected function updateBlockUnitCount()
    {
        if ($this->block) {
            $this->block->update([
                'no_of_units' => $this->block->units()->count()
            ]);
        }
    }
}

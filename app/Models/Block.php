<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'name',
        'management_company',
        'block_type_id',
        'user_id',
        'block_manager_id',
        'address1',
        'address2',
        'address3',
        'block_address',
        'management_company_address',
        'country_id',
        'state_id',
        'car_spaces',
        'inspection_count',
        'no_of_units',
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
        'car_spaces' => 'integer',
        'inspection_count' => 'integer',
        'no_of_units' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the block type that owns the block.
     */
    public function blockType()
    {
        return $this->belongsTo(BlockType::class);
    }

    /**
     * Get the user that owns the block.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the block manager for the block.
     */
    public function blockManager()
    {
        return $this->belongsTo(User::class, 'block_manager_id')->withTrashed();
    }

    /**
     * Get the property manager for the block.
     */
    public function propertyManager()
    {
        return $this->belongsTo(User::class, 'property_manager_id')->withTrashed();
    }

    /**
     * Get the country that owns the block.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that owns the block.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the buildings for the block.
     */
    public function buildings()
    {
        return $this->hasMany(BlockBuilding::class);
    }

    /**
     * Get the units for the block.
     */
    public function units()
    {
        return $this->hasMany(BlockUnit::class);
    }

    /**
     * Get the contractors for the block.
     */
    public function contractors()
    {
        return $this->hasMany(BlockContractor::class);
    }

    /**
     * Get the issues for the block.
     */
    public function issues()
    {
        return $this->hasMany(BlockIssue::class);
    }

    /**
     * Get the block issues for this block (alias for issues).
     */
    public function blockIssues()
    {
        return $this->hasMany(BlockIssue::class);
    }

    /**
     * Get the work orders for the block.
     */
    public function workOrders()
    {
        return $this->hasMany(BlockWorkOrder::class);
    }

    /**
     * Get the site visits for the block.
     */
    public function blockVisits()
    {
        return $this->hasMany(BlockVisit::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the block information for the block.
     */
    public function blockInformation()
    {
        return $this->hasMany(BlockInformation::class);
    }

    /**
     * Get the creator of the block.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the updater of the block.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    /**
     * Get the deleter of the block.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    /**
     * Scope a query to only include active blocks (not deleted and status is active).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
                    ->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include archived blocks.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    /**
     * Check if block has any related entities.
     * Returns true if block has units, buildings, issues, work orders, inspections, visits, or contractors.
     */
    public function hasRelatedEntities(): bool
    {
        return $this->units()->count() > 0
            || $this->buildings()->count() > 0
            || $this->issues()->count() > 0
            || $this->workOrders()->count() > 0
            || $this->blockInspections()->count() > 0
            || $this->blockVisits()->count() > 0
            || $this->contractors()->count() > 0
            || $this->blockInformation()->count() > 0
            || $this->images()->count() > 0;
    }

    /**
     * Check if block is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Check if block is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && is_null($this->deleted_at);
    }

    /**
     * Archive the block (soft delete with archived status).
     */
    public function archive(): bool
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->deleted_by = auth()->id();
        $this->save();
        return $this->delete(); // Soft delete
    }

    /**
     * Get the full address of the block.
     */
    public function getFullAddressAttribute()
    {
        $address = $this->address1;
        
        if ($this->address2) {
            $address .= ', ' . $this->address2;
        }
        
        if ($this->address3) {
            $address .= ', ' . $this->address3;
        }
        
        return $address;
    }

    /**
     * Get the image URL for the block.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path && $this->image_name) {
            return asset('storage/' . $this->image_path . '/' . $this->image_name);
        }
        
        return null;
    }

    /**
     * Get the address attribute (maps to address1 for API).
     */
    public function getAddressAttribute()
    {
        return $this->attributes['address1'] ?? null;
    }

    /**
     * Get the block address (maps to address1).
     */
    public function getBlockAddressAttribute()
    {
        return $this->address1;
    }

    /**
     * Set the block address (maps to address1).
     */
    public function setBlockAddressAttribute($value)
    {
        $this->attributes['address1'] = $value;
    }

    /**
     * Get the management company address (maps to address2).
     */
    public function getManagementCompanyAddressAttribute()
    {
        return $this->address2;
    }

    /**
     * Set the management company address (maps to address2).
     */
    public function setManagementCompanyAddressAttribute($value)
    {
        $this->attributes['address2'] = $value;
    }


    /**
     * Get the block information types available
     */
    public function getBlockInformationTypesAttribute()
    {
        return \App\Models\BlockInformationType::ordered()->get();
    }

    /**
     * Get the block inspections for this block
     */
    public function blockInspections()
    {
        return $this->hasMany(BlockInspection::class);
    }

    /**
     * Get the block work orders for this block
     */
    public function blockWorkOrders()
    {
        return $this->hasMany(BlockWorkOrder::class);
    }

    /**
     * Get the images for the block.
     */
    public function images()
    {
        return $this->hasMany(BlockImage::class)->ordered();
    }

    /**
     * Get the primary image for the block.
     */
    public function primaryImage()
    {
        return $this->hasOne(BlockImage::class)->where('is_primary', true);
    }
}

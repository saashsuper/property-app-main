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
    protected $fillable = [
        'name',
        'management_company',
        'block_type_id',
        'user_id',
        'address1',
        'address2',
        'address3',
        'image_path',
        'image_name',
        'country_id',
        'state_id',
        'car_spaces',
        'inspection_count',
        'no_of_units',
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
        return $this->belongsTo(User::class);
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
     * Get the creator of the block.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the block.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the deleter of the block.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope a query to only include active blocks.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
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
}

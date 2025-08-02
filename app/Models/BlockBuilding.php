<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockBuilding extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'name',
        'building_type_id',
        'floor_no',
        'roof_type',
        'no_lift',
        'image_path',
        'image_name',
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
        'floor_no' => 'integer',
        'no_lift' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Get the block that owns the building.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the building type that owns the building.
     */
    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class);
    }

    /**
     * Get the units for the building.
     */
    public function units()
    {
        return $this->hasMany(BlockUnit::class, 'block_building_id');
    }
}

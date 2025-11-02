<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockInspectionAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'block_inspection_id',
        'block_building_id',
        'building_asset_id',
        'block_general_asset_id',
        'block_inspection_value_id',
        'comments',
        'additional_comments',
    ];

    protected $casts = [
        'block_inspection_id' => 'integer',
        'block_building_id' => 'integer',
        'building_asset_id' => 'integer',
        'block_general_asset_id' => 'integer',
        'block_inspection_value_id' => 'integer',
    ];

    /**
     * Get the block inspection that owns the asset.
     */
    public function blockInspection()
    {
        return $this->belongsTo(BlockInspection::class);
    }

    /**
     * Get the block building.
     */
    public function blockBuilding()
    {
        return $this->belongsTo(BlockBuilding::class);
    }

    /**
     * Get the building asset.
     */
    public function buildingAsset()
    {
        return $this->belongsTo(BuildingAsset::class);
    }

    /**
     * Get the inspection value.
     */
    public function inspectionValue()
    {
        return $this->belongsTo(BlockInspectionValue::class, 'block_inspection_value_id');
    }

    /**
     * Get the images for the inspection asset.
     */
    public function images()
    {
        return $this->hasMany(BlockInspectionAssetImage::class);
    }

    /**
     * Get the general asset (if this is a general asset inspection).
     */
    public function generalAsset()
    {
        return $this->belongsTo(BlockGeneralAsset::class, 'block_general_asset_id');
    }
}

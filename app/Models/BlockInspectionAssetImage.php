<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockInspectionAssetImage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_inspection_asset_id',
        'block_inspection_id',
        'block_building_id',
        'building_asset_id',
        'image_path',
        'image_name',
        's3_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'block_inspection_asset_id' => 'integer',
        'block_inspection_id' => 'integer',
        'block_building_id' => 'integer',
        'building_asset_id' => 'integer',
        's3_status' => 'integer',
    ];

    /**
     * Get the block inspection asset that owns the image.
     */
    public function blockInspectionAsset()
    {
        return $this->belongsTo(BlockInspectionAsset::class);
    }

    /**
     * Get the block inspection that owns the image.
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
}


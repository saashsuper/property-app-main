<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockBuildingTypeAsset extends Model
{
    use HasFactory;

    protected $table = 'block_building_type_assets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_building_type_id',
        'block_building_asset_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'block_building_type_id' => 'integer',
        'block_building_asset_id' => 'integer',
    ];

    /**
     * Get the building type.
     */
    public function buildingType()
    {
        return $this->belongsTo(BlockBuildingType::class, 'block_building_type_id');
    }

    /**
     * Get the building asset.
     */
    public function buildingAsset()
    {
        return $this->belongsTo(BlockBuildingAsset::class, 'block_building_asset_id');
    }
}

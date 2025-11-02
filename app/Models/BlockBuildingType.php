<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockBuildingType extends Model
{
    use HasFactory;

    protected $table = 'block_building_types';
    protected $fillable = ['name'];
    
    /**
     * Get the buildings of this type.
     */
    public function buildings()
    {
        return $this->hasMany(BlockBuilding::class, 'building_type_id');
    }
    
    /**
     * Get the building type assets (pivot records).
     */
    public function buildingTypeAssets()
    {
        return $this->hasMany(BlockBuildingTypeAsset::class, 'block_building_type_id');
    }
    
    /**
     * Get the building assets associated with this type (many-to-many).
     */
    public function buildingAssets()
    {
        return $this->belongsToMany(
            BlockBuildingAsset::class, 
            'block_building_type_assets',
            'block_building_type_id',
            'block_building_asset_id'
        )->withTimestamps();
    }
}

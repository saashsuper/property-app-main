<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockBuildingAsset extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'integer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'block_inspection_value_type_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'block_inspection_value_type_id' => 'integer',
    ];

    /**
     * Get the value type.
     */
    public function valueType()
    {
        return $this->belongsTo(BlockInspectionValueType::class, 'block_inspection_value_type_id');
    }
    
    /**
     * Get the inspection values for this asset type.
     */
    public function inspectionValues()
    {
        return $this->hasMany(BlockInspectionValue::class, 'block_inspection_value_type_id', 'block_inspection_value_type_id');
    }
    
    /**
     * Get the building type assets (pivot records).
     */
    public function buildingTypeAssets()
    {
        return $this->hasMany(BlockBuildingTypeAsset::class, 'block_building_asset_id');
    }
    
    /**
     * Get the building types that use this asset (many-to-many).
     */
    public function buildingTypes()
    {
        return $this->belongsToMany(
            BlockBuildingType::class, 
            'block_building_type_assets',
            'block_building_asset_id',
            'block_building_type_id'
        )->withTimestamps();
    }
}

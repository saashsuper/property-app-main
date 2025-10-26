<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingTypeAsset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'building_type_id',
        'building_asset_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'building_type_id' => 'integer',
        'building_asset_id' => 'integer',
    ];

    /**
     * Get the building type.
     */
    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class);
    }

    /**
     * Get the building asset.
     */
    public function buildingAsset()
    {
        return $this->belongsTo(BuildingAsset::class);
    }
}


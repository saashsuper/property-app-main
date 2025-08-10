<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingAsset extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'id',
        'name',
        'block_inspection_value_type_id',
    ];

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
     * Get the inspection assets.
     */
    public function inspectionAssets()
    {
        return $this->hasMany(BlockInspectionAsset::class, 'building_asset_id');
    }
}
